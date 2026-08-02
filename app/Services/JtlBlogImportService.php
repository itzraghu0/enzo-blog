<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JtlBlogImportService
{
    private string $source;
    private ?string $target;
    private int $chunk;
    private int $defaultUserID;
    private bool $dryRun;
    private bool $preserveIDs;
    private string $sourceRoot;
    private string $mediaRoot;
    private string $publicPrefix;
    private string $postModel;
    private string $categoryModel;

    /** @var array<int, int> */
    private array $postMap = [];

    /** @var array<int, int> */
    private array $categoryMap = [];

    /** @var array<int, int> */
    private array $commentMap = [];

    /** @var array<int, string> */
    private array $logs = [];

    public function __construct(private readonly MediaService $mediaService)
    {
    }

    /** @return array{success: bool, logs: array<int, string>} */
    public function import(array $options = []): array
    {
        $this->source = (string) ($options['source'] ?? 'jtl_temp');
        $this->target = ($options['target'] ?? '') !== '' ? (string) $options['target'] : null;
        $this->chunk = max(50, (int) ($options['chunk'] ?? 500));
        $this->defaultUserID = (int) ($options['default_user_id'] ?? 1);
        $this->dryRun = (bool) ($options['dry_run'] ?? false);
        $this->preserveIDs = (bool) ($options['preserve_ids'] ?? false);
        $sourceRoot = rtrim((string) ($options['source_root'] ?? ''), "\\/");
        $this->sourceRoot = $sourceRoot !== '' ? $sourceRoot : base_path();
        $mediaRoot = trim((string) ($options['media_root'] ?? ''), "\\/");
        $this->mediaRoot = $mediaRoot !== '' ? $mediaRoot : trim(config('blog.media_directory', 'media/blog'), "\\/");
        $this->publicPrefix = rtrim((string) ($options['public_prefix'] ?? '/'), '/') . '/';
        $this->postModel = (string) ($options['post_model'] ?? 'App\\Models\\Post');
        $this->categoryModel = (string) ($options['category_model'] ?? 'App\\Models\\Category');
        $this->logs = [];
        $this->postMap = [];
        $this->categoryMap = [];
        $this->commentMap = [];

        $this->configureRuntime();

        $this->configureSourceConnection($options['database'] ?? 'cupssy', $options['username'] ?? 'root', $options['password'] ?? 'root');

        try {
            $this->validateSource();
            $this->validateTarget();
            $this->showCounts();
            $this->truncateTarget();
            $this->ensureDefaultUser();
            $this->importAuthors();
            $this->importCategories();
            $this->importCategoryTranslations();
            $this->importPosts();
            $this->importPostCategories();
            $this->importPostPreviewMedia();
            $this->importComments();
            $this->repairAutoIncrements();
            $this->info('JTL blog import finished.');

            return ['success' => true, 'logs' => $this->logs];
        } catch (\Throwable $exception) {
            $this->warn($exception->getMessage());

            return ['success' => false, 'logs' => $this->logs];
        }
    }

    private function sourceDB()
    {
        return DB::connection($this->source);
    }

    private function targetDB()
    {
        return $this->target === null ? DB::connection() : DB::connection($this->target);
    }

    private function configureSourceConnection(string $database, string $username, string $password): void
    {
        config([
            'database.connections.jtl_temp' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => $database,
                'username' => $username,
                'password' => $password,
                'unix_socket' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,
            ],
        ]);

        DB::purge('jtl_temp');
    }

    private function configureRuntime(): void
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        @ini_set('max_execution_time', '0');
    }

    private function info(string $message): void
    {
        $this->logs[] = '[info] '.$message;
    }

    private function warn(string $message): void
    {
        $this->logs[] = '[warning] '.$message;
    }

    /**
     * @param array<int, string> $headers
     * @param array<int, array<int, mixed>> $rows
     */
    private function table(array $headers, array $rows): void
    {
        $this->logs[] = '[table] '.implode(' | ', $headers);

        foreach ($rows as $row) {
            $this->logs[] = '[table] '.implode(' | ', array_map(static fn (mixed $value): string => (string) $value, $row));
        }
    }

    private function validateSource(): void
    {
        foreach (['tnews', 'tnewssprache', 'tseo', 'tnewskategorie', 'tnewskategoriesprache', 'tnewskategorienews'] as $table) {
            if (!$this->sourceDB()->getSchemaBuilder()->hasTable($table)) {
                throw new \RuntimeException("Source table {$table} does not exist on connection {$this->source}.");
            }
        }

        $this->validateSourceColumns('tnews', ['kNews', 'nAktiv', 'dErstellt', 'dGueltigVon', 'cPreviewImage']);
        $this->validateSourceColumns('tnewssprache', ['id', 'kNews', 'languageID', 'languageCode', 'title', 'content', 'preview', 'metaTitle', 'metaDescription']);
        $this->validateSourceColumns('tnewskategorie', ['kNewsKategorie', 'kParent', 'nAktiv', 'nSort', 'dLetzteAktualisierung']);
        $this->validateSourceColumns('tnewskategoriesprache', ['id', 'kNewsKategorie', 'languageID', 'languageCode', 'name', 'description', 'metaTitle', 'metaDescription']);

        $blankPostTitles = $this->sourceDB()->table('tnewssprache')
            ->where(static function ($query): void {
                $query->whereNull('title')->orWhere('title', '');
            })
            ->count();
        $blankPostContent = $this->sourceDB()->table('tnewssprache')
            ->where(static function ($query): void {
                $query->whereNull('content')->orWhere('content', '');
            })
            ->count();

        if ($blankPostTitles > 0 || $blankPostContent > 0) {
            $this->warn("JTL post translations with blank title: {$blankPostTitles}; blank content: {$blankPostContent}.");
        }
    }

    private function validateTarget(): void
    {
        foreach (['posts', 'post_translations', 'categories', 'category_translations', 'post_category', 'media', 'comments', 'users'] as $table) {
            if (!$this->targetDB()->getSchemaBuilder()->hasTable($table)) {
                $connection = $this->target ?? config('database.default');
                throw new \RuntimeException("Target table {$table} does not exist on connection {$connection}.");
            }
        }

        $this->validateTargetColumns('posts', ['id', 'user_id', 'status', 'published_at', 'created_at', 'updated_at', 'deleted_at']);
        $this->validateTargetColumns('post_translations', ['post_id', 'locale', 'title', 'slug', 'excerpt', 'content', 'seo_title', 'meta_description', 'og_title', 'og_description', 'canonical_url', 'preview_image_alt']);
        $this->validateTargetColumns('categories', ['id', 'parent_id', 'status', 'sort_order', 'created_at', 'updated_at', 'deleted_at']);
        $this->validateTargetColumns('category_translations', ['category_id', 'locale', 'name', 'slug', 'description', 'seo_title', 'meta_description']);
    }

    /**
     * @param array<int, string> $columns
     */
    private function validateSourceColumns(string $table, array $columns): void
    {
        $schema = $this->sourceDB()->getSchemaBuilder();

        foreach ($columns as $column) {
            if (!$schema->hasColumn($table, $column)) {
                throw new \RuntimeException("Source column {$table}.{$column} does not exist on connection {$this->source}.");
            }
        }
    }

    /**
     * @param array<int, string> $columns
     */
    private function validateTargetColumns(string $table, array $columns): void
    {
        $schema = $this->targetDB()->getSchemaBuilder();
        $connection = $this->target ?? config('database.default');

        foreach ($columns as $column) {
            if (!$schema->hasColumn($table, $column)) {
                throw new \RuntimeException("Target column {$table}.{$column} does not exist on connection {$connection}.");
            }
        }
    }

    private function showCounts(): void
    {
        $rows = [];
        foreach (['tnews', 'tnewssprache', 'tnewskategorie', 'tnewskategoriesprache', 'tnewskategorienews', 'tnewskommentar'] as $table) {
            if ($this->sourceDB()->getSchemaBuilder()->hasTable($table)) {
                $rows[] = [$table, $this->sourceDB()->table($table)->count()];
            }
        }

        $this->table(['JTL table', 'Rows'], $rows);
    }

    private function ensureDefaultUser(): void
    {
        if ($this->targetDB()->table('users')->where('id', $this->defaultUserID)->exists()) {
            return;
        }

        $this->targetDB()->table('users')->insert([
            'id' => $this->defaultUserID,
            'name' => 'JTL Import User',
            'email' => 'jtl-import-user@import.local',
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(40)),
            'role' => 3,
            'status' => 1,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function truncateTarget(): void
    {
        if ($this->dryRun) {
            $this->warn('Dry run enabled; target blog tables will not be truncated.');

            return;
        }

        $this->warn('Truncating target blog tables.');
        $db = $this->targetDB();
        $db->statement('SET FOREIGN_KEY_CHECKS=0');
        File::deleteDirectory(public_path($this->mediaRoot));

        foreach (
            [
                'post_media',
                'media',
                'comments',
                'post_category',
                'post_translations',
                'posts',
                'category_translations',
                'categories',
            ] as $table
        ) {
            if ($db->getSchemaBuilder()->hasTable($table)) {
                $db->table($table)->truncate();
            }
        }

        $db->statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function importAuthors(): void
    {
        if (!$this->sourceDB()->getSchemaBuilder()->hasTable('tadminlogin')) {
            return;
        }

        $this->info('Importing JTL authors as Laravel users...');

        $this->sourceDB()->table('tadminlogin')
            ->orderBy('kAdminlogin')
            ->chunk($this->chunk, function ($authors): void {
                foreach ($authors as $author) {
                    $email = $this->safeEmail($author->cMail ?? null, 'jtl-author-' . (int)$author->kAdminlogin . '@import.local');
                    $name = $this->trimTo((string)($author->cName ?: $author->cLogin ?: 'JTL Author ' . (int)$author->kAdminlogin), 255);

                    if ($this->dryRun) {
                        continue;
                    }

                    $this->upsertUser($email, $name, 3, (int)($author->bAktiv ?? 1));
                }
            });
    }

    private function importCategories(): void
    {
        $this->info('Importing categories...');

        $this->sourceDB()->table('tnewskategorie')
            ->orderBy('kNewsKategorie')
            ->chunk($this->chunk, function ($categories): void {
                foreach ($categories as $category) {
                    $sourceID = (int)$category->kNewsKategorie;
                    $targetID = $this->preserveIDs ? $sourceID : null;
                    $parentID = (int)$category->kParent > 0
                        ? ($this->preserveIDs ? (int)$category->kParent : ($this->categoryMap[(int)$category->kParent] ?? null))
                        : null;

                    $data = [
                        'parent_id' => $parentID,
                        'status' => (int)$category->nAktiv === 1 ? 'active' : 'inactive',
                        'sort_order' => (int)$category->nSort,
                        'created_at' => $this->dateOrNow($category->dLetzteAktualisierung ?? null),
                        'updated_at' => $this->dateOrNow($category->dLetzteAktualisierung ?? null),
                        'deleted_at' => null,
                    ];

                    if ($this->dryRun) {
                        $this->categoryMap[$sourceID] = $targetID ?? $sourceID;
                        continue;
                    }

                    if ($targetID !== null) {
                        $this->targetDB()->table('categories')->updateOrInsert(['id' => $targetID], $data);
                    } else {
                        $targetID = $this->targetDB()->table('categories')->insertGetId($data);
                    }

                    $this->categoryMap[$sourceID] = (int)$targetID;

                    if (!empty($category->cPreviewImage)) {
                        $this->copyMedia($category->cPreviewImage, 'category-preview', $this->categoryModel, (int)$targetID);
                    }
                }
            });
    }

    private function importCategoryTranslations(): void
    {
        $this->info('Importing category translations...');
        $imported = 0;

        $this->sourceDB()->table('tnewskategoriesprache as cts')
            ->leftJoin('tseo as seo', function ($join): void {
                $join->on('seo.kKey', '=', 'cts.kNewsKategorie')
                    ->on('seo.kSprache', '=', 'cts.languageID')
                    ->where('seo.cKey', '=', 'kNewsKategorie');
            })
            ->select('cts.*', 'seo.cSeo')
            ->orderBy('cts.id')
            ->chunk($this->chunk, function ($rows) use (&$imported): void {
                foreach ($rows as $row) {
                    $categoryID = $this->categoryMap[(int)$row->kNewsKategorie] ?? null;
                    if ($categoryID === null) {
                        $this->warn("Skipping category translation for missing category {$row->kNewsKategorie}.");
                        continue;
                    }

                    $locale = $this->locale((int)$row->languageID);
                    $slug = $this->uniqueSlug('category_translations', 'category_id', $categoryID, $locale, $row->cSeo ?: $row->name ?: 'category-' . $categoryID);
                    $name = $this->trimTo($row->name ?: 'Category ' . $categoryID, 191);

                    $data = [
                        'category_id' => $categoryID,
                        'locale' => $locale,
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $row->description,
                        'seo_title' => $row->metaTitle ?: $name,
                        'meta_description' => $row->metaDescription ?: null,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];

                    if (!$this->dryRun) {
                        $this->targetDB()->table('category_translations')->updateOrInsert(
                            ['category_id' => $categoryID, 'locale' => $locale],
                            $data
                        );
                    }

                    $imported++;
                }
            });

        $this->info("Imported category translations: {$imported}.");
    }

    private function importPosts(): void
    {
        $this->info('Importing posts and post translations...');
        $imported = 0;
        $translationsImported = 0;

        $this->sourceDB()->table('tnews as n')
            ->leftJoin('tcontentauthor as ca', function ($join): void {
                $join->on('ca.kContentId', '=', 'n.kNews')->where('ca.cRealm', '=', 'NEWS');
            })
            ->leftJoin('tadminlogin as a', 'a.kAdminlogin', '=', 'ca.kAdminlogin')
            ->select('n.*', 'a.cMail as author_email', 'a.cName as author_name')
            ->orderBy('n.kNews')
            ->chunk($this->chunk, function ($posts) use (&$imported, &$translationsImported): void {
                $translationsByPost = $this->postTranslationsForSourceIDs(
                    $posts->pluck('kNews')
                        ->map(static fn (mixed $sourceID): int => (int) $sourceID)
                        ->all()
                );

                foreach ($posts as $post) {
                    $sourceID = (int)$post->kNews;
                    $targetID = $this->preserveIDs ? $sourceID : null;
                    $userID = $this->resolveAuthorUserID($post->author_email ?? null, $post->author_name ?? null);

                    $data = [
                        'user_id' => $userID,
                        'status' => (int)$post->nAktiv === 1 ? 'published' : 'draft',
                        'is_featured' => 0,
                        'published_at' => $this->nullableDate($post->dGueltigVon ?? null),
                        'country_code' => null,
                        'region' => null,
                        'city' => null,
                        'latitude' => null,
                        'longitude' => null,
                        'timezone' => null,
                        'created_at' => $this->dateOrNow($post->dErstellt ?? null),
                        'updated_at' => $this->dateOrNow($post->dErstellt ?? null),
                        'deleted_at' => null,
                    ];

                    if ($this->dryRun) {
                        $targetID = $targetID ?? $sourceID;
                    } elseif ($targetID !== null) {
                        $this->targetDB()->table('posts')->updateOrInsert(['id' => $targetID], $data);
                    } else {
                        $targetID = $this->targetDB()->table('posts')->insertGetId($data);
                    }

                    $this->postMap[$sourceID] = (int)$targetID;
                    $imported++;

                    foreach ($translationsByPost[$sourceID] ?? [] as $translation) {
                        if ($this->importPostTranslation((int)$targetID, $translation)) {
                            $translationsImported++;
                        }
                    }
                }
            });

        $this->info("Imported posts: {$imported}; post translations: {$translationsImported}.");

        if (!$this->dryRun && $imported > 0 && $translationsImported === 0) {
            throw new \RuntimeException('No post translations were imported. Check tnewssprache.kNews values against imported tnews.kNews values.');
        }
    }

    private function importPostPreviewMedia(): void
    {
        $this->info('Importing post preview media...');
        $imported = 0;
        $missing = 0;
        $failed = 0;

        $this->sourceDB()->table('tnews')
            ->select(['kNews', 'cPreviewImage'])
            ->where('cPreviewImage', '<>', '')
            ->orderBy('kNews')
            ->chunk($this->chunk, function ($posts) use (&$imported, &$missing, &$failed): void {
                foreach ($posts as $post) {
                    $sourceID = (int) $post->kNews;
                    $targetID = $this->postMap[$sourceID] ?? null;

                    if ($targetID === null) {
                        continue;
                    }

                    $result = $this->importPostPreviewMediaForPost($sourceID, $targetID, $post->cPreviewImage ?? null);

                    if ($result === 'imported') {
                        $imported++;
                    } elseif ($result === 'missing') {
                        $missing++;
                    } elseif ($result === 'failed') {
                        $failed++;
                    }
                }
            });

        $this->info("Imported post preview media: {$imported}; missing: {$missing}; failed: {$failed}.");
    }

    private function importPostPreviewMediaForPost(int $sourceID, int $targetID, ?string $jtlPreviewPath): string
    {
        if ($this->dryRun || $this->sourceRoot === '') {
            return 'skipped';
        }

        $jtlPreviewPath = trim((string) $jtlPreviewPath);
        if ($jtlPreviewPath === '') {
            return 'skipped';
        }

        $sourcePath = $this->resolveSourcePath($jtlPreviewPath);
        if ($sourcePath === null || !File::isFile($sourcePath)) {
            $this->warn("Missing preview image for JTL post {$sourceID}: {$jtlPreviewPath}");

            return 'missing';
        }

        $post = Post::query()->find($targetID);
        if ($post === null) {
            $this->warn("Skipping preview image for JTL post {$sourceID}; missing target post {$targetID}.");
            return 'missing';
        }

        $user = User::query()->find($this->defaultUserID);
        $file = new UploadedFile(
            $sourcePath,
            basename($sourcePath),
            File::mimeType($sourcePath) ?: null,
            null,
            true
        );

        try {
            $media = $this->mediaService->store($file, $user, [
                'collection' => 'preview',
            ]);

            $media = $this->mediaService->attach($media, $post, 'preview');
        } catch (\Throwable $exception) {
            $this->warn("Failed preview image for JTL post {$sourceID}: {$exception->getMessage()}");

            return 'failed';
        }

        if ($this->targetDB()->getSchemaBuilder()->hasTable('post_media')) {
            $this->targetDB()->table('post_media')->updateOrInsert(
                [
                    'post_id' => $targetID,
                    'media_id' => $media->getKey(),
                    'collection' => 'preview',
                ],
                [
                    'locale' => null,
                    'purpose' => 'preview_image',
                    'sort_order' => (int) $media->sort_order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return 'imported';
    }

    /**
     * @param array<int, int> $sourceIDs
     * @return array<int, array<int, object>>
     */
    private function postTranslationsForSourceIDs(array $sourceIDs): array
    {
        $sourceIDs = array_values(array_unique(array_filter($sourceIDs)));

        if ($sourceIDs === []) {
            return [];
        }

        return $this->sourceDB()->table('tnewssprache as nts')
            ->leftJoin('tseo as seo', function ($join): void {
                $join->on('seo.kKey', '=', 'nts.kNews')
                    ->on('seo.kSprache', '=', 'nts.languageID')
                    ->where('seo.cKey', '=', 'kNews');
            })
            ->select('nts.*', 'seo.cSeo')
            ->whereIn('nts.kNews', $sourceIDs)
            ->orderBy('nts.id')
            ->get()
            ->groupBy(static fn (object $row): int => (int) $row->kNews)
            ->map(static fn ($rows): array => $rows->all())
            ->all();
    }

    private function importPostTranslation(int $postID, object $row): bool
    {
        $locale = $this->locale((int)$row->languageID);
        $title = $this->trimTo($row->title ?: 'JTL Blog ' . $postID, 191);
        $slug = $this->uniqueSlug('post_translations', 'post_id', $postID, $locale, $row->cSeo ?: $title);
        $content = $this->rewriteContentImages((string)($row->content ?? ''));
        $excerpt = $this->rewriteContentImages((string)($row->preview ?? ''));

        $data = [
            'post_id' => $postID,
            'locale' => $locale,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt !== '' ? $excerpt : null,
            'content' => $content !== '' ? $content : null,
            'seo_title' => $row->metaTitle ?: $title,
            'meta_description' => $row->metaDescription ?: null,
            'og_title' => $row->metaTitle ?: $title,
            'og_description' => $row->metaDescription ?: null,
            'canonical_url' => null,
            'preview_image_alt' => $title,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if (!$this->dryRun) {
            $this->targetDB()->table('post_translations')->updateOrInsert(
                ['post_id' => $postID, 'locale' => $locale],
                $data
            );
        }

        return true;
    }

    private function importPostCategories(): void
    {
        $this->info('Importing post-category links...');

        $this->sourceDB()->table('tnewskategorienews')
            ->orderBy('kNewsKategorieNews')
            ->chunk($this->chunk, function ($rows): void {
                foreach ($rows as $row) {
                    $postID = $this->postMap[(int)$row->kNews] ?? null;
                    $categoryID = $this->categoryMap[(int)$row->kNewsKategorie] ?? null;
                    if ($postID === null || $categoryID === null) {
                        continue;
                    }

                    if (!$this->dryRun) {
                        $this->targetDB()->table('post_category')->updateOrInsert(
                            ['post_id' => $postID, 'category_id' => $categoryID],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            });
    }

    private function importComments(): void
    {
        if (!$this->sourceDB()->getSchemaBuilder()->hasTable('tnewskommentar')) {
            return;
        }

        $this->info('Importing comments...');

        if ($this->sourceDB()->table('tnewskommentar')->count() === 0) {
            $this->info('No JTL comments found.');

            return;
        }

        $query = $this->sourceDB()->table('tnewskommentar as c')
            ->orderBy('c.parentCommentID')
            ->orderBy('c.kNewsKommentar');

        if ($this->sourceDB()->getSchemaBuilder()->hasTable('tkunde')) {
            $query
                ->leftJoin('tkunde as k', 'k.kKunde', '=', 'c.kKunde')
                ->select('c.*', 'k.cMail as customer_email', 'k.cVorname', 'k.cNachname');
        } else {
            $query->select('c.*');
        }

        $query
            ->chunk($this->chunk, function ($comments): void {
                foreach ($comments as $comment) {
                    $sourceID = (int)$comment->kNewsKommentar;
                    $postID = $this->postMap[(int)$comment->kNews] ?? null;
                    if ($postID === null) {
                        continue;
                    }

                    $targetID = $this->preserveIDs ? $sourceID : null;
                    $parentID = (int)$comment->parentCommentID > 0
                        ? ($this->preserveIDs ? (int)$comment->parentCommentID : ($this->commentMap[(int)$comment->parentCommentID] ?? null))
                        : null;
                    $userID = $this->resolveCommentUserID($comment);

                    $data = [
                        'post_id' => $postID,
                        'user_id' => $userID,
                        'parent_id' => $parentID,
                        'content' => (string)$comment->cKommentar,
                        'created_at' => $this->dateOrNow($comment->dErstellt ?? null),
                        'updated_at' => $this->dateOrNow($comment->dErstellt ?? null),
                    ];

                    if ($this->dryRun) {
                        $this->commentMap[$sourceID] = $targetID ?? $sourceID;
                        continue;
                    }

                    if ($targetID !== null) {
                        $this->targetDB()->table('comments')->updateOrInsert(['id' => $targetID], $data);
                    } else {
                        $targetID = $this->targetDB()->table('comments')->insertGetId($data);
                    }

                    $this->commentMap[$sourceID] = (int)$targetID;
                }
            });
    }

    private function resolveAuthorUserID(?string $email, ?string $name): int
    {
        $email = $this->safeEmail($email, null);
        if ($email === null) {
            return $this->defaultUserID;
        }

        if ($this->dryRun) {
            return $this->defaultUserID;
        }

        return $this->upsertUser($email, $name ?: $email, 3, 1);
    }

    private function resolveCommentUserID(object $comment): int
    {
        $email = $this->safeEmail($comment->customer_email ?? $comment->cEmail ?? null, null);
        if ($email === null) {
            $email = 'jtl-comment-' . (int)$comment->kNewsKommentar . '@import.local';
        }

        $name = trim(($comment->cVorname ?? '') . ' ' . ($comment->cNachname ?? ''));
        if ($name === '') {
            $name = (string)($comment->cName ?: $email);
        }

        if ($this->dryRun) {
            return $this->defaultUserID;
        }

        return $this->upsertUser($email, $name, 4, 1);
    }

    private function upsertUser(string $email, string $name, int $role, int $status): int
    {
        $existing = $this->targetDB()->table('users')->where('email', $email)->first();
        $data = [
            'name' => $this->trimTo($name, 255),
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make(Str::random(40)),
            'role' => $role,
            'status' => $status,
            'updated_at' => now(),
        ];

        if ($existing !== null) {
            $this->targetDB()->table('users')->where('id', $existing->id)->update([
                'name' => $data['name'],
                'status' => $data['status'],
                'updated_at' => now(),
            ]);

            return (int)$existing->id;
        }

        $data['created_at'] = now();
        $data['remember_token'] = Str::random(10);

        return (int)$this->targetDB()->table('users')->insertGetId($data);
    }

    private function copyMedia(string $jtlPath, string $collection, string $mediableType, int $mediableID): ?string
    {
        if ($this->dryRun || $this->sourceRoot === '') {
            return null;
        }

        $sourcePath = $this->resolveSourcePath($jtlPath);
        if ($sourcePath === null || !File::exists($sourcePath)) {
            $this->warn("Missing media file: {$jtlPath}");
            return null;
        }

        $filename = basename($sourcePath);
        $targetRelative = $this->mediaRoot . '/' . $collection . '/' . $mediableID . '/' . $filename;
        $targetPath = public_path($targetRelative);

        File::ensureDirectoryExists(dirname($targetPath));
        File::copy($sourcePath, $targetPath);

        $this->targetDB()->table('media')->updateOrInsert(
            [
                'collection' => $collection,
                'mediable_type' => $mediableType,
                'mediable_id' => $mediableID,
                'filename' => $filename,
            ],
            [
                'user_id' => $this->defaultUserID,
                'disk' => 'public',
                'path' => str_replace('\\', '/', $targetRelative),
                'original_name' => $filename,
                'mime_type' => File::mimeType($sourcePath) ?: 'application/octet-stream',
                'size' => File::size($sourcePath),
                'alt_text' => null,
                'title' => null,
                'caption' => null,
                'locale' => null,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]
        );

        return str_replace('\\', '/', $targetRelative);
    }

    private function rewriteContentImages(string $html): string
    {
        if ($html === '' || $this->sourceRoot === '') {
            return $html;
        }

        return preg_replace_callback('/(<img\b[^>]*\bsrc=["\'])([^"\']+)(["\'][^>]*>)/i', function (array $matches): string {
            $src = html_entity_decode($matches[2]);
            $relative = $this->extractJtlRelativePath($src);

            if ($relative === null) {
                return $matches[0];
            }

            $copied = $this->copyContentImage($relative);
            if ($copied === null) {
                return $matches[0];
            }

            return $matches[1] . $this->publicPrefix . $copied . $matches[3];
        }, $html) ?? $html;
    }

    private function copyContentImage(string $relative): ?string
    {
        if ($this->dryRun) {
            return null;
        }

        $sourcePath = $this->resolveSourcePath($relative);
        if ($sourcePath === null || !File::exists($sourcePath)) {
            $this->warn("Missing content image: {$relative}");
            return null;
        }

        $targetRelative = $this->mediaRoot . '/content/' . ltrim(str_replace('\\', '/', $relative), '/');
        $targetPath = public_path($targetRelative);
        File::ensureDirectoryExists(dirname($targetPath));
        File::copy($sourcePath, $targetPath);

        return str_replace('\\', '/', $targetRelative);
    }

    private function resolveSourcePath(string $path): ?string
    {
        $relative = $this->extractJtlRelativePath($path) ?? $path;
        $relative = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative), DIRECTORY_SEPARATOR);

        if ($this->sourceRoot === '') {
            return null;
        }

        return $this->sourceRoot . DIRECTORY_SEPARATOR . $relative;
    }

    private function extractJtlRelativePath(string $src): ?string
    {
        $src = str_replace('\\', '/', $src);
        $path = parse_url($src, PHP_URL_PATH) ?: $src;
        $path = ltrim($path, '/');

        foreach (['bilder/news/', 'bilder/newskategorie/', 'media/image/news/', 'media/image/newscategory/'] as $needle) {
            $pos = stripos($path, $needle);
            if ($pos !== false) {
                return substr($path, $pos);
            }
        }

        return null;
    }

    private function uniqueSlug(string $table, string $ownerColumn, int $ownerID, string $locale, string $raw): string
    {
        $base = Str::slug($raw);
        if ($base === '') {
            $base = 'jtl-' . $ownerID;
        }

        $base = $this->trimSlug($base);
        $slug = $base;
        $i = 2;

        while ($this->targetDB()->table($table)
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->where($ownerColumn, '<>', $ownerID)
            ->exists()
        ) {
            $suffix = '-' . $i;
            $slug = $this->trimSlug($base, strlen($suffix)) . $suffix;
            $i++;
        }

        return $slug;
    }

    private function trimSlug(string $slug, int $reserved = 0): string
    {
        return rtrim(Str::limit($slug, 191 - $reserved, ''), '-');
    }

    private function locale(int $languageID): string
    {
        return $languageID === 2 ? 'en' : 'de';
    }

    private function safeEmail(?string $email, ?string $fallback): ?string
    {
        $email = trim((string)$email);
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->trimTo(strtolower($email), 191);
        }

        return $fallback;
    }

    private function trimTo(string $value, int $length): string
    {
        return mb_substr($value, 0, $length);
    }

    private function nullableDate(?string $value): ?string
    {
        if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        return $value;
    }

    private function dateOrNow(?string $value): string
    {
        return $this->nullableDate($value) ?? now()->toDateTimeString();
    }

    private function repairAutoIncrements(): void
    {
        if ($this->dryRun || !$this->preserveIDs) {
            return;
        }

        foreach (['posts', 'categories', 'comments'] as $table) {
            $max = (int)$this->targetDB()->table($table)->max('id');
            $this->targetDB()->statement('ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . ($max + 1));
        }
    }
}


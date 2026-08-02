<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostTranslation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $staff = $this->seedStaff();
            $members = $this->seedMembers();
            $categories = $this->seedCategories();
            $posts = $this->seedPosts($staff, $categories);

            $this->seedComments($posts, $members, $staff);
            $this->clearBlogCaches();
        });
    }

    /**
     * @return array<int, User>
     */
    private function seedStaff(): array
    {
        $staffData = [
            ['name' => 'Admin User', 'email' => 'admin@admin.com', 'role' => User::ROLE_ADMIN],
            ['name' => 'Editor Greta Weber', 'email' => 'editor1@example.com', 'role' => User::ROLE_EDITOR],
            ['name' => 'Editor Max Klein', 'email' => 'editor2@example.com', 'role' => User::ROLE_EDITOR],
            ['name' => 'Author Lena Hoffmann', 'email' => 'author1@example.com', 'role' => User::ROLE_AUTHOR],
            ['name' => 'Author Felix Braun', 'email' => 'author2@example.com', 'role' => User::ROLE_AUTHOR],
        ];

        return array_map(
            fn (array $data): User => $this->upsertUser($data + ['password' => 'password', 'verified' => true]),
            $staffData
        );
    }

    /**
     * @return array<int, User>
     */
    private function seedMembers(): array
    {
        $members = [];

        for ($index = 1; $index <= 10; $index++) {
            $members[] = $this->upsertUser([
                'name' => sprintf('Member %02d', $index),
                'email' => sprintf('member%02d@example.com', $index),
                'password' => 'password',
                'role' => User::ROLE_VIEWER,
                'verified' => $index <= 9,
            ]);
        }

        return $members;
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $definitions = [
            'nachhaltigkeit' => [
                'sort_order' => 1,
                'translations' => [
                    'de' => ['name' => 'Nachhaltigkeit', 'description' => 'Ideen und Praxis fuer nachhaltige Entscheidungen.'],
                    'en' => ['name' => 'Sustainability', 'description' => 'Ideas and practical guidance for sustainable decisions.'],
                ],
            ],
            'ratgeber' => [
                'sort_order' => 2,
                'translations' => [
                    'de' => ['name' => 'Ratgeber', 'description' => 'Praktische Anleitungen fuer Leser und Kunden.'],
                    'en' => ['name' => 'Guides', 'description' => 'Practical tutorials for readers and customers.'],
                ],
            ],
            'produkte' => [
                'sort_order' => 3,
                'translations' => [
                    'de' => ['name' => 'Produkte', 'description' => 'Produktwissen, Vergleiche und Empfehlungen.'],
                    'en' => ['name' => 'Products', 'description' => 'Product knowledge, comparisons, and recommendations.'],
                ],
            ],
            'unternehmen' => [
                'sort_order' => 4,
                'translations' => [
                    'de' => ['name' => 'Unternehmen', 'description' => 'Neuigkeiten rund um Marke, Team und Service.'],
                    'en' => ['name' => 'Company', 'description' => 'Updates about brand, team, and service.'],
                ],
            ],
            'seo-aeo-geo' => [
                'parent_slug' => 'ratgeber',
                'sort_order' => 5,
                'translations' => [
                    'de' => ['name' => 'SEO AEO GEO', 'description' => 'Optimierung fuer Suche, Antworten und generative Systeme.'],
                    'en' => ['name' => 'SEO AEO GEO', 'description' => 'Optimization for search, answers, and generative engines.'],
                ],
            ],
        ];

        $categories = [];

        foreach ($definitions as $slug => $definition) {
            $categories[$slug] = $this->upsertCategory($slug, $definition);
        }

        foreach ($definitions as $slug => $definition) {
            if (! empty($definition['parent_slug'])) {
                $categories[$slug]->update(['parent_id' => $categories[$definition['parent_slug']]->id]);
            }
        }

        return $categories;
    }

    private function upsertCategory(string $slug, array $definition): Category
    {
        $translation = CategoryTranslation::query()
            ->where('locale', 'de')
            ->where('slug', $slug)
            ->first();

        $category = $translation?->category ?? new Category();
        $category->forceFill([
            'parent_id' => null,
            'status' => 'active',
            'sort_order' => $definition['sort_order'],
        ])->save();

        foreach ($definition['translations'] as $locale => $payload) {
            CategoryTranslation::query()->updateOrCreate(
                ['category_id' => $category->id, 'locale' => $locale],
                [
                    'name' => $payload['name'],
                    'slug' => $locale === 'de' ? $slug : Str::slug($payload['name']),
                    'description' => $payload['description'],
                    'seo_title' => $payload['name'],
                    'meta_description' => Str::limit($payload['description'], 150, ''),
                ]
            );
        }

        return $category->load('translations');
    }

    /**
     * @param array<int, User> $staff
     * @param array<string, Category> $categories
     * @return array<int, Post>
     */
    private function seedPosts(array $staff, array $categories): array
    {
        $definitions = [
            ['title' => 'Mehrwegbecher richtig reinigen', 'category_slugs' => ['ratgeber', 'produkte']],
            ['title' => 'Warum nachhaltige Verpackung Vertrauen schafft', 'category_slugs' => ['nachhaltigkeit', 'unternehmen']],
            ['title' => 'Checkliste fuer den ersten Blogartikel', 'category_slugs' => ['ratgeber', 'seo-aeo-geo']],
            ['title' => 'So funktionieren strukturierte Daten im Blog', 'category_slugs' => ['seo-aeo-geo', 'ratgeber']],
            ['title' => 'Produktseiten und Blog sinnvoll verbinden', 'category_slugs' => ['produkte', 'seo-aeo-geo']],
            ['title' => 'Antwortoptimierung fuer haeufige Kundenfragen', 'category_slugs' => ['seo-aeo-geo']],
            ['title' => 'Was gute Autorenprofile leisten', 'category_slugs' => ['unternehmen', 'ratgeber']],
            ['title' => 'Redaktionsplanung fuer mehrsprachige Inhalte', 'category_slugs' => ['ratgeber']],
            ['title' => 'Generative Engine Optimization im Alltag', 'category_slugs' => ['seo-aeo-geo']],
            ['title' => 'Bilder im Blog fuer SEO nutzen', 'category_slugs' => ['produkte', 'seo-aeo-geo']],
            ['title' => 'Kategorien mit Elternstruktur planen', 'category_slugs' => ['ratgeber']],
            ['title' => 'Kommentare als Community Signal', 'category_slugs' => ['unternehmen']],
            ['title' => 'Schnelle Bloglisten bei vielen Artikeln', 'category_slugs' => ['seo-aeo-geo']],
            ['title' => 'Newsletter Themen aus Blogdaten ableiten', 'category_slugs' => ['unternehmen', 'ratgeber']],
            ['title' => 'Content Fallbacks fuer Deutsch und Englisch', 'category_slugs' => ['ratgeber', 'seo-aeo-geo']],
        ];

        $posts = [];
        $authors = array_values(array_filter($staff, fn (User $user): bool => $user->canManageBlog()));

        foreach ($definitions as $index => $definition) {
            $slug = Str::slug($definition['title']);
            $post = $this->upsertPost(
                $slug,
                $definition,
                $authors[$index % count($authors)],
                Carbon::parse('2026-07-30 10:00:00')->subDays($index)
            );

            $post->categories()->sync(
                collect($definition['category_slugs'])
                    ->map(fn (string $categorySlug): int => $categories[$categorySlug]->id)
                    ->all()
            );

            $posts[] = $post->load(['translations', 'categories']);
        }

        return $posts;
    }

    private function upsertPost(string $slug, array $definition, User $author, Carbon $publishedAt): Post
    {
        $translation = PostTranslation::query()
            ->where('locale', 'de')
            ->where('slug', $slug)
            ->first();

        $post = $translation?->post ?? new Post();
        $post->forceFill([
            'user_id' => $author->id,
            'status' => 'published',
            'is_featured' => $slug === 'mehrwegbecher-richtig-reinigen',
            'published_at' => $publishedAt,
            'country_code' => 'DE',
            'region' => 'Hessen',
            'city' => 'Schoeneck',
            'latitude' => '50.2010000',
            'longitude' => '8.8350000',
            'timezone' => 'Europe/Berlin',
        ])->save();

        $deTitle = $definition['title'];
        $enTitle = $this->englishTitle($deTitle);
        $deExcerpt = 'Kurzer Ueberblick: ' . Str::lower($deTitle) . ' mit praktischen Hinweisen fuer Leser.';
        $enExcerpt = 'Quick overview: ' . Str::lower($enTitle) . ' with practical notes for readers.';

        $this->upsertPostTranslation($post, 'de', [
            'title' => $deTitle,
            'slug' => $slug,
            'excerpt' => $deExcerpt,
            'content' => $this->contentHtml($deTitle, 'de'),
        ]);

        $this->upsertPostTranslation($post, 'en', [
            'title' => $enTitle,
            'slug' => Str::slug($enTitle),
            'excerpt' => $enExcerpt,
            'content' => $this->contentHtml($enTitle, 'en'),
        ]);

        return $post;
    }

    private function upsertPostTranslation(Post $post, string $locale, array $payload): void
    {
        PostTranslation::query()->updateOrCreate(
            ['post_id' => $post->id, 'locale' => $locale],
            [
                'title' => $payload['title'],
                'slug' => $payload['slug'],
                'excerpt' => $payload['excerpt'],
                'content' => $payload['content'],
                'seo_title' => $payload['title'],
                'meta_description' => Str::limit($payload['excerpt'], 150, ''),
                'og_title' => $payload['title'],
                'og_description' => Str::limit($payload['excerpt'], 150, ''),
                'canonical_url' => url('/' . $payload['slug']),
                'preview_image_alt' => $payload['title'] . ' preview image',
            ]
        );
    }

    /**
     * @param array<int, Post> $posts
     * @param array<int, User> $members
     * @param array<int, User> $staff
     */
    private function seedComments(array $posts, array $members, array $staff): void
    {
        foreach ($posts as $index => $post) {
            $member = $members[$index % count($members)];
            $secondMember = $members[($index + 3) % count($members)];
            $staffUser = $staff[$index % count($staff)];

            $firstComment = Comment::query()->firstOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $member->id,
                    'parent_id' => null,
                    'content' => 'Helpful article. I would like to see one practical example for this topic.',
                ]
            );

            $reply = Comment::query()->firstOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $staffUser->id,
                    'parent_id' => $firstComment->id,
                    'content' => 'Thanks for the feedback. We added this topic to the editorial queue.',
                ]
            );

            Comment::query()->firstOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $secondMember->id,
                    'parent_id' => $reply->id,
                    'content' => 'This nested reply confirms threaded comments are working.',
                ]
            );
        }
    }

    private function upsertUser(array $data): User
    {
        $user = User::query()->firstOrNew(['email' => $data['email']]);

        $user->forceFill([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'email_verified_at' => $data['verified'] ? now() : null,
            'remember_token' => Str::random(10),
        ])->save();

        return $user;
    }

    private function englishTitle(string $title): string
    {
        return match ($title) {
            'Mehrwegbecher richtig reinigen' => 'How to clean reusable cups properly',
            'Warum nachhaltige Verpackung Vertrauen schafft' => 'Why sustainable packaging builds trust',
            'Checkliste fuer den ersten Blogartikel' => 'Checklist for the first blog post',
            'So funktionieren strukturierte Daten im Blog' => 'How structured data works in the blog',
            'Produktseiten und Blog sinnvoll verbinden' => 'Connecting product pages and blog content',
            'Antwortoptimierung fuer haeufige Kundenfragen' => 'Answer optimization for common customer questions',
            'Was gute Autorenprofile leisten' => 'What strong author profiles can do',
            'Redaktionsplanung fuer mehrsprachige Inhalte' => 'Editorial planning for multilingual content',
            'Generative Engine Optimization im Alltag' => 'Generative Engine Optimization in daily publishing',
            'Bilder im Blog fuer SEO nutzen' => 'Using blog images for SEO',
            'Kategorien mit Elternstruktur planen' => 'Planning categories with parent hierarchy',
            'Kommentare als Community Signal' => 'Comments as a community signal',
            'Schnelle Bloglisten bei vielen Artikeln' => 'Fast blog listings with many articles',
            'Newsletter Themen aus Blogdaten ableiten' => 'Deriving newsletter topics from blog data',
            'Content Fallbacks fuer Deutsch und Englisch' => 'Content fallbacks for German and English',
            default => $title,
        };
    }

    private function contentHtml(string $title, string $locale): string
    {
        $intro = $locale === 'de'
            ? 'Dieser Demoartikel zeigt, wie der Blog Inhalt, Kategorien, Kommentare und strukturierte Daten zusammenfuehrt.'
            : 'This demo article shows how the blog combines content, categories, comments, and structured data.';

        $summary = $locale === 'de'
            ? 'Die Inhalte sind absichtlich kurz, damit Seed-Daten schnell geladen werden und die Oberflaeche direkt getestet werden kann.'
            : 'The content is intentionally short so seed data loads quickly and the interface can be tested immediately.';

        return <<<HTML
<p>{$intro}</p>
<h2>{$title}</h2>
<p>{$summary}</p>
<ul>
    <li>Language based content with default fallback.</li>
    <li>Role based authors and member comments.</li>
    <li>SEO, AEO, and GEO friendly metadata.</li>
</ul>
HTML;
    }

    private function clearBlogCaches(): void
    {
        Cache::forget('blog.published_months.' . config('blog.default_locale', 'de'));
        Cache::forget('blog.featured_category_ids.de');
        Cache::forget('blog.featured_category_ids.en');
        Cache::forget('admin.dashboard.overview');
    }
}

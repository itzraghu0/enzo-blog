<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\PostTranslation;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly SeoService $seoService,
        private readonly GeoService $geoService,
        private readonly SlugService $slugService,
        private readonly MediaService $mediaService,
    ) {}

    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data): Post {
            $post = Post::create(array_merge(
                Arr::only($data, ['user_id', 'status', 'is_featured', 'published_at']),
                $this->geoService->normalize($data['geo'] ?? $data),
            ));

            $this->syncCategories($post, $data['category_ids'] ?? []);
            $this->syncTranslations($post, $data['translations'] ?? []);
            $this->flushListingCaches();

            return $post->load(['translations', 'categories', 'media']);
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data): Post {
            $post->update(array_merge(
                Arr::only($data, ['user_id', 'status', 'is_featured', 'published_at']),
                $this->geoService->normalize($data['geo'] ?? $data),
            ));

            if (array_key_exists('category_ids', $data)) {
                $this->syncCategories($post, $data['category_ids'] ?? []);
            }

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($post, $data['translations'] ?? []);
            }

            $this->flushListingCaches();

            return $post->load(['translations', 'categories', 'media']);
        });
    }

    public function publish(Post $post): Post
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->flushListingCaches();

        return $post->refresh();
    }

    public function delete(Post $post): bool
    {
        return (bool) DB::transaction(function () use ($post): bool {
            $deleted = (bool) $post->delete();
            $this->flushListingCaches();

            return $deleted;
        });
    }

    public function publishedIndex(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        $locale = $filters['locale'] ?? config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');
        $sort = $filters['sort'] ?? 'recent_desc';
        $month = $filters['month'] ?? null;
        $categoryId = $filters['category_id'] ?? null;
        $search = trim((string) ($filters['search'] ?? ''));

        $previewMediaSubquery = DB::table('media')
            ->selectRaw('MAX(id) as id, mediable_id')
            ->where('mediable_type', Post::class)
            ->where('collection', 'preview')
            ->whereNull('deleted_at')
            ->groupBy('mediable_id');

        $query = DB::table('posts')
            ->leftJoin('post_translations as pt_locale', function ($join) use ($locale): void {
                $join->on('pt_locale.post_id', '=', 'posts.id')
                    ->where('pt_locale.locale', '=', $locale)
                    ->whereNull('pt_locale.deleted_at');
            })
            ->leftJoin('post_translations as pt_default', function ($join) use ($defaultLocale): void {
                $join->on('pt_default.post_id', '=', 'posts.id')
                    ->where('pt_default.locale', '=', $defaultLocale)
                    ->whereNull('pt_default.deleted_at');
            })
            ->leftJoinSub($previewMediaSubquery, 'preview_pick', function ($join): void {
                $join->on('preview_pick.mediable_id', '=', 'posts.id');
            })
            ->leftJoin('media as preview_media', 'preview_media.id', '=', 'preview_pick.id')
            ->leftJoin('users as authors', 'authors.id', '=', 'posts.user_id')
            ->select([
                'posts.id',
                'posts.user_id',
                'posts.status',
                'posts.is_featured',
                'posts.published_at',
                'posts.created_at',
                'posts.updated_at',
                'authors.name as author_name',
                'preview_media.id as preview_media_id',
                'preview_media.path as preview_image_path',
                'preview_media.filename as preview_image_filename',
                'preview_media.alt_text as media_alt_text',
            ])
            ->selectRaw('COALESCE(pt_locale.title, pt_default.title) as title')
            ->selectRaw('COALESCE(pt_locale.slug, pt_default.slug) as slug')
            ->selectRaw('COALESCE(pt_locale.excerpt, pt_default.excerpt) as excerpt')
            ->selectRaw('COALESCE(pt_locale.preview_image_alt, pt_default.preview_image_alt, preview_media.alt_text) as preview_image_alt')
            ->selectRaw('(select count(*) from comments where comments.post_id = posts.id) as comments_count')
            ->where('posts.status', 'published')
            ->whereNull('posts.deleted_at')
            ->whereNotNull('posts.published_at')
            ->when($month, function ($query) use ($month): void {
                $date = Carbon::createFromFormat('Y-m', $month);

                $query->whereBetween('posts.published_at', [
                    $date->copy()->startOfMonth(),
                    $date->copy()->endOfMonth(),
                ]);
            })
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->whereExists(function ($categoryQuery) use ($categoryId): void {
                    $categoryQuery->selectRaw('1')
                        ->from('post_category')
                        ->whereColumn('post_category.post_id', 'posts.id')
                        ->where('post_category.category_id', $categoryId);
                });
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('pt_locale.title', 'like', "%{$search}%")
                        ->orWhere('pt_locale.excerpt', 'like', "%{$search}%")
                        ->orWhere('pt_locale.content', 'like', "%{$search}%")
                        ->orWhere('pt_default.title', 'like', "%{$search}%")
                        ->orWhere('pt_default.excerpt', 'like', "%{$search}%")
                        ->orWhere('pt_default.content', 'like', "%{$search}%");
                });
            });

        if (in_array($sort, ['title_asc', 'title_desc'], true)) {
            $direction = $sort === 'title_asc' ? 'asc' : 'desc';

            $query->orderByRaw('COALESCE(pt_locale.title, pt_default.title) ' . $direction);
        } else {
            $query->orderBy('posts.published_at', $sort === 'recent_asc' ? 'asc' : 'desc');
        }

        $posts = $query->paginate($perPage)->withQueryString();
        $posts->getCollection()->transform(function (object $post): object {
            $post->published_at = $post->published_at ? Carbon::parse($post->published_at) : null;
            $post->created_at = $post->created_at ? Carbon::parse($post->created_at) : null;
            $post->updated_at = $post->updated_at ? Carbon::parse($post->updated_at) : null;
            $post->preview_image_url = $post->preview_image_path ? asset($post->preview_image_path) : null;
            $post->preview_image_sm_url = $this->mediaVariantUrl($post, 'sm');
            $post->preview_image_md_url = $this->mediaVariantUrl($post, 'md');
            $post->preview_image_alt = $post->preview_image_alt ?: ($post->media_alt_text ?: $post->title);

            return $post;
        });

        return $posts;
    }

    public function resolvePublishedBySlug(string $slug, ?string $locale = null): ?Post
    {
        $locale ??= config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');

        $translation = PostTranslation::query()
            ->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt', 'content', 'seo_title', 'meta_description', 'og_title', 'og_description', 'canonical_url', 'preview_image_alt'])
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->first()
            ?? PostTranslation::query()
                ->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt', 'content', 'seo_title', 'meta_description', 'og_title', 'og_description', 'canonical_url', 'preview_image_alt'])
                ->where('locale', $defaultLocale)
                ->where('slug', $slug)
                ->first();

        if ($translation === null) {
            return null;
        }

        return $translation->post()
            ->select(['posts.id', 'posts.user_id', 'posts.status', 'posts.is_featured', 'posts.published_at', 'posts.created_at', 'posts.updated_at'])
            ->with([
                'translations' => function ($translationQuery) use ($locale, $defaultLocale): void {
                    $translationQuery->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt', 'content', 'seo_title', 'meta_description', 'og_title', 'og_description', 'canonical_url', 'preview_image_alt'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'categories.translations' => function ($categoryTranslationQuery) use ($locale, $defaultLocale): void {
                    $categoryTranslationQuery->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'previewMedia:id,user_id,disk,path,filename,original_name,mime_type,size,alt_text,title,caption,seo_keywords,hashtags,relevance_notes,aeo_summary,aeo_questions,geo_summary,geo_entities,geo_prompts,geo_context,collection,locale,mediable_type,mediable_id,sort_order,created_at,updated_at',
                'postMedia.media:id,user_id,disk,path,filename,original_name,mime_type,size,alt_text,title,caption,seo_keywords,hashtags,relevance_notes,aeo_summary,aeo_questions,geo_summary,geo_entities,geo_prompts,geo_context,collection,locale,created_at,updated_at',
                'author:id,name',
            ])
            ->where('status', 'published')
            ->first();
    }

    public function syncPreviewImage(Post $post, ?UploadedFile $file, ?Media $selectedMedia = null, array $data = []): ?Media
    {
        if ($file === null) {
            if ($selectedMedia === null) {
                if ($post->previewMedia !== null) {
                    $this->recordMediaAttachment($post, $post->previewMedia, 'preview', $data['locale'] ?? null, 'preview_image');
                }

                return $post->previewMedia;
            }

            $existing = $post->previewMedia;
            if ($existing !== null && $existing->is($selectedMedia)) {
                $this->recordMediaAttachment($post, $selectedMedia, 'preview', $data['locale'] ?? null, 'preview_image');

                return $existing;
            }

            if ($existing !== null && $existing->isNot($selectedMedia)) {
                $this->mediaService->delete($existing);
            }

            $media = $this->mediaService->duplicateForModel($selectedMedia, $post, 'preview', $data['locale'] ?? null, [
                'alt_text' => $this->blankToNull($data['alt_text'] ?? $selectedMedia->alt_text),
            ]);

            $this->recordMediaAttachment($post, $media, 'preview', $data['locale'] ?? null, 'preview_image');

            return $media;
        }

        $existing = $post->previewMedia;
        if ($existing !== null) {
            $this->mediaService->delete($existing);
        }

        $media = $this->mediaService->store($file, auth()->user(), array_merge($data, [
            'collection' => 'preview',
        ]));

        $media = $this->mediaService->attach($media, $post, 'preview', $data['locale'] ?? null);
        $this->recordMediaAttachment($post, $media, 'preview', $data['locale'] ?? null, 'preview_image');

        return $media;
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    public function syncCategories(Post $post, array $categoryIds): void
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds)));

        $post->categories()->sync($categoryIds);
    }

    public function attachMedia(Post $post, Media $media, string $collection = 'preview', ?string $locale = null): Media
    {
        $media = app(MediaService::class)->attach($media, $post, $collection, $locale);
        $this->recordMediaAttachment($post, $media, $collection, $locale, $collection);

        return $media;
    }

    private function recordMediaAttachment(Post $post, Media $media, string $collection, ?string $locale = null, ?string $purpose = null): PostMedia
    {
        return PostMedia::updateOrCreate(
            [
                'post_id' => $post->getKey(),
                'media_id' => $media->getKey(),
                'collection' => $collection,
            ],
            [
                'locale' => $locale,
                'purpose' => $purpose,
                'sort_order' => (int) ($media->sort_order ?? 0),
            ],
        );
    }

    public function publishedMonths(): Collection
    {
        $cacheKey = 'blog.published_months.' . config('blog.default_locale', 'en');
        $cachedMonths = Cache::get($cacheKey);

        if ($cachedMonths instanceof Collection) {
            return $cachedMonths;
        }

        if (is_object($cachedMonths) && str_starts_with(get_class($cachedMonths), '__PHP_Incomplete_Class')) {
            Cache::forget($cacheKey);
        } elseif (is_array($cachedMonths)) {
            return collect($cachedMonths);
        }

        $months = DB::table('posts')
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->whereNotNull('published_at')
            ->selectRaw("DATE_FORMAT(published_at, '%Y-%m') as month")
            ->groupBy('month')
            ->orderByDesc('month')
            ->pluck('month');

        Cache::put($cacheKey, $months->all(), now()->addMinutes(15));

        return $months;
    }

    private function syncTranslations(Post $post, array $translations): void
    {
        $fields = [
            'title',
            'slug',
            'excerpt',
            'content',
            'seo_title',
            'meta_description',
            'og_title',
            'og_description',
            'canonical_url',
            'preview_image_alt',
        ];

        $normalizedTranslations = $this->translationService->normalizeLocalePayloads($translations, $fields);
        $defaultLocale = $this->translationService->defaultLocale();

        foreach ($normalizedTranslations as $locale => $payload) {
            $payload = array_merge(
                $payload,
                $this->seoService->normalize($payload, $payload['title'] ?? null)
            );

            $payload['slug'] = $this->slugService->unique(
                (string) ($payload['slug'] ?? $payload['title'] ?? 'post'),
                static function (string $slug) use ($post, $locale): bool {
                    return PostTranslation::query()
                        ->where('locale', $locale)
                        ->where('slug', $slug)
                        ->where('post_id', '!=', $post->getKey())
                        ->exists();
                }
            );

            PostTranslation::updateOrCreate(
                [
                    'post_id' => $post->getKey(),
                    'locale' => $locale,
                ],
                Arr::only($payload, $fields)
            );
        }

        if (! $post->translations()->where('locale', $defaultLocale)->exists() && isset($normalizedTranslations[$defaultLocale])) {
            PostTranslation::updateOrCreate(
                [
                    'post_id' => $post->getKey(),
                    'locale' => $defaultLocale,
                ],
                Arr::only($normalizedTranslations[$defaultLocale], $fields)
            );
        }
    }

    private function flushListingCaches(): void
    {
        Cache::forget('blog.published_months.' . config('blog.default_locale', 'en'));
        Cache::forget('admin.dashboard.overview');
    }

    private function mediaVariantUrl(object $mediaProjection, string $variant): ?string
    {
        if (!array_key_exists($variant, config('blog.media_variants', []))) {
            return null;
        }

        if (!$mediaProjection->preview_media_id || !$mediaProjection->preview_image_filename) {
            return null;
        }

        $baseName = pathinfo((string) $mediaProjection->preview_image_filename, PATHINFO_FILENAME);
        $directory = trim(config('blog.media_directory', 'media/blog'), '/');
        $path = trim($directory.'/'.$mediaProjection->preview_media_id.'/'.$variant.'/'.$baseName.'.webp', '/');

        return is_file(public_path($path)) ? asset($path) : null;
    }
}

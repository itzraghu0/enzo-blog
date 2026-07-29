<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
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

        $query = Post::query()
            ->select(['posts.id', 'posts.user_id', 'posts.status', 'posts.is_featured', 'posts.published_at', 'posts.created_at', 'posts.updated_at'])
            ->with([
                'translations' => function ($translationQuery) use ($locale, $defaultLocale): void {
                    $translationQuery->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt', 'preview_image_alt'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'categories.translations' => function ($categoryTranslationQuery) use ($locale, $defaultLocale): void {
                    $categoryTranslationQuery->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'previewMedia:id,user_id,disk,path,filename,original_name,mime_type,size,alt_text,title,caption,collection,locale,mediable_type,mediable_id,sort_order,created_at,updated_at',
                'author:id,name',
            ])
            ->where('status', 'published')
            ->when($month, function ($query) use ($month): void {
                $date = Carbon::createFromFormat('Y-m', $month);

                $query->whereBetween('published_at', [
                    $date->copy()->startOfMonth(),
                    $date->copy()->endOfMonth(),
                ]);
            })
            ->when($categoryId, function ($query) use ($categoryId): void {
                $query->whereHas('categories', function ($categoryQuery) use ($categoryId): void {
                    $categoryQuery->whereKey($categoryId);
                });
            });

        if (in_array($sort, ['title_asc', 'title_desc'], true)) {
            $direction = $sort === 'title_asc' ? 'asc' : 'desc';

            $query->leftJoin('post_translations as pt_sort', function ($join) use ($locale): void {
                $join->on('pt_sort.post_id', '=', 'posts.id')
                    ->where('pt_sort.locale', '=', $locale);
            })
                ->select('posts.id', 'posts.user_id', 'posts.status', 'posts.is_featured', 'posts.published_at', 'posts.created_at', 'posts.updated_at')
                ->orderBy('pt_sort.title', $direction);
        } else {
            $query->orderBy('published_at', $sort === 'recent_asc' ? 'asc' : 'desc');
        }

        return $query->paginate($perPage)->withQueryString();
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
                'previewMedia:id,user_id,disk,path,filename,original_name,mime_type,size,alt_text,title,caption,collection,locale,mediable_type,mediable_id,sort_order,created_at,updated_at',
                'author:id,name',
            ])
            ->where('status', 'published')
            ->first();
    }

    public function syncPreviewImage(Post $post, ?UploadedFile $file, ?Media $selectedMedia = null, array $data = []): ?Media
    {
        if ($file === null) {
            if ($selectedMedia === null) {
                return $post->previewMedia;
            }

            $existing = $post->previewMedia;
            if ($existing !== null && $existing->is($selectedMedia)) {
                return $existing;
            }

            if ($existing !== null && $existing->isNot($selectedMedia)) {
                $this->mediaService->delete($existing);
            }

            return $this->mediaService->duplicateForModel($selectedMedia, $post, 'preview', $data['locale'] ?? null, [
                'alt_text' => $this->blankToNull($data['alt_text'] ?? $selectedMedia->alt_text),
            ]);
        }

        $existing = $post->previewMedia;
        if ($existing !== null) {
            $this->mediaService->delete($existing);
        }

        $media = $this->mediaService->store($file, auth()->user(), array_merge($data, [
            'collection' => 'preview',
        ]));

        return $this->mediaService->attach($media, $post, 'preview', $data['locale'] ?? null);
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
        return app(MediaService::class)->attach($media, $post, $collection, $locale);
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

        $months = Post::query()
            ->where('status', 'published')
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
}

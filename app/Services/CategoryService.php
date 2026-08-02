<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Media;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly SeoService $seoService,
        private readonly SlugService $slugService,
        private readonly MediaService $mediaService,
    ) {
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data): Category {
            $category = Category::create(Arr::only($data, ['parent_id', 'status', 'sort_order']));
            $this->syncTranslations($category, $data['translations'] ?? []);
            $this->syncPreviewImage($category, $data);
            $this->flushListingCaches();

            return $category->load(['translations', 'children', 'previewMedia']);
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data): Category {
            $category->update(Arr::only($data, ['parent_id', 'status', 'sort_order']));

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($category, $data['translations'] ?? []);
            }

            $this->syncPreviewImage($category, $data);
            $this->flushListingCaches();

            return $category->load(['translations', 'children', 'previewMedia']);
        });
    }

    public function delete(Category $category): bool
    {
        return (bool) DB::transaction(function () use ($category): bool {
            if ($category->previewMedia) {
                $this->mediaService->delete($category->previewMedia);
            }

            $deleted = (bool) $category->delete();
            $this->flushListingCaches();

            return $deleted;
        });
    }

    public function featured(): Collection
    {
        $locale = app()->getLocale() ?: config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');

        $categoryIds = Cache::remember("blog.featured_category_ids.{$locale}", now()->addMinutes(15), function () use ($locale, $defaultLocale): array {
            return Category::query()
                ->select(['id'])
                ->with([
                    'translations' => function ($query) use ($locale, $defaultLocale): void {
                        $query->select(['id', 'category_id', 'locale'])
                            ->whereIn('locale', [$locale, $defaultLocale]);
                    },
                ])
                ->where('status', 'active')
                ->orderBy('sort_order')
                ->pluck('id')
                ->all();
        });

        return Category::query()
            ->select(['id', 'parent_id', 'status', 'sort_order'])
            ->with([
                'translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'children.translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
            ])
            ->whereKey($categoryIds)
            ->orderBy('sort_order')
            ->get();
    }

    public function resolveBySlug(string $slug, ?string $locale = null): ?Category
    {
        $locale ??= config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');

        return Category::query()
            ->select(['id', 'parent_id', 'status', 'sort_order'])
            ->with([
                'translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'children.translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
            ])
            ->whereHas('translations', function ($query) use ($slug, $locale): void {
                $query->where('locale', $locale)->where('slug', $slug);
            })
            ->first()
            ?? Category::query()
                ->select(['id', 'parent_id', 'status', 'sort_order'])
                ->with([
                    'translations' => function ($query) use ($defaultLocale): void {
                        $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                            ->where('locale', $defaultLocale);
                    },
                    'children.translations' => function ($query) use ($defaultLocale): void {
                        $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                            ->where('locale', $defaultLocale);
                    },
                ])
                ->whereHas('translations', function ($query) use ($slug, $defaultLocale): void {
                    $query->where('locale', $defaultLocale)->where('slug', $slug);
                })
                ->first();
    }

    public function publishedPosts(Category $category, int $perPage = 9): LengthAwarePaginator
    {
        $locale = app()->getLocale() ?: config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');

        return $category->posts()
            ->select(['posts.id', 'posts.user_id', 'posts.status', 'posts.is_featured', 'posts.published_at', 'posts.created_at', 'posts.updated_at'])
            ->with([
                'translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt', 'preview_image_alt'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'categories.translations' => function ($query) use ($locale, $defaultLocale): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug'])
                        ->whereIn('locale', [$locale, $defaultLocale]);
                },
                'previewMedia:id,user_id,disk,path,filename,original_name,mime_type,size,alt_text,title,caption,seo_keywords,hashtags,relevance_notes,aeo_summary,aeo_questions,geo_summary,geo_entities,geo_prompts,geo_context,collection,locale,mediable_type,mediable_id,sort_order,created_at,updated_at',
                'author:id,name',
            ])
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function syncTranslations(Category $category, array $translations): void
    {
        $fields = [
            'name',
            'slug',
            'description',
            'seo_title',
            'meta_description',
        ];

        $normalizedTranslations = $this->translationService->normalizeLocalePayloads($translations, $fields);

        foreach ($normalizedTranslations as $locale => $payload) {
            $payload = array_merge(
                $payload,
                $this->seoService->normalize($payload, $payload['name'] ?? null)
            );

            $payload['slug'] = $this->slugService->unique(
                (string) ($payload['slug'] ?? $payload['name'] ?? 'category'),
                static function (string $slug) use ($category, $locale): bool {
                    return CategoryTranslation::query()
                        ->where('locale', $locale)
                        ->where('slug', $slug)
                        ->where('category_id', '!=', $category->getKey())
                        ->exists();
                }
            );

            CategoryTranslation::updateOrCreate(
                [
                    'category_id' => $category->getKey(),
                    'locale' => $locale,
                ],
                Arr::only($payload, $fields)
            );
        }
    }

    private function syncPreviewImage(Category $category, array $data): void
    {
        $file = $data['preview_image'] ?? null;
        $selectedMediaId = (int) ($data['preview_media_id'] ?? 0);
        $selectedMedia = $selectedMediaId > 0 ? Media::query()->find($selectedMediaId) : null;
        $altText = $this->blankToNull($data['preview_image_alt'] ?? null);

        if ($file === null && $selectedMedia === null) {
            if ($category->previewMedia && array_key_exists('preview_media_id', $data) && $selectedMediaId === 0) {
                $this->mediaService->delete($category->previewMedia);
            }

            return;
        }

        if ($file !== null) {
            if ($category->previewMedia) {
                $this->mediaService->delete($category->previewMedia);
            }

            $media = $this->mediaService->store($file, auth()->user(), [
                'collection' => 'preview',
                'alt_text' => $altText,
            ]);
            $this->mediaService->attach($media, $category, 'preview');

            return;
        }

        if ($selectedMedia === null) {
            return;
        }

        if ($category->previewMedia && $category->previewMedia->is($selectedMedia)) {
            $category->previewMedia->update([
                'alt_text' => $altText ?: $category->previewMedia->alt_text,
            ]);

            return;
        }

        if ($category->previewMedia && $category->previewMedia->isNot($selectedMedia)) {
            $this->mediaService->delete($category->previewMedia);
        }

        $copy = $this->mediaService->duplicateForModel($selectedMedia, $category, 'preview', null, [
            'alt_text' => $altText ?: $selectedMedia->alt_text,
        ]);
        $this->mediaService->attach($copy, $category, 'preview');
    }

    private function blankToNull(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function flushListingCaches(): void
    {
        foreach (config('blog.supported_locales', [config('blog.default_locale', 'en')]) as $locale) {
            Cache::forget("blog.featured_categories.{$locale}");
            Cache::forget("blog.featured_category_ids.{$locale}");
        }

        Cache::forget('admin.dashboard.overview');
    }
}

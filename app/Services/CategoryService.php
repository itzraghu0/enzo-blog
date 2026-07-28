<?php

namespace App\Services;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function __construct(
        private readonly TranslationService $translationService,
        private readonly SeoService $seoService,
        private readonly SlugService $slugService,
    ) {
    }

    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data): Category {
            $category = Category::create(Arr::only($data, ['parent_id', 'status', 'sort_order']));
            $this->syncTranslations($category, $data['translations'] ?? []);

            return $category->load(['translations', 'children']);
        });
    }

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data): Category {
            $category->update(Arr::only($data, ['parent_id', 'status', 'sort_order']));

            if (array_key_exists('translations', $data)) {
                $this->syncTranslations($category, $data['translations'] ?? []);
            }

            return $category->load(['translations', 'children']);
        });
    }

    public function delete(Category $category): bool
    {
        return (bool) DB::transaction(static fn (): bool => (bool) $category->delete());
    }

    public function featured(): Collection
    {
        return Category::query()
            ->with(['translations', 'children.translations'])
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();
    }

    public function resolveBySlug(string $slug, ?string $locale = null): ?Category
    {
        $locale ??= config('blog.default_locale', 'en');
        $defaultLocale = config('blog.default_locale', 'en');

        return Category::query()
            ->with(['translations', 'children.translations'])
            ->whereHas('translations', function ($query) use ($slug, $locale): void {
                $query->where('locale', $locale)->where('slug', $slug);
            })
            ->first()
            ?? Category::query()
                ->with(['translations', 'children.translations'])
                ->whereHas('translations', function ($query) use ($slug, $defaultLocale): void {
                    $query->where('locale', $defaultLocale)->where('slug', $slug);
                })
                ->first();
    }

    public function publishedPosts(Category $category, int $perPage = 9): LengthAwarePaginator
    {
        return $category->posts()
            ->with(['translations', 'categories.translations', 'previewMedia', 'author'])
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
}

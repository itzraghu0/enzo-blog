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

            return $post->load(['translations', 'categories', 'media']);
        });
    }

    public function publish(Post $post): Post
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $post->refresh();
    }

    public function delete(Post $post): bool
    {
        return (bool) DB::transaction(function () use ($post): bool {
            return (bool) $post->delete();
        });
    }

    public function publishedIndex(array $filters = [], int $perPage = 9): LengthAwarePaginator
    {
        $locale = $filters['locale'] ?? config('blog.default_locale', 'en');
        $sort = $filters['sort'] ?? 'recent_desc';
        $month = $filters['month'] ?? null;
        $categoryId = $filters['category_id'] ?? null;

        $query = Post::query()
            ->with(['translations', 'categories.translations', 'previewMedia', 'author'])
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
                ->select('posts.*')
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
            ->with(['post.translations', 'post.categories.translations', 'post.previewMedia', 'post.author'])
            ->where('locale', $locale)
            ->where('slug', $slug)
            ->first()
            ?? PostTranslation::query()
            ->with(['post.translations', 'post.categories.translations', 'post.previewMedia', 'post.author'])
            ->where('locale', $defaultLocale)
            ->where('slug', $slug)
            ->first();

        return $translation?->post?->status === 'published' ? $translation->post : null;
    }

    public function syncPreviewImage(Post $post, ?UploadedFile $file, array $data = []): ?Media
    {
        if ($file === null) {
            return $post->previewMedia;
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
        return Post::query()
            ->where('status', 'published')
            ->selectRaw("DATE_FORMAT(published_at, '%Y-%m') as month")
            ->whereNotNull('published_at')
            ->groupBy('month')
            ->orderByDesc('month')
            ->pluck('month');
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
}

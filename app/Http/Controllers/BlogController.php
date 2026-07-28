<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CategoryService $categoryService,
        private readonly CommentService $commentService,
    ) {
    }

    public function index(Request $request): View
    {
        $locale = $this->resolveLocale($request);
        $sort = (string) $request->get('sort', 'recent_desc');
        $sort = in_array($sort, ['recent_desc', 'recent_asc', 'title_asc', 'title_desc'], true) ? $sort : 'recent_desc';

        $month = $request->get('month');
        $month = is_string($month) && preg_match('/^\d{4}-\d{2}$/', $month) === 1 ? $month : null;

        $categoryId = $request->get('category_id');
        $categoryId = is_numeric($categoryId) ? (int) $categoryId : null;

        $filters = [
            'locale' => $locale,
            'sort' => $sort,
            'month' => $month,
            'category_id' => $categoryId,
        ];

        $perPage = (int) $request->get('per_page', 20);
        if (! in_array($perPage, [12, 20, 40], true)) {
            $perPage = 20;
        }

        return view('frontend.blog.index', [
            'posts' => $this->postService->publishedIndex($filters, $perPage),
            'locale' => $locale,
            'featuredCategories' => $this->categoryService->featured(),
            'availableMonths' => $this->postService->publishedMonths(),
            'filters' => array_merge($filters, ['per_page' => $perPage]),
            'sortOptions' => [
                'recent_desc' => __('Most recent first'),
                'recent_asc' => __('Oldest first'),
                'title_asc' => __('Title A-Z'),
                'title_desc' => __('Title Z-A'),
            ],
            'perPageOptions' => [12, 20, 40],
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $locale = $this->resolveLocale($request);
        $post = $this->postService->resolvePublishedBySlug($slug, $locale);
        abort_if($post === null, 404);

        return view('frontend.blog.show', [
            'post' => $post,
            'locale' => $locale,
            'translation' => $post->translationFor($locale),
            'comments' => $this->commentService->treeForPost($post),
        ]);
    }

    public function category(Request $request, string $slug): View
    {
        $locale = $this->resolveLocale($request);
        $category = $this->categoryService->resolveBySlug($slug, $locale);
        abort_if($category === null, 404);

        return view('frontend.blog.category', [
            'category' => $category,
            'posts' => $this->categoryService->publishedPosts($category),
            'locale' => $locale,
            'translation' => $category->translationFor($locale),
        ]);
    }

    private function resolveLocale(Request $request): string
    {
        $locale = (string) $request->get('locale', app()->getLocale() ?: config('blog.default_locale', 'en'));

        return in_array($locale, config('blog.supported_locales', []), true)
            ? $locale
            : config('blog.default_locale', 'en');
    }
}

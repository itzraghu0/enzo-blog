<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(Request $request): View
    {
        $locale = $request->get('locale', config('blog.default_locale'));
        $search = trim((string) $request->get('search', ''));

        $stats = [
            'total' => Category::query()->count(),
            'active' => Category::query()->where('status', 'active')->count(),
            'inactive' => Category::query()->where('status', 'inactive')->count(),
            'parents' => Category::query()->whereNull('parent_id')->count(),
        ];

        $categories = Category::query()
            ->select(['id', 'parent_id', 'status', 'sort_order', 'created_at', 'updated_at'])
            ->with([
                'translations' => function ($query): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug', 'description']);
                },
                'parent.translations' => function ($query): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                },
                'children.translations' => function ($query): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                },
            ])
            ->when($search !== '', function ($query) use ($search, $locale): void {
                $query->whereHas('translations', function ($translationQuery) use ($search, $locale): void {
                    $translationQuery->where('locale', $locale)
                        ->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.blog.categories.index', [
            'categories' => $categories,
            'locale' => $locale,
            'search' => $search,
            'stats' => $stats,
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.categories.create', [
            'category' => new Category(),
            'parents' => Category::query()
                ->select(['id', 'parent_id', 'status', 'sort_order'])
                ->with([
                    'translations' => function ($query): void {
                        $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                    },
                ])
                ->orderBy('sort_order')
                ->get(),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
        ]);
    }

    public function edit(Category $category): View
    {
        return view('admin.blog.categories.edit', [
            'category' => $category->load(['translations', 'parent.translations', 'children.translations']),
            'parents' => Category::query()
                ->select(['id', 'parent_id', 'status', 'sort_order'])
                ->with([
                    'translations' => function ($query): void {
                        $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                    },
                ])
                ->whereKeyNot($category->getKey())
                ->orderBy('sort_order')
                ->get(),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category->load(['translations', 'children', 'parent']),
        ]);
    }

    public function store(UpsertCategoryRequest $request): JsonResponse|RedirectResponse
    {
        $category = $this->categoryService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Category created successfully'),
                'data' => $category,
            ], 201);
        }

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', __('Category created successfully'));
    }

    public function update(UpsertCategoryRequest $request, Category $category): JsonResponse|RedirectResponse
    {
        $category = $this->categoryService->update($category, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Category updated successfully'),
                'data' => $category,
            ]);
        }

        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('success', __('Category updated successfully'));
    }

    public function destroy(Request $request, Category $category): JsonResponse|RedirectResponse
    {
        $this->categoryService->delete($category);

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', __('Category deleted successfully'));
    }
}

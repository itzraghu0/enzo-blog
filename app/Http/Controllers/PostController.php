<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertPostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use App\Services\MediaService;
use App\Services\PostService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly MediaService $mediaService,
    ) {
    }

    public function index(Request $request): View
    {
        $locale = $request->get('locale', config('blog.default_locale'));
        $search = trim((string) $request->get('search', ''));

        $posts = Post::query()
            ->with(['translations', 'categories.translations', 'author'])
            ->when($search !== '', function ($query) use ($search, $locale): void {
                $query->whereHas('translations', function ($translationQuery) use ($search, $locale): void {
                    $translationQuery->where('locale', $locale)
                        ->where('title', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.blog.posts.index', [
            'posts' => $posts,
            'locale' => $locale,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.posts.create', [
            'post' => new Post(),
            'categories' => Category::query()->with('translations')->orderBy('sort_order')->get(),
            'authors' => User::query()->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_EDITOR,
                User::ROLE_AUTHOR,
            ])->orderBy('name')->get(),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
            'mediaLibrary' => $this->mediaService->paginate(),
        ]);
    }

    public function edit(Post $post): View
    {
        return view('admin.blog.posts.edit', [
            'post' => $post->load(['translations', 'categories.translations', 'media', 'previewMedia']),
            'categories' => Category::query()->with('translations')->orderBy('sort_order')->get(),
            'authors' => User::query()->whereIn('role', [
                User::ROLE_ADMIN,
                User::ROLE_EDITOR,
                User::ROLE_AUTHOR,
            ])->orderBy('name')->get(),
            'locales' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
            'mediaLibrary' => $this->mediaService->paginate(),
        ]);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json([
            'data' => $post->load(['translations', 'categories', 'media', 'author']),
        ]);
    }

    public function store(UpsertPostRequest $request): JsonResponse|RedirectResponse
    {
        $data = array_merge(
            $request->validated(),
            ['user_id' => $request->user()->id]
        );

        $post = $this->postService->create($data);
        $this->postService->syncPreviewImage($post, $request->file('preview_image'), [
            'alt_text' => $request->input('preview_image_alt'),
            'locale' => config('blog.default_locale'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Post created successfully'),
                'data' => $post->fresh(['translations', 'categories', 'media', 'previewMedia']),
            ], 201);
        }

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', __('Post created successfully'));
    }

    public function update(UpsertPostRequest $request, Post $post): JsonResponse|RedirectResponse
    {
        $post = $this->postService->update($post, $request->validated());
        $this->postService->syncPreviewImage($post, $request->file('preview_image'), [
            'alt_text' => $request->input('preview_image_alt'),
            'locale' => config('blog.default_locale'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Post updated successfully'),
                'data' => $post->fresh(['translations', 'categories', 'media', 'previewMedia']),
            ]);
        }

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', __('Post updated successfully'));
    }

    public function destroy(Request $request, Post $post): JsonResponse|RedirectResponse
    {
        $this->postService->delete($post);

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', __('Post deleted successfully'));
    }
}

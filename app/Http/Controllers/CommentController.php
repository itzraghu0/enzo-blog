<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Services\CommentService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CommentService $commentService,
    ) {
    }

    public function store(StoreCommentRequest $request, string $slug): JsonResponse|RedirectResponse
    {
        $post = $this->postService->resolvePublishedBySlug($slug, $request->get('locale'));
        abort_if($post === null, 404);

        $comment = $this->commentService->create($post, $request->user(), $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('Comment posted successfully'),
                'data' => $comment,
            ], 201);
        }

        return redirect()
            ->to(route('blog.show', ['slug' => $slug, 'locale' => $request->get('locale')]).'#comment-'.$comment->id)
            ->with('success', __('Comment posted successfully'))
        ;
    }
}

<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommentService
{
    public function create(Post $post, User $user, array $data): Comment
    {
        return DB::transaction(function () use ($post, $user, $data): Comment {
            $parentId = $data['parent_id'] ?? null;

            if ($parentId !== null) {
                $this->ensureParentBelongsToPost($post, (int) $parentId);
            }

            return Comment::create([
                'post_id' => $post->getKey(),
                'user_id' => $user->getKey(),
                'parent_id' => $parentId,
                'content' => $data['content'],
            ])->load(['user', 'childrenRecursive.user']);
        });
    }

    public function treeForPost(Post $post): Collection
    {
        return Comment::query()
            ->with(['user', 'childrenRecursive.user'])
            ->where('post_id', $post->getKey())
            ->whereNull('parent_id')
            ->oldest()
            ->get();
    }

    private function ensureParentBelongsToPost(Post $post, int $parentId): void
    {
        $belongs = Comment::query()
            ->whereKey($parentId)
            ->where('post_id', $post->getKey())
            ->exists();

        if (! $belongs) {
            throw ValidationException::withMessages([
                'parent_id' => __('The selected parent comment is invalid.'),
            ]);
        }
    }
}

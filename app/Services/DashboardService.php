<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function overview(): array
    {
        return Cache::remember('admin.dashboard.overview', now()->addMinutes(5), function (): array {
            $authorRoles = [
                User::ROLE_ADMIN,
                User::ROLE_EDITOR,
                User::ROLE_AUTHOR,
            ];

            $totals = [
                'posts' => Post::query()->count(),
                'published' => Post::query()->where('status', 'published')->count(),
                'drafts' => Post::query()->where('status', 'draft')->count(),
                'categories' => Category::query()->count(),
                'comments' => Comment::query()->count(),
                'media' => Media::query()->count(),
                'authors' => User::query()->whereIn('role', $authorRoles)->count(),
                'members' => User::query()->where('role', User::ROLE_VIEWER)->count(),
                'verifiedMembers' => User::query()->where('role', User::ROLE_VIEWER)->whereNotNull('email_verified_at')->count(),
                'pendingMembers' => User::query()->where('role', User::ROLE_VIEWER)->whereNull('email_verified_at')->count(),
            ];

            $trend = $this->monthlyTrend();

            return [
                'totals' => $totals,
                'trend' => $trend,
                'recentPosts' => $this->recentPosts(),
                'recentComments' => $this->recentComments(),
                'topCategories' => $this->topCategories(),
                'recentMembers' => $this->recentMembers(),
                'roleBreakdown' => $this->roleBreakdown(),
            ];
        });
    }

    private function monthlyTrend(): Collection
    {
        $startMonth = now()->subMonths(11)->startOfMonth();
        $counts = Post::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '>=', $startMonth)
            ->selectRaw("DATE_FORMAT(published_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        return collect(CarbonPeriod::create($startMonth, '1 month', now()->startOfMonth()))
            ->map(function (Carbon $date) use ($counts): array {
                $month = $date->format('Y-m');

                return [
                    'label' => $date->format('M'),
                    'month' => $month,
                    'count' => (int) ($counts[$month] ?? 0),
                ];
            });
    }

    private function recentPosts(): Collection
    {
        return Post::query()
            ->select(['id', 'user_id', 'status', 'is_featured', 'published_at', 'created_at'])
            ->with([
                'author:id,name',
                'translations' => function ($query): void {
                    $query->select(['id', 'post_id', 'locale', 'title', 'slug', 'excerpt']);
                },
                'categories.translations' => function ($query): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                },
            ])
            ->latest('published_at')
            ->limit(6)
            ->get();
    }

    private function recentComments(): Collection
    {
        return Comment::query()
            ->select(['id', 'post_id', 'user_id', 'parent_id', 'content', 'created_at'])
            ->with([
                'user:id,name',
                'post:id,user_id,status,published_at',
                'post.translations' => function ($query): void {
                    $query->select(['id', 'post_id', 'locale', 'title', 'slug']);
                },
            ])
            ->latest()
            ->limit(6)
            ->get();
    }

    private function topCategories(): Collection
    {
        return Category::query()
            ->select(['id', 'parent_id', 'status', 'sort_order'])
            ->withCount('posts')
            ->with([
                'translations' => function ($query): void {
                    $query->select(['id', 'category_id', 'locale', 'name', 'slug']);
                },
            ])
            ->orderByDesc('posts_count')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();
    }

    private function recentMembers(): Collection
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at'])
            ->where('role', User::ROLE_VIEWER)
            ->latest('created_at')
            ->limit(6)
            ->get();
    }

    private function roleBreakdown(): Collection
    {
        return collect([
            [
                'label' => __('Admins'),
                'count' => User::query()->where('role', User::ROLE_ADMIN)->count(),
                'tone' => 'danger',
            ],
            [
                'label' => __('Editors'),
                'count' => User::query()->where('role', User::ROLE_EDITOR)->count(),
                'tone' => 'primary',
            ],
            [
                'label' => __('Authors'),
                'count' => User::query()->where('role', User::ROLE_AUTHOR)->count(),
                'tone' => 'success',
            ],
            [
                'label' => __('Viewers'),
                'count' => User::query()->where('role', User::ROLE_VIEWER)->count(),
                'tone' => 'secondary',
            ],
        ]);
    }
}

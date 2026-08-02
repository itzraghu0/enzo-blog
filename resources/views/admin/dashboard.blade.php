@extends('layouts.admin.app')
@section('title', __('Dashboard'))

@php
    $totals = $overview['totals'] ?? [];
    $trend = collect($overview['trend'] ?? []);
    $recentPosts = collect($overview['recentPosts'] ?? []);
    $recentComments = collect($overview['recentComments'] ?? []);
    $topCategories = collect($overview['topCategories'] ?? []);
    $recentMembers = collect($overview['recentMembers'] ?? []);
    $roleBreakdown = collect($overview['roleBreakdown'] ?? []);
    $maxTrend = max((int) $trend->max('count'), 1);

    $statCards = [
        ['label' => __('Total posts'), 'value' => $totals['posts'] ?? 0, 'icon' => 'ki-document', 'color' => '#2563eb', 'note' => __('All blog entries')],
        ['label' => __('Published'), 'value' => $totals['published'] ?? 0, 'icon' => 'ki-check-circle', 'color' => '#16a34a', 'note' => __('Visible on frontend')],
        ['label' => __('Drafts'), 'value' => $totals['drafts'] ?? 0, 'icon' => 'ki-pencil', 'color' => '#f59e0b', 'note' => __('Waiting for publish')],
        ['label' => __('Members'), 'value' => $totals['members'] ?? 0, 'icon' => 'ki-users', 'color' => '#7c3aed', 'note' => __('Frontend accounts')],
    ];
@endphp

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Dashboard') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Admin') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Dashboard') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus-circle"></i>
                    {{ __('Write post') }}
                </a>
                <a href="{{ route('admin.media.index') }}" class="kt-btn kt-btn-outline">
                    <i class="ki-filled ki-picture"></i>
                    {{ __('Media') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid gap-5 lg:gap-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-6 lg:p-7.5">
                <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
                    <div>
                        <div class="text-sm text-secondary-foreground">{{ __('Blog overview') }}</div>
                        <h2 class="mt-2 text-2xl font-semibold text-mono">{{ __('Manage publishing, members, comments, and media.') }}</h2>
                        <p class="mt-2 max-w-3xl text-sm text-secondary-foreground">
                            {{ __('Standard analytics view with cached counts and lightweight tables for daily administration.') }}
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-xl border border-border p-4">
                            <div class="text-xs text-secondary-foreground">{{ __('Categories') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-mono">{{ $totals['categories'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl border border-border p-4">
                            <div class="text-xs text-secondary-foreground">{{ __('Comments') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-mono">{{ $totals['comments'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl border border-border p-4">
                            <div class="text-xs text-secondary-foreground">{{ __('Media') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-mono">{{ $totals['media'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-xl border border-border p-4">
                            <div class="text-xs text-secondary-foreground">{{ __('Authors') }}</div>
                            <div class="mt-1 text-2xl font-semibold text-mono">{{ $totals['authors'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($statCards as $card)
                <div class="kt-card">
                    <div class="kt-card-content p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-sm text-secondary-foreground">{{ $card['label'] }}</div>
                                <div class="mt-2 text-3xl font-semibold text-mono">{{ $card['value'] }}</div>
                                <div class="mt-2 text-xs text-secondary-foreground">{{ $card['note'] }}</div>
                            </div>
                            <div class="flex items-center justify-center rounded-xl text-white" style="width: 48px; height: 48px; background: {{ $card['color'] }};">
                                <i class="ki-filled {{ $card['icon'] }} text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="kt-card">
                <div class="kt-card-header py-4">
                    <div>
                        <h3 class="kt-card-title">{{ __('Publishing trend') }}</h3>
                        <div class="text-xs text-secondary-foreground">{{ __('Last 12 months') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-4">
                    <div class="grid grid-cols-6 gap-2 md:grid-cols-12">
                        @foreach ($trend as $item)
                            @php
                                $height = max(8, (int) round(($item['count'] / $maxTrend) * 62));
                            @endphp
                            <div class="flex min-w-0 flex-col items-center justify-end gap-1">
                                <div class="flex w-full items-end justify-center rounded-md px-1" style="height: 72px; background: #f5f7fb;">
                                    <div class="w-full max-w-6 rounded-t-md" style="height: {{ $height }}px; background: #2563eb;"></div>
                                </div>
                                <div class="text-xs text-secondary-foreground">{{ $item['label'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header py-4">
                    <div>
                        <h3 class="kt-card-title">{{ __('Content status') }}</h3>
                        <div class="text-xs text-secondary-foreground">{{ __('Publishing health') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-4 grid gap-3">
                    @php
                        $postTotal = max((int) ($totals['posts'] ?? 0), 1);
                        $publishedPercent = (int) round((($totals['published'] ?? 0) / $postTotal) * 100);
                        $draftPercent = (int) round((($totals['drafts'] ?? 0) / $postTotal) * 100);
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-secondary-foreground">{{ __('Published') }}</span>
                            <span class="font-semibold text-mono">{{ $publishedPercent }}%</span>
                        </div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-muted">
                            <div class="h-full rounded-full" style="width: {{ $publishedPercent }}%; background: #16a34a;"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-secondary-foreground">{{ __('Drafts') }}</span>
                            <span class="font-semibold text-mono">{{ $draftPercent }}%</span>
                        </div>
                        <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-muted">
                            <div class="h-full rounded-full" style="width: {{ $draftPercent }}%; background: #f59e0b;"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <div class="rounded-lg border border-border p-3">
                            <div class="text-xs text-secondary-foreground">{{ __('Verified members') }}</div>
                            <div class="mt-1 text-lg font-semibold text-mono">{{ $totals['verifiedMembers'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-lg border border-border p-3">
                            <div class="text-xs text-secondary-foreground">{{ __('Pending members') }}</div>
                            <div class="mt-1 text-lg font-semibold text-mono">{{ $totals['pendingMembers'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="kt-card kt-card-grid">
                <div class="kt-card-header py-5">
                    <div>
                        <h3 class="kt-card-title">{{ __('Recent posts') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Latest content activity.') }}</div>
                    </div>
                    <a href="{{ route('admin.posts.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">{{ __('View all') }}</a>
                </div>
                <div class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable-x-auto">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Author') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Published') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentPosts as $post)
                                    @php
                                        $translation = $post->translationFor(config('blog.default_locale', 'de'));
                                    @endphp
                                    <tr>
                                        <td class="font-medium text-mono">{{ $translation?->title ?? __('Untitled') }}</td>
                                        <td>{{ $post->author?->name ?? '-' }}</td>
                                        <td>
                                            <span class="kt-badge {{ $post->status === 'published' ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>
                                        <td>{{ optional($post->published_at)->format('d.m.Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-secondary-foreground">{{ __('No items found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="kt-card kt-card-grid">
                <div class="kt-card-header py-5">
                    <div>
                        <h3 class="kt-card-title">{{ __('Recent comments') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Newest member discussions.') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-5 grid gap-3">
                    @forelse ($recentComments as $comment)
                        @php
                            $commentPostTranslation = $comment->post?->translationFor(config('blog.default_locale', 'de'));
                        @endphp
                        <div class="rounded-xl border border-border p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="font-medium text-mono">{{ $comment->user?->name ?? __('Guest') }}</div>
                                <div class="text-xs text-secondary-foreground">{{ $comment->created_at?->diffForHumans() }}</div>
                            </div>
                            <div class="mt-2 text-sm text-secondary-foreground">{{ \Illuminate\Support\Str::limit($comment->content, 110) }}</div>
                            <div class="mt-3 text-xs text-secondary-foreground">{{ __('Post') }}: {{ $commentPostTranslation?->title ?? __('Untitled') }}</div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-secondary-foreground">{{ __('No items found') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="kt-card kt-card-grid">
                <div class="kt-card-header py-5">
                    <div>
                        <h3 class="kt-card-title">{{ __('Top categories') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Categories ranked by post count.') }}</div>
                    </div>
                    <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-sm kt-btn-outline">{{ __('Manage') }}</a>
                </div>
                <div class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable-x-auto">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Posts') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topCategories as $category)
                                    @php
                                        $translation = $category->translationFor(config('blog.default_locale', 'de'));
                                    @endphp
                                    <tr>
                                        <td class="font-medium text-mono">{{ $translation?->name ?? '-' }}</td>
                                        <td>{{ $category->posts_count }}</td>
                                        <td>
                                            <span class="kt-badge {{ $category->status === 'active' ? 'kt-badge-success' : 'kt-badge-secondary' }}">
                                                {{ ucfirst($category->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 text-center text-secondary-foreground">{{ __('No items found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <div>
                        <h3 class="kt-card-title">{{ __('Users and roles') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Staff roles and newest members.') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-5 grid gap-5">
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                        @foreach ($roleBreakdown as $item)
                            <div class="rounded-xl border border-border p-4">
                                <div class="text-xs text-secondary-foreground">{{ $item['label'] }}</div>
                                <div class="mt-1 text-2xl font-semibold text-mono">{{ $item['count'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid gap-3">
                        @forelse ($recentMembers->take(4) as $user)
                            <div class="flex items-center justify-between gap-3 rounded-xl border border-border p-4">
                                <div class="min-w-0">
                                    <div class="truncate font-medium text-mono">{{ $user->name }}</div>
                                    <div class="truncate text-sm text-secondary-foreground">{{ $user->email }}</div>
                                </div>
                                <span class="kt-badge {{ $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                    {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                </span>
                            </div>
                        @empty
                            <div class="py-8 text-center text-secondary-foreground">{{ __('No members found') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

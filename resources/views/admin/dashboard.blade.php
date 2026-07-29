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
    $roleToneClasses = [
        'danger' => 'kt-badge-danger',
        'primary' => 'kt-badge-primary',
        'success' => 'kt-badge-success',
        'secondary' => 'kt-badge-secondary',
    ];
@endphp

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Dashboard') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Dashboard') }}</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus-circle"></i>
                    {{ __('New Post') }}
                </a>
                <a href="{{ route('admin.media.index') }}" class="kt-btn kt-btn-outline">
                    {{ __('Media Library') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-7.5">
        <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 text-white shadow-2xl">
            <div class="kt-card-content p-6 lg:p-10">
                <div class="grid lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.8fr)] gap-8 items-center">
                    <div class="space-y-5">
                        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em]">
                            <span class="size-2 rounded-full bg-emerald-400"></span>
                            {{ __('Live analytics') }}
                        </div>
                        <div class="space-y-3">
                            <h2 class="text-3xl md:text-5xl font-semibold leading-tight">
                                {{ __('Track every story, category, and interaction from one place.') }}
                            </h2>
                            <p class="max-w-2xl text-sm md:text-base text-white/70">
                                {{ __('This dashboard is optimized for larger blogs, with cached analytics, lightweight queries, and quick access to the most active content.') }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('admin.posts.create') }}" class="kt-btn kt-btn-light">
                                {{ __('Write post') }}
                            </a>
                            <a href="{{ route('admin.categories.create') }}" class="kt-btn kt-btn-outline text-white border-white/20 bg-white/5 hover:bg-white/10">
                                {{ __('Create category') }}
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-3xl bg-emerald-500/15 border border-white/10 p-5 backdrop-blur">
                            <div class="text-sm text-white/70">{{ __('Posts') }}</div>
                            <div class="mt-2 text-3xl font-semibold">{{ $totals['posts'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3xl bg-sky-500/15 border border-white/10 p-5 backdrop-blur">
                            <div class="text-sm text-white/70">{{ __('Published') }}</div>
                            <div class="mt-2 text-3xl font-semibold">{{ $totals['published'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3xl bg-amber-500/15 border border-white/10 p-5 backdrop-blur">
                            <div class="text-sm text-white/70">{{ __('Comments') }}</div>
                            <div class="mt-2 text-3xl font-semibold">{{ $totals['comments'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-3xl bg-fuchsia-500/15 border border-white/10 p-5 backdrop-blur">
                            <div class="text-sm text-white/70">{{ __('Media') }}</div>
                            <div class="mt-2 text-3xl font-semibold">{{ $totals['media'] ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid xl:grid-cols-4 gap-5 lg:gap-7.5">
            <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white">
                <div class="kt-card-content p-6">
                    <div class="text-sm text-white/80">{{ __('Total posts') }}</div>
                    <div class="text-3xl font-semibold mt-1">{{ $totals['posts'] ?? 0 }}</div>
                    <div class="mt-4 text-xs uppercase tracking-[0.2em] text-white/70">{{ __('All content items') }}</div>
                </div>
            </div>
            <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-sky-500 to-cyan-500 text-white">
                <div class="kt-card-content p-6">
                    <div class="text-sm text-white/80">{{ __('Published') }}</div>
                    <div class="text-3xl font-semibold mt-1">{{ $totals['published'] ?? 0 }}</div>
                    <div class="mt-4 text-xs uppercase tracking-[0.2em] text-white/70">{{ __('Visible on frontend') }}</div>
                </div>
            </div>
            <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-amber-500 to-orange-500 text-white">
                <div class="kt-card-content p-6">
                    <div class="text-sm text-white/80">{{ __('Drafts') }}</div>
                    <div class="text-3xl font-semibold mt-1">{{ $totals['drafts'] ?? 0 }}</div>
                    <div class="mt-4 text-xs uppercase tracking-[0.2em] text-white/70">{{ __('Needs review') }}</div>
                </div>
            </div>
            <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-fuchsia-500 to-violet-600 text-white">
                <div class="kt-card-content p-6">
                    <div class="text-sm text-white/80">{{ __('Authors') }}</div>
                    <div class="text-3xl font-semibold mt-1">{{ $totals['authors'] ?? 0 }}</div>
                    <div class="mt-4 text-xs uppercase tracking-[0.2em] text-white/70">{{ __('Active writers') }}</div>
                </div>
            </div>
        </div>

        <div class="grid xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)] gap-5 lg:gap-7.5">
            <div class="kt-card">
                <div class="kt-card-header">
                    <div>
                        <h3 class="kt-card-title">{{ __('Publishing trend') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Last 12 months of published posts.') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-5">
                    <div class="grid grid-cols-12 gap-3 items-end min-h-[260px]">
                        @foreach ($trend as $item)
                            @php
                                $height = $maxTrend > 0 ? max(12, round(($item['count'] / $maxTrend) * 100)) : 12;
                            @endphp
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-full flex justify-center">
                                    <div class="w-full max-w-12 rounded-t-2xl bg-gradient-to-t from-slate-900 to-sky-500 shadow-lg shadow-sky-500/20" style="height: {{ $height }}%;">
                                    </div>
                                </div>
                                <div class="text-xs text-secondary-foreground">{{ $item['label'] }}</div>
                                <div class="text-xs font-semibold text-mono">{{ $item['count'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <div>
                        <h3 class="kt-card-title">{{ __('Content mix') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Status and support counters.') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-5 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ __('Drafts') }}</span>
                            <span class="font-semibold">{{ $totals['drafts'] ?? 0 }}</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-slate-200 overflow-hidden">
                            <div class="h-full rounded-full bg-amber-500" style="width: {{ $totals['posts'] ? min(100, round((($totals['drafts'] ?? 0) / max($totals['posts'], 1)) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ __('Categories') }}</span>
                            <span class="font-semibold">{{ $totals['categories'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ __('Media assets') }}</span>
                            <span class="font-semibold">{{ $totals['media'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ __('Comments') }}</span>
                            <span class="font-semibold">{{ $totals['comments'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,0.85fr)] gap-5 lg:gap-7.5">
            <div class="kt-card kt-card-grid">
                <div class="kt-card-header py-5 flex-wrap gap-3">
                    <div>
                        <h3 class="kt-card-title">{{ __('Activity center') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Switch between posts, comments, and new members.') }}</div>
                    </div>
                    <div class="kt-tabs kt-tabs-line" data-kt-tabs="true" id="dashboard_activity_tabs">
                        <div class="flex items-center gap-2.5">
                            <button class="kt-tab-toggle py-2 px-3 active" data-kt-tab-toggle="#activity_posts">
                                {{ __('Posts') }}
                            </button>
                            <button class="kt-tab-toggle py-2 px-3" data-kt-tab-toggle="#activity_comments">
                                {{ __('Comments') }}
                            </button>
                            <button class="kt-tab-toggle py-2 px-3" data-kt-tab-toggle="#activity_users">
                                {{ __('Members') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="kt-card-content p-5">
                    <div class="grid gap-5" id="activity_posts">
                        @forelse ($recentPosts as $post)
                            @php
                                $translation = $post->translationFor(config('blog.default_locale', 'en'));
                                $categoryTranslation = $post->categories->first()?->translationFor(config('blog.default_locale', 'en'));
                            @endphp
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-border p-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-11 rounded-2xl bg-gradient-to-br from-slate-900 to-sky-500 text-white flex items-center justify-center">
                                        <i class="ki-filled ki-document text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-mono">{{ $translation?->title ?? __('Untitled') }}</div>
                                        <div class="text-sm text-secondary-foreground">
                                            {{ $post->author?->name ?? __('Author') }} &middot; {{ $categoryTranslation?->name ?? __('Category') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($post->is_featured)
                                        <span class="kt-badge kt-badge-warning">{{ __('Featured') }}</span>
                                    @endif
                                    <span class="kt-badge {{ $post->status === 'published' ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-secondary-foreground">{{ __('No items found') }}</div>
                        @endforelse
                    </div>

                    <div class="hidden grid gap-5" id="activity_comments">
                        @forelse ($recentComments as $comment)
                            @php $commentPostTranslation = $comment->post?->translationFor(config('blog.default_locale', 'en')); @endphp
                            <div class="flex items-start gap-3 rounded-2xl border border-border p-4">
                                <div class="kt-avatar size-11 shrink-0">
                                    <div class="kt-avatar-image">
                                        <div class="size-11 rounded-full bg-gradient-to-br from-fuchsia-500 to-rose-500 text-white flex items-center justify-center font-semibold">
                                            {{ strtoupper(\Illuminate\Support\Str::substr($comment->user?->name ?? __('G'), 0, 1)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="grow">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="font-medium text-mono">{{ $comment->user?->name ?? __('Guest') }}</div>
                                        <span class="text-xs text-secondary-foreground">{{ $comment->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-sm text-secondary-foreground mt-2 line-clamp-2">
                                        {{ \Illuminate\Support\Str::limit($comment->content, 120) }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-3">
                                        <span class="kt-badge kt-badge-outline kt-badge-primary">
                                            {{ __('On') }}: {{ $commentPostTranslation?->title ?? __('Untitled') }}
                                        </span>
                                        @if ($comment->parent_id)
                                            <span class="kt-badge kt-badge-outline kt-badge-secondary">{{ __('Reply') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-secondary-foreground">{{ __('No items found') }}</div>
                        @endforelse
                    </div>

                    <div class="hidden grid gap-5" id="activity_users">
                        @forelse ($recentMembers as $user)
                            @php
                                $initial = strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 1));
                                $roleLabel = match ($user->role) {
                                    \App\Models\User::ROLE_ADMIN => __('Admin'),
                                    \App\Models\User::ROLE_EDITOR => __('Editor'),
                                    \App\Models\User::ROLE_AUTHOR => __('Author'),
                                    default => __('Viewer'),
                                };
                            @endphp
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-border p-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-11 rounded-full bg-slate-900 text-white flex items-center justify-center font-semibold">
                                        {{ $initial }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-mono">{{ $user->name }}</div>
                                        <div class="text-sm text-secondary-foreground">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="kt-badge kt-badge-outline kt-badge-primary">{{ $roleLabel }}</span>
                                    <span class="kt-badge {{ $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                        {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-secondary-foreground">{{ __('No items found') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-5">
                <div class="kt-card overflow-hidden border-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 text-white">
                    <div class="kt-card-content p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.25em] text-white/60">{{ __('Operational note') }}</p>
                                <h3 class="text-2xl font-semibold mt-2">{{ __('Optimized for scale') }}</h3>
                            </div>
                            <div class="kt-menu" data-kt-menu="true">
                                <div class="kt-menu-item kt-menu-item-dropdown" data-kt-menu-item-offset="0, 10px" data-kt-menu-item-placement="bottom-end" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click">
                                    <button class="kt-menu-toggle kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost text-white">
                                        <i class="ki-filled ki-dots-vertical"></i>
                                    </button>
                                    <div class="kt-menu-dropdown kt-menu-default w-full max-w-[175px]" data-kt-menu-dismiss="true">
                                        <div class="kt-menu-item">
                                            <a class="kt-menu-link" href="{{ route('admin.posts.create') }}">
                                                <span class="kt-menu-icon"><i class="ki-filled ki-plus"></i></span>
                                                <span class="kt-menu-title">{{ __('New post') }}</span>
                                            </a>
                                        </div>
                                        <div class="kt-menu-item">
                                            <a class="kt-menu-link" href="{{ route('admin.members.index') }}">
                                                <span class="kt-menu-icon"><i class="ki-filled ki-users"></i></span>
                                                <span class="kt-menu-title">{{ __('View members') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-sm text-white/75 mt-3">
                            {{ __('This dashboard uses cached analytics, lightweight queries, and live activity blocks inspired by the demo9 profile templates.') }}
                        </p>
                        <div class="grid sm:grid-cols-2 gap-4 mt-6">
                            <div class="rounded-2xl bg-white/10 border border-white/10 p-4">
                                <div class="text-sm text-white/70">{{ __('Verified members') }}</div>
                                <div class="text-2xl font-semibold">{{ $totals['verifiedMembers'] ?? 0 }}</div>
                            </div>
                            <div class="rounded-2xl bg-white/10 border border-white/10 p-4">
                                <div class="text-sm text-white/70">{{ __('Pending members') }}</div>
                                <div class="text-2xl font-semibold">{{ $totals['pendingMembers'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-card">
                    <div class="kt-card-header">
                        <div>
                            <h3 class="kt-card-title">{{ __('Team snapshot') }}</h3>
                            <div class="text-sm text-secondary-foreground">{{ __('Role mix and newest registrations.') }}</div>
                        </div>
                    </div>
                    <div class="kt-card-content p-5 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($roleBreakdown as $item)
                                <div class="rounded-2xl bg-slate-50 p-4">
                                    <div class="text-sm text-secondary-foreground">{{ $item['label'] }}</div>
                                    <div class="mt-1 text-2xl font-semibold text-mono">{{ $item['count'] }}</div>
                                    <div class="mt-3">
                                        <span class="kt-badge {{ $roleToneClasses[$item['tone']] ?? 'kt-badge-secondary' }}">
                                            {{ __('Role') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="rounded-2xl border border-border overflow-hidden">
                            @foreach ($recentMembers->take(4) as $user)
                                @php
                                    $initial = strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 1));
                                    $roleLabel = match ($user->role) {
                                        \App\Models\User::ROLE_ADMIN => __('Admin'),
                                        \App\Models\User::ROLE_EDITOR => __('Editor'),
                                        \App\Models\User::ROLE_AUTHOR => __('Author'),
                                        default => __('Viewer'),
                                    };
                                @endphp
                                <div class="flex items-center justify-between gap-3 p-4 {{ $loop->first ? '' : 'border-t border-border' }}">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-gradient-to-br from-indigo-500 to-sky-500 text-white flex items-center justify-center font-semibold">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-mono">{{ $user->name }}</div>
                                            <div class="text-xs text-secondary-foreground">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="kt-badge kt-badge-outline kt-badge-primary">{{ $roleLabel }}</span>
                                        <span class="kt-badge {{ $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                            {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] gap-5 lg:gap-7.5">
            <div class="kt-card">
                <div class="kt-card-header">
                    <div>
                        <h3 class="kt-card-title">{{ __('Top categories') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Categories ranked by post count.') }}</div>
                    </div>
                </div>
                <div class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable-x-auto">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Posts') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topCategories as $category)
                                    @php $translation = $category->translationFor(config('blog.default_locale', 'en')); @endphp
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
                                        <td colspan="3" class="text-center text-secondary-foreground py-8">{{ __('No items found') }}</td>
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
                        <h3 class="kt-card-title">{{ __('Recent posts') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Most recently published items.') }}</div>
                    </div>
                </div>
                <div class="kt-card-content p-5 space-y-4">
                    @forelse ($recentPosts as $post)
                        @php $translation = $post->translationFor(config('blog.default_locale', 'en')); @endphp
                        <div class="flex items-start justify-between gap-4 rounded-2xl border border-border p-4">
                            <div>
                                <div class="font-medium text-mono">{{ $translation?->title ?? __('Untitled') }}</div>
                                <div class="text-sm text-secondary-foreground">
                                    {{ $post->author?->name ?? __('Author') }} &middot; {{ optional($post->published_at)->format('M d, Y') ?? __('Draft') }}
                                </div>
                            </div>
                            <span class="kt-badge {{ $post->status === 'published' ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-sm text-secondary-foreground">{{ __('No items found') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

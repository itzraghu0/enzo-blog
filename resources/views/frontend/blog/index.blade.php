@extends('frontend.blog.layout')
@section('title', config('app.name', 'Blog'))
@section('metaDescription', __('A multilingual blog with authors, categories, media, SEO metadata, and locale-aware content.'))

@php
    $entries = $posts->getCollection();
    $featuredPost = $entries->first();
    $spotlightPosts = $entries->slice(1, 4)->values();

    $itemList = $entries->map(function ($post, $position) use ($locale) {
        $translation = $post->translationFor($locale);

        return [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'url' => route('blog.show', $translation?->slug ?? $post->id),
            'name' => $translation?->title ?? __('Untitled'),
        ];
    })->values()->all();

    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => route('blog.index', ['locale' => $locale]) . '#organization',
                'name' => config('app.name', 'Blog'),
                'url' => url()->current(),
            ],
            [
                '@type' => 'WebSite',
                '@id' => route('blog.index', ['locale' => $locale]) . '#website',
                'name' => config('app.name', 'Blog'),
                'url' => url()->current(),
            ],
            [
                '@type' => 'CollectionPage',
                '@id' => url()->current() . '#collection',
                'name' => config('app.name', 'Blog'),
                'url' => url()->current(),
                'description' => __('A multilingual blog with authors, categories, media, SEO metadata, and locale-aware content.'),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
                    'numberOfItems' => $posts->total(),
                    'itemListElement' => $itemList,
                ],
            ],
        ],
    ];
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="editorial-shell space-y-8">
        <section class="grid gap-6 lg:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.55fr)]">
            <article class="editorial-card overflow-hidden">
                <a href="{{ $featuredPost ? route('blog.show', $featuredPost->translationFor($locale)?->slug ?? $featuredPost->id) : '#' }}" class="block">
                    <div class="relative aspect-[16/10] bg-[color:var(--surface-3)]">
                        @if ($featuredPost && $featuredPost->previewMedia)
                            <img src="{{ $featuredPost->previewMedia->url() }}" alt="{{ $featuredPost->translationFor($locale)?->preview_image_alt ?? $featuredPost->translationFor($locale)?->title ?? '' }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-[color:var(--surface-2)] to-[color:var(--surface-3)]">
                                <span class="text-sm font-semibold uppercase tracking-[0.25em] text-[color:var(--muted)]">{{ __('Featured story') }}</span>
                            </div>
                        @endif

                        @if ($featuredPost?->is_featured)
                            <div class="absolute right-4 top-4 rounded-full bg-rose-600 px-3 py-1 text-xs font-bold text-white shadow-lg">
                                {{ __('Featured') }}
                            </div>
                        @endif
                    </div>
                </a>

                <div class="p-6 md:p-8">
                    <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">
                        <span>{{ __('Hero story') }}</span>
                        <span>&middot;</span>
                        <span>{{ $posts->total() }} {{ __('articles') }}</span>
                    </div>

                    <h1 class="mt-4 text-4xl font-extrabold tracking-[-0.03em] text-[color:var(--text)] md:text-5xl">
                        {{ $featuredPost ? ($featuredPost->translationFor($locale)?->title ?? __('Untitled')) : __('No published posts yet.') }}
                    </h1>

                    <p class="mt-4 max-w-2xl text-base leading-8 text-[color:var(--muted)]">
                        {{ $featuredPost ? ($featuredPost->translationFor($locale)?->excerpt ?? __('A multilingual editorial system for readers and authors.')) : __('Publish your first article to unlock the homepage layout.') }}
                    </p>

                    @if ($featuredPost)
                        <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-[color:var(--muted)]">
                            <span class="editorial-chip editorial-chip-primary">{{ $featuredPost->author?->name ?? __('Author') }}</span>
                            <span>{{ optional($featuredPost->published_at)->format('M d, Y') ?? __('Draft') }}</span>
                            <span>&middot;</span>
                            <span>{{ $featuredPost->categories->count() }} {{ __('categories') }}</span>
                        </div>
                    @endif

                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($featuredCategories->take(4) as $category)
                            @php $translation = $category->translationFor($locale); @endphp
                            <a href="{{ route('blog.category', $translation?->slug ?? $category->id) }}" class="editorial-chip">
                                {{ $translation?->name ?? __('Category') }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </article>

            <aside class="space-y-6">
                <div class="editorial-card p-6">
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Quick facts') }}</div>
                    <dl class="mt-5 grid grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-[color:var(--surface-2)] p-4">
                            <dt class="text-sm text-[color:var(--muted)]">{{ __('Total entries') }}</dt>
                            <dd class="mt-2 text-3xl font-extrabold tracking-[-0.03em]">{{ $posts->total() }}</dd>
                        </div>
                        <div class="rounded-2xl bg-[color:var(--surface-2)] p-4">
                            <dt class="text-sm text-[color:var(--muted)]">{{ __('Categories') }}</dt>
                            <dd class="mt-2 text-3xl font-extrabold tracking-[-0.03em]">{{ $featuredCategories->count() }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 rounded-2xl border border-dashed border-[color:var(--border)] bg-[color:var(--surface-2)] p-4">
                        <p class="text-sm leading-7 text-[color:var(--muted)]">
                            {{ __('Everything is locale-aware, service-backed, and ready to power API responses with the same content rules.') }}
                        </p>
                    </div>
                </div>

                <div class="editorial-card p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Trending filters') }}</div>
                            <div class="mt-1 text-lg font-bold text-[color:var(--text)]">{{ __('Refine the feed') }}</div>
                        </div>
                        <span class="editorial-chip editorial-chip-primary">{{ __('Live') }}</span>
                    </div>

                    <form method="GET" action="{{ route('blog.index') }}" class="mt-5 space-y-4">
                        <input type="hidden" name="locale" value="{{ $locale }}">

                        <div class="grid gap-4">
                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[color:var(--muted)]">{{ __('Sort') }}</label>
                                <select name="sort" onchange="this.form.submit()" class="editorial-select">
                                    @foreach ($sortOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['sort'] ?? 'recent_desc') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[color:var(--muted)]">{{ __('Month') }}</label>
                                <select name="month" onchange="this.form.submit()" class="editorial-select">
                                    <option value="">{{ __('All months') }}</option>
                                    @foreach ($availableMonths as $month)
                                        <option value="{{ $month }}" @selected(($filters['month'] ?? '') === $month)>
                                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[color:var(--muted)]">{{ __('Category') }}</label>
                                <select name="category_id" onchange="this.form.submit()" class="editorial-select">
                                    <option value="">{{ __('All categories') }}</option>
                                    @foreach ($featuredCategories as $category)
                                        @php $translation = $category->translationFor($locale); @endphp
                                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                            {{ $translation?->name ?? __('Category') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-[color:var(--muted)]">{{ __('Per page') }}</label>
                                <select name="per_page" onchange="this.form.submit()" class="editorial-select">
                                    @foreach ($perPageOptions as $count)
                                        <option value="{{ $count }}" @selected((int) ($filters['per_page'] ?? 20) === $count)>{{ $count }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <button type="submit" class="editorial-button editorial-button-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('blog.index', ['locale' => $locale]) }}" class="editorial-button editorial-button-secondary">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>
            </aside>
        </section>

        <section id="latest" class="space-y-5">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Latest feed') }}</div>
                    <h2 class="mt-2 text-2xl font-extrabold tracking-[-0.03em]">{{ __('Stories with editorial depth') }}</h2>
                </div>
                <div class="text-sm text-[color:var(--muted)]">
                    {{ __('Total entries') }}: <span class="font-bold text-[color:var(--text)]">{{ $posts->total() }}</span>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">
                @forelse ($spotlightPosts as $index => $post)
                    @php
                        $translation = $post->translationFor($locale);
                        $leadCategory = $post->categories->first()?->translationFor($locale);
                        $cardClass = match ($index) {
                            0 => 'lg:col-span-7',
                            1 => 'lg:col-span-5',
                            2 => 'lg:col-span-4',
                            3 => 'lg:col-span-8',
                            default => 'lg:col-span-6',
                        };
                    @endphp
                    <article class="editorial-card overflow-hidden {{ $cardClass }}">
                        <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="block">
                            <div class="relative aspect-[16/10] bg-[color:var(--surface-3)]">
                                @if ($post->previewMedia)
                                    <img src="{{ $post->previewMedia->url() }}" alt="{{ $translation?->preview_image_alt ?? $translation?->title ?? '' }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-[color:var(--muted)]">
                                        {{ __('No image') }}
                                    </div>
                                @endif
                            </div>
                        </a>

                        <div class="p-6">
                            <div class="flex items-center justify-between gap-4 text-xs font-semibold uppercase tracking-[0.22em] text-[color:var(--muted)]">
                                <span>{{ $leadCategory?->name ?? __('Category') }}</span>
                                <span>{{ optional($post->published_at)->format('M d, Y') ?? __('Draft') }}</span>
                            </div>

                            <h3 class="mt-4 text-2xl font-bold tracking-[-0.03em] text-[color:var(--text)]">
                                <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="hover:text-[color:var(--primary)]">
                                    {{ $translation?->title ?? __('Untitled') }}
                                </a>
                            </h3>

                            <p class="mt-3 text-sm leading-7 text-[color:var(--muted)]">
                                {{ \Illuminate\Support\Str::limit(strip_tags($translation?->excerpt ?? ''), 180) }}
                            </p>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <span class="editorial-chip editorial-chip-primary">{{ $post->author?->name ?? __('Author') }}</span>
                                @foreach ($post->categories->take(2) as $category)
                                    @php $categoryTranslation = $category->translationFor($locale); @endphp
                                    <a href="{{ route('blog.category', $categoryTranslation?->slug ?? $category->id) }}" class="editorial-chip">
                                        {{ $categoryTranslation?->name ?? __('Category') }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="editorial-card col-span-full p-10 text-center text-[color:var(--muted)]">
                        {{ __('No published posts yet.') }}
                    </div>
                @endforelse
            </div>

            <div class="pt-3">
                {{ $posts->links() }}
            </div>
        </section>

        <section id="categories" class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="editorial-card p-6 md:p-8">
                <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Categories') }}</div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($featuredCategories as $category)
                        @php $translation = $category->translationFor($locale); @endphp
                        <a href="{{ route('blog.category', $translation?->slug ?? $category->id) }}" class="editorial-chip">
                            {{ $translation?->name ?? __('Category') }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="editorial-card overflow-hidden">
                <div class="bg-[color:var(--surface-2)] p-6 md:p-8">
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Newsletter') }}</div>
                    <h3 class="mt-3 text-2xl font-extrabold tracking-[-0.03em]">{{ __('Master the pulse of language.') }}</h3>
                    <p class="mt-3 text-sm leading-7 text-[color:var(--muted)]">
                        {{ __('Join readers, authors, and editors receiving curated multilingual stories and content updates.') }}
                    </p>

                    <form class="mt-5 flex flex-col gap-3 sm:flex-row">
                        <input type="email" class="editorial-input flex-1" placeholder="{{ __('Enter your email') }}">
                        <button type="submit" class="editorial-button editorial-button-primary">{{ __('Subscribe') }}</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

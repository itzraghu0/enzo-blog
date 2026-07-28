@extends('frontend.blog.layout')
@section('title', config('app.name', 'Blog'))
@section('metaDescription', __('A multilingual blog with authors, categories, media, SEO metadata, and locale-aware content.'))

@php
    $itemList = $posts->getCollection()->map(function ($post, $position) use ($locale) {
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
        '@type' => 'CollectionPage',
        'name' => config('app.name', 'Blog'),
        'url' => url()->current(),
        'description' => __('A multilingual blog with authors, categories, media, SEO metadata, and locale-aware content.'),
        'mainEntity' => [
            '@type' => 'ItemList',
            'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
            'numberOfItems' => $posts->total(),
            'itemListElement' => $itemList,
        ],
    ];
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <div class="space-y-8">
        <div class="rounded-[2rem] bg-white border border-slate-200 p-6 md:p-8 shadow-lg shadow-slate-900/5">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div class="max-w-3xl">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500 mb-3">{{ __('Featured publishing') }}</p>
                    <h1 class="text-4xl md:text-5xl font-semibold leading-tight text-slate-900">{{ __('Stories, categories, and media in one multilingual grid.') }}</h1>
                    <p class="mt-4 text-slate-600">{{ __('Filter by month, category, sort order, and page size. Every card uses the same service-backed content pipeline.') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="text-sm text-slate-500">{{ __('Total entries') }}</div>
                    <div class="text-3xl font-semibold text-slate-900">{{ $posts->total() }}</div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('blog.index') }}" class="rounded-[2rem] bg-white border border-slate-200 p-4 md:p-5 shadow-lg shadow-slate-900/5">
            <input type="hidden" name="locale" value="{{ $locale }}">
            <div class="grid md:grid-cols-4 xl:grid-cols-5 gap-4 items-end">
                <label class="block">
                    <span class="block text-sm font-medium text-slate-600 mb-2">{{ __('Sort') }}</span>
                    <select name="sort" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900">
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'recent_desc') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="block text-sm font-medium text-slate-600 mb-2">{{ __('Month') }}</span>
                    <select name="month" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900">
                        <option value="">{{ __('All months') }}</option>
                        @foreach ($availableMonths as $month)
                            <option value="{{ $month }}" @selected(($filters['month'] ?? '') === $month)>
                                {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="block text-sm font-medium text-slate-600 mb-2">{{ __('Category') }}</span>
                    <select name="category_id" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900">
                        <option value="">{{ __('All categories') }}</option>
                        @foreach ($featuredCategories as $category)
                            @php $translation = $category->translationFor($locale); @endphp
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                {{ $translation?->name ?? __('Category') }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="block text-sm font-medium text-slate-600 mb-2">{{ __('Per page') }}</span>
                    <select name="per_page" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900">
                        @foreach ($perPageOptions as $count)
                            <option value="{{ $count }}" @selected((int) ($filters['per_page'] ?? 20) === $count)>
                                {{ $count }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-4 py-3 rounded-xl bg-slate-900 text-white text-sm font-medium">
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('blog.index', ['locale' => $locale]) }}" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-700 text-sm font-medium">
                        {{ __('Reset') }}
                    </a>
                </div>
            </div>
        </form>

        <div class="grid lg:grid-cols-[minmax(0,1fr)_280px] gap-8 items-start">
            <section class="space-y-6">
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse ($posts as $post)
                        @php
                            $translation = $post->translationFor($locale);
                            $categoryTranslation = $post->categories->first()?->translationFor($locale);
                        @endphp
                        <article class="group rounded-[2rem] bg-white border border-slate-200 overflow-hidden shadow-lg shadow-slate-900/5 transition hover:-translate-y-1 hover:shadow-xl">
                            <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="block">
                                <div class="relative aspect-[4/3] bg-slate-100">
                                    @if ($post->previewMedia)
                                        <img src="{{ $post->previewMedia->url() }}"
                                            alt="{{ $translation?->preview_image_alt ?? $translation?->title ?? '' }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                            <span class="text-sm uppercase tracking-[0.2em]">{{ __('No image') }}</span>
                                        </div>
                                    @endif

                                    @if ($post->is_featured)
                                        <span class="absolute top-4 right-4 rounded-full bg-rose-600 px-3 py-1 text-xs font-semibold text-white">
                                            {{ __('Featured') }}
                                        </span>
                                    @endif
                                </div>
                            </a>

                            <div class="p-6 space-y-4">
                                <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.2em] text-slate-500">
                                    <span>{{ $post->author?->name ?? __('Author') }}</span>
                                    <span>&middot;</span>
                                    <span>{{ optional($post->published_at)->format('M d, Y') ?? __('Draft') }}</span>
                                </div>

                                <h2 class="text-xl font-semibold leading-snug text-slate-900">
                                    <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="hover:text-slate-600">
                                        {{ $translation?->title ?? __('Untitled') }}
                                    </a>
                                </h2>

                                <p class="text-sm text-slate-600 line-clamp-3">
                                    {{ $translation?->excerpt ?? '' }}
                                </p>

                                <div class="flex flex-wrap gap-2">
                                    @foreach ($post->categories->take(3) as $category)
                                        @php $itemTranslation = $category->translationFor($locale); @endphp
                                        <a href="{{ route('blog.category', $itemTranslation?->slug ?? $category->id) }}" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                            {{ $itemTranslation?->name ?? __('Category') }}
                                        </a>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-sm text-slate-500">
                                        {{ $categoryTranslation?->name ?? __('Blog post') }}
                                    </span>
                                    <a href="{{ route('blog.show', $translation?->slug ?? $post->id) }}" class="text-sm font-medium text-slate-900 hover:text-slate-600">
                                        {{ __('Read more') }}
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-[2rem] bg-white border border-slate-200 p-10 text-slate-500">
                            {{ __('No published posts yet.') }}
                        </div>
                    @endforelse
                </div>

                <div class="pt-3">
                    {{ $posts->links() }}
                </div>
            </section>

            <aside class="space-y-6 sticky top-6">
                <div class="rounded-[2rem] bg-white border border-slate-200 p-6 shadow-lg shadow-slate-900/5">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 mb-4">{{ __('Categories') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($featuredCategories as $category)
                            @php $translation = $category->translationFor($locale); @endphp
                            <a href="{{ route('blog.category', $translation?->slug ?? $category->id) }}" class="rounded-full border border-slate-200 px-3 py-2 text-slate-700">
                                {{ $translation?->name ?? __('Category') }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection

@extends('frontend.blog.layout')
@section('title', $translation?->seo_title ?? $translation?->name ?? __('Category'))
@section('metaDescription', $translation?->meta_description ?? '')

@php
    $itemList = $posts->getCollection()->map(function ($post, $position) use ($locale) {
        $postTranslation = $post->translationFor($locale);

        return [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'url' => route('blog.show', $postTranslation?->slug ?? $post->id),
            'name' => $postTranslation?->title ?? __('Untitled'),
        ];
    })->values()->all();

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'CollectionPage',
        'name' => $translation?->name ?? __('Category'),
        'url' => url()->current(),
        'description' => $translation?->description ?? '',
        'mainEntity' => [
            '@type' => 'ItemList',
            'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
            'numberOfItems' => count($itemList),
            'itemListElement' => $itemList,
        ],
    ];
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="max-w-5xl mx-auto space-y-8">
        <div class="rounded-3xl bg-white border border-slate-200 p-8 shadow-lg shadow-slate-900/5">
            <p class="uppercase tracking-[0.3em] text-xs text-slate-500 mb-3">{{ __('Category') }}</p>
            <h1 class="text-4xl md:text-5xl font-semibold">{{ $translation?->name ?? __('Category') }}</h1>
            <p class="mt-4 text-slate-600 max-w-3xl">{{ $translation?->description ?? '' }}</p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            @forelse ($posts as $post)
                @php $postTranslation = $post->translationFor($locale); @endphp
                <article class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-lg shadow-slate-900/5">
                    @if ($post->previewMedia)
                        <a href="{{ route('blog.show', $postTranslation?->slug ?? $post->id) }}">
                            <img src="{{ $post->previewMedia->url() }}" alt="{{ $postTranslation?->preview_image_alt ?? $postTranslation?->title ?? '' }}" class="w-full h-56 object-cover">
                        </a>
                    @endif
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold leading-tight">
                            <a href="{{ route('blog.show', $postTranslation?->slug ?? $post->id) }}">{{ $postTranslation?->title ?? __('Untitled') }}</a>
                        </h2>
                        <p class="mt-3 text-slate-600 line-clamp-3">{{ $postTranslation?->excerpt ?? '' }}</p>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl bg-white border border-slate-200 p-10 text-slate-500">
                    {{ __('No published posts in this category yet.') }}
                </div>
            @endforelse
        </div>

        <div>{{ $posts->links() }}</div>
    </section>
@endsection

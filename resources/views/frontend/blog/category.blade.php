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
        '@graph' => [
            [
                '@type' => 'CollectionPage',
                '@id' => url()->current() . '#collection',
                'name' => $translation?->name ?? __('Category'),
                'url' => url()->current(),
                'description' => $translation?->description ?? '',
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'itemListOrder' => 'https://schema.org/ItemListOrderDescending',
                    'numberOfItems' => count($itemList),
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
    <section class="editorial-shell space-y-8">
        <div class="editorial-card overflow-hidden">
            <div class="bg-[linear-gradient(135deg,rgba(0,74,198,0.10),rgba(255,255,255,0.00))] p-6 md:p-8">
                <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">{{ __('Category') }}</div>
                <h1 class="mt-3 text-4xl font-extrabold tracking-[-0.04em] text-[color:var(--text)] md:text-5xl">
                    {{ $translation?->name ?? __('Category') }}
                </h1>
                <p class="mt-4 max-w-3xl text-base leading-8 text-[color:var(--muted)]">
                    {{ $translation?->description ?? '' }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @forelse ($posts as $post)
                @php
                    $postTranslation = $post->translationFor($locale);
                @endphp
                <article class="editorial-card overflow-hidden">
                    <a href="{{ route('blog.show', $postTranslation?->slug ?? $post->id) }}" class="block">
                        <div class="aspect-[16/10] bg-[color:var(--surface-3)]">
                            @if ($post->previewMedia)
                                <img src="{{ $post->previewMedia->url() }}" alt="{{ $postTranslation?->preview_image_alt ?? $postTranslation?->title ?? '' }}" class="h-full w-full object-cover">
                            @endif
                        </div>
                    </a>
                    <div class="p-6">
                        <div class="editorial-chip editorial-chip-primary">{{ $post->author?->name ?? __('Author') }}</div>
                        <h2 class="mt-4 text-2xl font-bold tracking-[-0.03em] text-[color:var(--text)]">
                            <a href="{{ route('blog.show', $postTranslation?->slug ?? $post->id) }}" class="hover:text-[color:var(--primary)]">
                                {{ $postTranslation?->title ?? __('Untitled') }}
                            </a>
                        </h2>
                        <p class="mt-3 text-sm leading-7 text-[color:var(--muted)]">
                            {{ \Illuminate\Support\Str::limit(strip_tags($postTranslation?->excerpt ?? ''), 180) }}
                        </p>
                    </div>
                </article>
            @empty
                <div class="editorial-card p-10 text-center text-[color:var(--muted)]">
                    {{ __('No published posts in this category yet.') }}
                </div>
            @endforelse
        </div>

        <div>
            {{ $posts->links() }}
        </div>
    </section>
@endsection

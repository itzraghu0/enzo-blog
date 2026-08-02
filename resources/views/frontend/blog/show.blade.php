@extends('frontend.blog.layout')
@section('title', $translation?->seo_title ?? ($translation?->title ?? config('app.name', 'Blog')))
@section('metaDescription', $translation?->meta_description ?? '')

@php
    $categoryNames = $post->categories
        ->map(function ($category) use ($locale) {
            return $category->translationFor($locale)?->name ?? __('Category');
        })
        ->values()
        ->all();

    $postSlug = $translation?->slug ?? $post->id;

    $structuredData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'BlogPosting',
                '@id' => url()->current() . '#article',
                'headline' => $translation?->title ?? __('Untitled'),
                'description' => $translation?->excerpt ?? '',
                'datePublished' => optional($post->published_at)?->toAtomString(),
                'dateModified' => optional($post->updated_at)?->toAtomString(),
                'inLanguage' => $locale,
                'author' => [
                    '@type' => 'Person',
                    'name' => $post->author?->name ?? __('Author'),
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => config('app.name', 'Blog'),
                ],
                'mainEntityOfPage' => url()->current(),
                'articleSection' => $categoryNames,
            ],
            [
                '@type' => 'BreadcrumbList',
                '@id' => url()->current() . '#breadcrumbs',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => __('Home'),
                        'item' => route('blog.index', ['locale' => $locale]),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $translation?->title ?? __('Untitled'),
                        'item' => url()->current(),
                    ],
                ],
            ],
        ],
    ];

    if ($post->previewMedia) {
        $imageData = [
            '@type' => 'ImageObject',
            'url' => $post->previewMedia->url(),
            'name' => $post->previewMedia->title ?? $post->previewMedia->original_name,
            'caption' => $post->previewMedia->caption,
            'description' =>
                $post->previewMedia->geo_summary ??
                ($post->previewMedia->aeo_summary ?? $post->previewMedia->relevance_notes),
            'keywords' => $post->previewMedia->seo_keywords,
        ];

        $optimizationProperties = array_filter([
            $post->previewMedia->seo_keywords
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'SEO keywords',
                    'value' => $post->previewMedia->seo_keywords,
                ]
                : null,
            $post->previewMedia->hashtags
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'SEO hashtags',
                    'value' => $post->previewMedia->hashtags,
                ]
                : null,
            $post->previewMedia->aeo_summary
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'AEO summary',
                    'value' => $post->previewMedia->aeo_summary,
                ]
                : null,
            $post->previewMedia->aeo_questions
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'AEO questions',
                    'value' => implode(' | ', $post->previewMedia->aeo_questions),
                ]
                : null,
            $post->previewMedia->geo_summary
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'GEO summary',
                    'value' => $post->previewMedia->geo_summary,
                ]
                : null,
            $post->previewMedia->geo_entities
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'GEO entities',
                    'value' => $post->previewMedia->geo_entities,
                ]
                : null,
            $post->previewMedia->geo_prompts
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'GEO prompts',
                    'value' => $post->previewMedia->geo_prompts,
                ]
                : null,
            $post->previewMedia->geo_context
                ? [
                    '@type' => 'PropertyValue',
                    'name' => 'GEO context',
                    'value' => $post->previewMedia->geo_context,
                ]
                : null,
        ]);

        if ($optimizationProperties !== []) {
            $imageData['additionalProperty'] = array_values($optimizationProperties);
        }

        $structuredData['@graph'][0]['image'] = [array_filter($imageData)];
    }
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <article class="editorial-shell news-page">
        <div class="space-y-8">
            <header class="space-y-5 border-b border-[color:var(--border)] pb-8">
                <div
                    class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">
                    @foreach ($post->categories->take(3) as $category)
                        @php $categoryTranslation = $category->translationFor($locale); @endphp
                        <a href="{{ route('blog.category', $categoryTranslation?->slug ?? $category->id) }}"
                            class="editorial-chip">
                            {{ $categoryTranslation?->name ?? __('Category') }}
                        </a>
                    @endforeach
                </div>

                <h1 class="news-title max-w-5xl">
                    {{ $translation?->title ?? __('Untitled') }}
                </h1>

                <p class="max-w-4xl text-base leading-8 text-[color:var(--muted)]">
                    {!! $translation?->excerpt ?? '' !!}
                </p>

                <div class="flex flex-wrap items-center gap-3 text-sm text-[color:var(--muted)]">
                    <span class="editorial-chip editorial-chip-primary">{{ $post->author?->name ?? __('Author') }}</span>
                    <span>{{ optional($post->published_at)->format('M d, Y') ?? __('Draft') }}</span>
                    <span>&middot;</span>
                    <span>{{ $post->categories->count() }} {{ __('categories') }}</span>
                </div>
            </header>

            @if ($post->previewMedia)
                <figure class="overflow-hidden">
                    <img src="{{ $post->previewMedia->variantUrl('lg') ?? $post->previewMedia->url() }}"
                        alt="{{ $translation?->preview_image_alt ?? ($translation?->title ?? '') }}"
                        class="h-auto w-full object-cover"
                        loading="eager"
                        decoding="async">
                </figure>
            @endif

            <section>
                <div class="article-content max-w-none">
                    {!! $translation?->content !!}
                </div>

                <div class="mt-8 flex flex-wrap gap-2 border-t border-[color:var(--border)] pt-6">
                    @foreach ($post->categories as $category)
                        @php $categoryTranslation = $category->translationFor($locale); @endphp
                        <a href="{{ route('blog.category', $categoryTranslation?->slug ?? $category->id) }}"
                            class="editorial-chip">
                            {{ $categoryTranslation?->name ?? __('Category') }}
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="space-y-5">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.24em] text-[color:var(--muted)]">
                            {{ __('Discussion') }}</div>
                        <h2 class="mt-2 text-2xl font-extrabold tracking-[-0.03em]">{{ __('Comments') }}</h2>
                    </div>
                    <div class="text-sm text-[color:var(--muted)]">{{ $comments->count() }} {{ __('comments') }}</div>
                </div>

                @auth
                    @if (auth()->user()->hasVerifiedEmail())
                        <form action="{{ route('blog.comments.store', $postSlug) }}" method="POST"
                            class="space-y-4 border-y border-[color:var(--border)] py-6">
                            @csrf
                            <div>
                                <label
                                    class="mb-2 block text-sm font-semibold text-[color:var(--muted)]">{{ __('Add a comment') }}</label>
                                <textarea name="content" rows="5" class="editorial-textarea" placeholder="{{ __('Write your comment here') }}">{{ old('content') }}</textarea>
                            </div>
                            <button type="submit"
                                class="editorial-button editorial-button-primary">{{ __('Post comment') }}</button>
                        </form>
                    @else
                        <div class="border-y border-amber-200 bg-amber-50/90 py-6 text-sm text-amber-900">
                            {{ __('Verify your email before commenting.') }}
                        </div>
                    @endif
                @else
                    <div class="border-y border-[color:var(--border)] py-6 text-sm leading-7 text-[color:var(--muted)]">
                        <a href="{{ route('login') }}" class="font-bold text-[color:var(--text)]">{{ __('Sign in') }}</a>
                        {{ __('or') }}
                        <a href="{{ route('register') }}"
                            class="font-bold text-[color:var(--text)]">{{ __('create an account') }}</a>
                        {{ __('to join the discussion.') }}
                    </div>
                @endauth

                <div class="space-y-4">
                    @forelse ($comments as $comment)
                        @include('frontend.blog.partials.comment', [
                            'comment' => $comment,
                            'slug' => $postSlug,
                        ])
                    @empty
                        <div class="border-y border-[color:var(--border)] py-6 text-[color:var(--muted)]">
                            {{ __('No comments yet.') }}
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </article>
@endsection

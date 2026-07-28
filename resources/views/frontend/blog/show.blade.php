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

    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
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
    ];

    if ($post->previewMedia) {
        $structuredData['image'] = [$post->previewMedia->url()];
    }
@endphp

@push('structured-data')
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <article class="max-w-4xl mx-auto space-y-8">
        <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-xl shadow-slate-900/5">
            @if ($post->previewMedia)
                <img src="{{ $post->previewMedia->url() }}"
                    alt="{{ $translation?->preview_image_alt ?? ($translation?->title ?? '') }}"
                    class="w-full h-[420px] object-cover">
            @endif

            <div class="p-8 md:p-12">
                <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.25em] text-slate-500">
                    <span>{{ $post->author?->name ?? __('Author') }}</span>
                    <span>&middot;</span>
                    <span>{{ optional($post->published_at)->format('M d, Y') ?? __('Draft') }}</span>
                </div>

                <h1 class="mt-4 text-4xl md:text-5xl font-semibold leading-tight">
                    {{ $translation?->title ?? __('Untitled') }}
                </h1>
                <p class="mt-5 text-lg text-slate-600 max-w-3xl">{{ $translation?->excerpt ?? '' }}</p>

                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($post->categories as $category)
                        @php $categoryTranslation = $category->translationFor($locale); @endphp
                        <a href="{{ route('blog.category', $categoryTranslation?->slug ?? $category->id) }}"
                            class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-sm">
                            {{ $categoryTranslation?->name ?? __('Category') }}
                        </a>
                    @endforeach
                </div>

                <div class="prose prose-slate max-w-none mt-10">
                    {!! $translation?->content !!}
                </div>
            </div>
        </div>

        <section class="space-y-6">
            <div class="flex items-center justify-between gap-4">
                <h2 class="text-2xl font-semibold">{{ __('Comments') }}</h2>
                <span class="text-sm text-slate-500">{{ $comments->count() }}</span>
            </div>

            @auth
                @if (auth()->user()->hasVerifiedEmail())
                    <form action="{{ route('blog.comments.store', $post->translationFor($locale)?->slug ?? $post->id) }}" method="POST" class="rounded-3xl border border-slate-200 bg-white p-6 space-y-4 shadow-lg shadow-slate-900/5">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">{{ __('Add a comment') }}</label>
                            <textarea name="content" rows="5" class="w-full rounded-2xl border border-slate-200 p-4" placeholder="{{ __('Write your comment here') }}">{{ old('content') }}</textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm">{{ __('Post comment') }}</button>
                    </form>
                @else
                    <div class="rounded-3xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
                        {{ __('Verify your email before commenting.') }}
                    </div>
                @endif
            @else
                <div class="rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-600 shadow-lg shadow-slate-900/5">
                    <a href="{{ route('login') }}" class="font-medium text-slate-900">{{ __('Sign in') }}</a>
                    {{ __('or') }}
                    <a href="{{ route('register') }}" class="font-medium text-slate-900">{{ __('create an account') }}</a>
                    {{ __('to join the discussion.') }}
                </div>
            @endauth

            <div class="space-y-4">
                @forelse ($comments as $comment)
                    @include('frontend.blog.partials.comment', ['comment' => $comment, 'slug' => $post->translationFor($locale)?->slug ?? $post->id])
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-slate-500">
                        {{ __('No comments yet.') }}
                    </div>
                @endforelse
            </div>
        </section>
    </article>
@endsection

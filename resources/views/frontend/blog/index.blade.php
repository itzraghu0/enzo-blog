@extends('frontend.blog.layout')
@section('title', __('Aktuelle Neuigkeiten'))
@section('metaDescription', __('Aktuelle Blogbeiträge mit Kategorien, Kommentaren und strukturierten Daten.'))

@php
    $entries = $posts->getCollection();

    $itemList = $entries->map(function ($post, $position) use ($locale) {
        return [
            '@type' => 'ListItem',
            'position' => $position + 1,
            'url' => $post->slug ? route('blog.show', $post->slug) : route('blog.index', ['locale' => $locale]),
            'name' => $post->title ?? __('Untitled'),
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
                'name' => __('Aktuelle Neuigkeiten'),
                'url' => url()->current(),
                'description' => __('Aktuelle Blogbeiträge mit Kategorien, Kommentaren und strukturierten Daten.'),
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
    <div class="editorial-shell news-page">
        <h1 class="news-title">{{ __('Aktuelle Neuigkeiten') }}</h1>

        <form method="GET" action="{{ route('blog.index') }}" class="mt-14">
            <input type="hidden" name="locale" value="{{ $locale }}">

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-[310px_175px_245px_185px_minmax(220px,1fr)] xl:items-center">
                <select name="sort" onchange="this.form.submit()" class="news-filter-select">
                    @foreach ($sortOptions as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['sort'] ?? 'recent_desc') === $value)>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="month" onchange="this.form.submit()" class="news-filter-select">
                    <option value="">{{ __('Alle Monate') }}</option>
                    @foreach ($availableMonths as $month)
                        <option value="{{ $month }}" @selected(($filters['month'] ?? '') === $month)>
                            {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>

                <select name="category_id" onchange="this.form.submit()" class="news-filter-select">
                    <option value="">{{ __('Alle Kategorien') }}</option>
                    @foreach ($featuredCategories as $category)
                        @php($categoryTranslation = $category->translationFor($locale))
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                            {{ $categoryTranslation?->name ?? __('Category') }}
                        </option>
                    @endforeach
                </select>

                <select name="per_page" onchange="this.form.submit()" class="news-filter-select">
                    @foreach ($perPageOptions as $count)
                        <option value="{{ $count }}" @selected((int) ($filters['per_page'] ?? 10) === $count)>{{ $count }}</option>
                    @endforeach
                </select>

                <div class="news-filter-total">
                    {{ __('Einträge insgesamt') }}: {{ $posts->total() }}
                </div>
            </div>
        </form>

        <div class="news-divider mt-6"></div>

        <div class="news-grid mt-10">
            <?php foreach ($entries as $entry): ?>
                <?php
                    $postUrl = $entry->slug ? route('blog.show', $entry->slug) : '#';
                    $excerpt = trim(strip_tags((string) $entry->excerpt));
                    $publishedDate = $entry->published_at
                        ? \Illuminate\Support\Carbon::parse($entry->published_at)->format('d.m.Y')
                        : '';
                    $previewAlt = $entry->preview_image_alt ?? $entry->title ?? __('Blog image');
                ?>

                <article class="news-card">
                    <?php if ($entry->preview_image_url): ?>
                        <a href="<?php echo e($postUrl); ?>" class="news-image">
                            <img
                                src="<?php echo e($entry->preview_image_sm_url ?? $entry->preview_image_url); ?>"
                                srcset="<?php echo e(collect([
                                    ($entry->preview_image_sm_url ?? null) ? $entry->preview_image_sm_url . ' 360w' : null,
                                    ($entry->preview_image_md_url ?? null) ? $entry->preview_image_md_url . ' 768w' : null,
                                    $entry->preview_image_url . ' 1200w',
                                ])->filter()->implode(', ')); ?>"
                                sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                alt="<?php echo e($previewAlt); ?>"
                                loading="lazy"
                                decoding="async">
                        </a>
                    <?php else: ?>
                        <a href="<?php echo e($postUrl); ?>" class="news-image news-image-placeholder" aria-label="<?php echo e($entry->title ?? __('Untitled')); ?>">
                            <span class="news-image-placeholder-icon">
                                <i class="fa-regular fa-image"></i>
                            </span>
                            <span><?php echo e(__('No image available')); ?></span>
                        </a>
                    <?php endif; ?>

                    <div class="news-meta">
                        <span><?php echo e($publishedDate); ?></span>
                        <span class="news-comment">
                            <i class="fa-solid fa-comments"></i>
                            <?php echo e($entry->comments_count ?? 0); ?>
                        </span>
                    </div>

                    <a href="<?php echo e($postUrl); ?>" class="news-post-title">
                        <?php echo e($entry->title ?? __('Untitled')); ?>
                    </a>

                    <p class="news-excerpt">
                        <?php echo e(\Illuminate\Support\Str::limit($excerpt, 90)); ?>
                    </p>

                    <a href="<?php echo e($postUrl); ?>" class="news-read-more"><?php echo e(__('Weiter')); ?> <span aria-hidden="true">-&gt;</span></a>
                </article>
            <?php endforeach; ?>

            @if ($entries->isEmpty())
                <div class="col-span-full py-12 text-lg">
                    {{ __('No published posts yet.') }}
                </div>
            @endif
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

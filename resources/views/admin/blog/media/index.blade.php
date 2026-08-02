@extends('layouts.admin.app')
@section('title', __('Media Library'))

@push('style')
    <link href="{{ url('assets/vendors/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Media Library') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Media') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.media.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus-circle text-lg me-1"></i>
                    {{ __('Add new') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid xl:grid-cols-3 gap-5 lg:gap-7.5 mb-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Total media') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Uploaded this month') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['thisMonth'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Public folder') }}</div>
                <div class="text-sm font-medium text-mono mt-1 break-all">
                    {{ $stats['publicFolder'] ?? 'public/media/blog' }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card kt-card-grid min-w-full overflow-hidden">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('All Media') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Showing 50 images first. Scroll to load more.') }}
                </div>
            </div>
            <a href="{{ route('admin.media.create') }}" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-plus-circle"></i>
                {{ __('Upload') }}
            </a>
        </div>
        <div class="kt-card-content p-5">
            <div class="pe-2" style="height: 700px; overflow-y: auto;" data-media-scroll-area>
                <div class="grid grid-cols-3 gap-2" data-media-grid>
                    @forelse ($mediaItems as $media)
                        <div class="rounded-2xl border border-border bg-background overflow-hidden shadow-sm"
                            data-media-card="{{ $media->id }}">
                            <img src="{{ $media->variantUrl('sm') ?? $media->url() }}"
                                alt="{{ $media->alt_text ?? ($media->original_name ?? '') }}"
                                class="h-44 w-full object-cover">
                            <div class="p-4 space-y-3">
                                <div>
                                    <div class="font-medium text-mono text-sm truncate">{{ $media->original_name }}</div>
                                    <div class="text-xs text-secondary-foreground truncate">{{ $media->path }}</div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($media->seo_keywords)
                                        <span class="kt-badge kt-badge-outline kt-badge-primary">{{ __('SEO') }}</span>
                                    @endif
                                    @if ($media->aeo_summary || $media->aeo_questions)
                                        <span class="kt-badge kt-badge-outline kt-badge-success">{{ __('AEO') }}</span>
                                    @endif
                                    @if ($media->geo_summary || $media->geo_entities || $media->geo_prompts || $media->geo_context)
                                        <span class="kt-badge kt-badge-outline kt-badge-warning">{{ __('GEO') }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                                    <a href="{{ route('admin.media.edit', $media) }}"
                                        class="kt-btn kt-btn-sm kt-btn-primary w-full justify-center sm:w-auto">
                                        {{ __('Edit') }}
                                    </a>
                                    <button type="button"
                                        class="kt-btn kt-btn-sm kt-btn-mono w-full justify-center sm:w-auto"
                                        data-copy-url="{{ $media->url() }}">
                                        {{ __('Copy URL') }}
                                    </button>
                                    <form action="{{ route('admin.media.destroy', $media) }}" method="POST"
                                        data-confirm-delete>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="kt-btn kt-btn-sm kt-btn-danger w-full justify-center sm:w-auto">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-secondary-foreground">
                            {{ __('No media uploaded yet.') }}
                        </div>
                    @endforelse
                </div>
                <div class="py-5 text-center text-sm text-secondary-foreground hidden" data-media-loading>
                    {{ __('Loading more images...') }}
                </div>
                <div class="py-5 text-center text-sm text-secondary-foreground {{ $mediaItems->hasMorePages() ? 'hidden' : '' }}"
                    data-media-complete>
                    {{ __('All images loaded.') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        jQuery(function($) {
            const $scrollArea = $('[data-media-scroll-area]');
            const $grid = $('[data-media-grid]');
            const $loading = $('[data-media-loading]');
            const $complete = $('[data-media-complete]');
            const csrfToken = $('meta[name="_token"]').attr('content') || '';

            let page = {{ $mediaItems->currentPage() }};
            let lastPage = {{ $mediaItems->lastPage() }};
            let isLoading = false;

            function mediaQueryData(nextPage) {
                const params = Object.fromEntries(new URLSearchParams(window.location.search));

                return {
                    ...params,
                    page: nextPage,
                    per_page: 50,
                    type: params.type || 'image',
                };
            }

            function escapeHtml(value) {
                return String(value || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function mediaCard(media) {
                const destroyUrl = @json(route('admin.media.destroy', ['media' => '__MEDIA_ID__'])).replace('__MEDIA_ID__', media.id);
                const editUrl = @json(route('admin.media.edit', ['media' => '__MEDIA_ID__'])).replace('__MEDIA_ID__', media.id);

                return `
                    <div class="rounded-2xl border border-border bg-background overflow-hidden shadow-sm" data-media-card="${media.id}">
                        <img src="${escapeHtml(media.thumbnail_url || media.url)}" alt="${escapeHtml(media.alt_text || media.original_name || '')}" class="h-44 w-full object-cover">
                        <div class="p-4 space-y-3">
                            <div>
                                <div class="font-medium text-mono text-sm truncate">${escapeHtml(media.original_name || media.filename || '')}</div>
                                <div class="text-xs text-secondary-foreground truncate">${escapeHtml(media.path || '')}</div>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                ${media.seo_keywords ? `<span class="kt-badge kt-badge-outline kt-badge-primary">${@json(__('SEO'))}</span>` : ''}
                                ${(media.aeo_summary || media.aeo_questions) ? `<span class="kt-badge kt-badge-outline kt-badge-success">${@json(__('AEO'))}</span>` : ''}
                                ${(media.geo_summary || media.geo_entities || media.geo_prompts || media.geo_context) ? `<span class="kt-badge kt-badge-outline kt-badge-warning">${@json(__('GEO'))}</span>` : ''}
                            </div>
                            <div class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                                <a href="${editUrl}" class="kt-btn kt-btn-sm kt-btn-primary w-full justify-center sm:w-auto">
                                    ${@json(__('Edit'))}
                                </a>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-mono w-full justify-center sm:w-auto" data-copy-url="${escapeHtml(media.url)}">
                                    ${@json(__('Copy URL'))}
                                </button>
                                <form action="${destroyUrl}" method="POST" data-confirm-delete>
                                    <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="kt-btn kt-btn-sm kt-btn-danger w-full justify-center sm:w-auto">
                                        ${@json(__('Delete'))}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
            }

            async function loadMore() {
                if (isLoading || page >= lastPage) {
                    $complete.toggleClass('hidden', page < lastPage);
                    return;
                }

                isLoading = true;
                $loading.removeClass('hidden');

                $.ajax({
                    url: @json(route('admin.media.index')),
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    data: mediaQueryData(page + 1),
                }).done(function(payload) {
                    page = payload.meta.current_page;
                    lastPage = payload.meta.last_page;
                    $grid.append(payload.data.map(mediaCard).join(''));
                    $complete.toggleClass('hidden', page < lastPage);
                }).fail(function() {
                    window.KTToast?.show({
                        message: @json(__('Unable to load more images.')),
                        class: 'bg-error text-white',
                    });
                }).always(function() {
                    isLoading = false;
                    $loading.addClass('hidden');
                });
            }

            function checkAndLoadMore() {
                const scrollArea = $scrollArea.get(0);

                if (!scrollArea) {
                    return;
                }

                const nearBottom = scrollArea.scrollTop + scrollArea.clientHeight >= scrollArea.scrollHeight - 80;

                if (nearBottom) {
                    loadMore();
                }
            }

            $scrollArea.on('scroll', checkAndLoadMore);

            $grid.on('click', '[data-copy-url]', function() {
                navigator.clipboard.writeText($(this).data('copy-url') || '');
            });

            $grid.on('submit', '[data-confirm-delete]', function(event) {
                if (!confirm(@json(__('Are you sure?')))) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush

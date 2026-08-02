@once
    <div class="kt-modal" data-kt-modal="true" id="admin_media_selector_modal" data-admin-media-selector-modal>
        <div class="kt-modal-content max-w-[1200px] top-[3%] max-h-[94%]">
            <div class="kt-modal-header">
                <div>
                    <h3 class="kt-modal-title">{{ __('Media Library') }}</h3>
                    <div class="text-sm text-secondary-foreground">
                        {{ __('Upload, search, and choose media.') }}
                    </div>
                </div>
                <button type="button" class="kt-modal-close" aria-label="{{ __('Close modal') }}"
                    data-kt-modal-dismiss="#admin_media_selector_modal" data-admin-media-selector-close>
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            <div class="grid gap-4 border-b border-border p-5 lg:grid-cols-[1fr_auto]">
                <label class="kt-input">
                    <i class="ki-filled ki-magnifier"></i>
                    <input type="search" data-admin-media-selector-search data-kt-modal-input-focus="true"
                        placeholder="{{ __('Search media') }}">
                </label>
                <form class="flex w-full flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center lg:w-auto"
                    data-admin-media-selector-upload>
                    <input type="file" name="file" class="kt-input w-full sm:max-w-64" accept="image/*" required>
                    <input type="text" name="alt_text" class="kt-input w-full sm:max-w-64"
                        placeholder="{{ __('Alt text') }}">
                    <button type="submit" class="kt-btn kt-btn-primary w-full justify-center sm:w-auto">
                        <i class="ki-filled ki-file-up"></i>
                        {{ __('Upload') }}
                    </button>
                </form>
            </div>

            <div class="kt-modal-body max-h-[70vh] overflow-y-auto p-5" data-admin-media-selector-scroll>
                <div class="grid grid-cols-3 gap-2" data-admin-media-selector-grid></div>
                <div class="py-5 text-center text-sm text-secondary-foreground hidden" data-admin-media-selector-loading>
                    {{ __('Loading media...') }}
                </div>
                <div class="py-5 text-center text-sm text-secondary-foreground hidden" data-admin-media-selector-empty>
                    {{ __('No media found.') }}
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            jQuery(function($) {
                const $modal = $('[data-admin-media-selector-modal]');
                const $grid = $('[data-admin-media-selector-grid]');
                const $search = $('[data-admin-media-selector-search]');
                const $scroll = $('[data-admin-media-selector-scroll]');
                const $loading = $('[data-admin-media-selector-loading]');
                const $empty = $('[data-admin-media-selector-empty]');
                const $uploadForm = $('[data-admin-media-selector-upload]');
                const csrfToken = $('meta[name="_token"]').attr('content') || '';

                let modalInstance = null;
                let selectedCallback = null;
                let selectorMode = 'default';
                let mediaPage = 1;
                let mediaLastPage = 1;
                let isLoading = false;
                let searchTimer = null;

                function escapeHtml(value) {
                    return String(value || '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function mediaCard(media) {
                    const isImage = String(media.mime_type || '').startsWith('image/');

                    return `
                        <button type="button"
                            class="text-left rounded-xl border border-border bg-background overflow-hidden transition hover:border-primary"
                            data-admin-media-selector-item
                            data-media='${escapeHtml(JSON.stringify(media))}'>
                            <div class="h-40 bg-muted/40">
                                ${isImage
                                    ? `<img src="${escapeHtml(media.thumbnail_url || media.url)}" alt="${escapeHtml(media.alt_text || media.original_name)}" class="h-full w-full object-cover">`
                                    : `<div class="flex h-full items-center justify-center text-secondary-foreground"><i class="ki-filled ki-file text-4xl"></i></div>`
                                }
                            </div>
                            <div class="p-3">
                                <div class="font-medium text-sm text-mono truncate">${escapeHtml(media.original_name || media.filename)}</div>
                                <div class="text-xs text-secondary-foreground truncate">${escapeHtml(media.path)}</div>
                                <div class="mt-3 kt-btn kt-btn-sm kt-btn-outline">${@json(__('Use Media'))}</div>
                            </div>
                        </button>
                    `;
                }

                function showModal() {
                    if (!modalInstance && window.KTModal && $modal.length) {
                        window.KTModal.createInstances();
                        modalInstance = window.KTModal.getInstance($modal[0]) || new window.KTModal($modal[0]);
                        modalInstance.on('hide', function() {
                            selectedCallback = null;
                        });
                    }

                    if (modalInstance) {
                        modalInstance.show();
                        return;
                    }

                    $modal.removeClass('hidden');
                }

                function hideModal() {
                    if (modalInstance) {
                        modalInstance.hide();
                        return;
                    }

                    $modal.addClass('hidden');
                }

                function loadMedia(page = 1, replace = false) {
                    if (isLoading) {
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
                        },
                        data: {
                            page,
                            per_page: 24,
                            type: 'image',
                            search: $search.val() || '',
                        },
                    }).done(function(payload) {
                        mediaPage = payload.meta.current_page;
                        mediaLastPage = payload.meta.last_page;

                        if (replace) {
                            $grid.empty();
                        }

                        $grid.append(payload.data.map(mediaCard).join(''));
                        $empty.toggleClass('hidden', $grid.children().length > 0);
                    }).fail(function() {
                        window.KTToast?.show({
                            message: @json(__('Unable to load media.')),
                            class: 'bg-error text-white',
                        });
                    }).always(function() {
                        isLoading = false;
                        $loading.addClass('hidden');
                    });
                }

                window.AdminMediaLibrary = {
                    open(options = {}) {
                        selectorMode = options.mode || 'default';
                        selectedCallback = typeof options.onSelect === 'function' ? options.onSelect : null;
                        showModal();
                        loadMedia(1, true);
                        setTimeout(function() {
                            $search.trigger('focus');
                        }, 0);
                    },
                    close() {
                        hideModal();
                        selectedCallback = null;
                    },
                };

                $('[data-admin-media-selector-close]').on('click', function() {
                    window.AdminMediaLibrary.close();
                });

                $grid.on('click', '[data-admin-media-selector-item]', function() {
                    const media = JSON.parse($(this).attr('data-media'));

                    if (selectedCallback) {
                        selectedCallback(media);
                    }

                    window.AdminMediaLibrary.close();
                });

                $search.on('input', function() {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function() {
                        loadMedia(1, true);
                    }, 300);
                });

                $scroll.on('scroll', function() {
                    const element = this;
                    const nearBottom = element.scrollTop + element.clientHeight >= element.scrollHeight - 200;

                    if (nearBottom && !isLoading && mediaPage < mediaLastPage) {
                        loadMedia(mediaPage + 1, false);
                    }
                });

                $uploadForm.on('submit', function(event) {
                    event.preventDefault();

                    const formData = new FormData(this);
                    formData.set('collection', selectorMode === 'preview' ? 'preview' : 'content');

                    $.ajax({
                        url: @json(route('admin.media.store')),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        headers: {
                            Accept: 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    }).done(function(payload) {
                        $uploadForm[0].reset();
                        $grid.prepend(mediaCard(payload.data));
                        $empty.addClass('hidden');

                        if (selectedCallback) {
                            selectedCallback(payload.data);
                        }

                        window.AdminMediaLibrary.close();
                    }).fail(function() {
                        window.KTToast?.show({
                            message: @json(__('Unable to upload media.')),
                            class: 'bg-error text-white',
                        });
                    });
                });
            });
        </script>
    @endpush
@endonce

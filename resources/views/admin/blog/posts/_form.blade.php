@php
    $defaultLocale = config('blog.default_locale', 'en');
    $locales = $locales ?? config('blog.supported_locales', [$defaultLocale]);
    $activeLocale = in_array($defaultLocale, $locales, true) ? $defaultLocale : $locales[0] ?? $defaultLocale;
@endphp

<form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="kt-card mt-3">
        <div class="kt-card-header">
            <h3 class="kt-card-title">{{ __('Post settings') }}</h3>
        </div>
        <div class="kt-card-content">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 p-3">
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Author') }}</label>
                    <div class="kt-form-control">
                        <select class="select2 kt-input w-full" name="user_id">
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ old('user_id', $post->user_id ?? auth()->id()) == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kt-form-description">{{ __('Choose the staff author for this post.') }}</div>
                    @error('user_id')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Status') }}</label>
                    <div class="kt-form-control">
                        <select class="select2 kt-input w-full" name="status">
                            @foreach (['draft', 'pending', 'published', 'scheduled', 'archived'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $post->status ?? 'draft') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kt-form-description">{{ __('Published posts are visible on the frontend.') }}</div>
                    @error('status')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Featured') }}</label>
                    <div class="kt-form-control">
                        <label class="kt-label">
                            <input class="kt-checkbox kt-checkbox-sm" type="checkbox" name="is_featured" value="1"
                                {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }} />
                            <span class="kt-checkbox-label">{{ __('Feature this post') }}</span>
                        </label>
                    </div>
                    <div class="kt-form-description">{{ __('Featured posts can be highlighted on the homepage.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Published At') }}</label>
                    <div class="kt-form-control">
                        <input type="text" name="published_at" class="kt-input w-full datepicker"
                            value="{{ old('published_at', optional($post->published_at ?? null)->format('Y-m-d H:i')) }}">
                    </div>
                    <div class="kt-form-description">{{ __('Set publish date/time for published or scheduled posts.') }}</div>
                    @error('published_at')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Categories') }}</label>
                    <div class="kt-form-control">
                        <select class="select2 kt-input w-full" name="category_ids[]" multiple>
                            @php
                                $selectedCategories = collect(
                                    old('category_ids', $post->categories?->pluck('id')->all() ?? []),
                                )
                                    ->map(fn($value) => (int) $value)
                                    ->all();
                            @endphp
                            @foreach ($categories as $category)
                                @php $categoryTranslation = $category->translationFor($defaultLocale); @endphp
                                <option value="{{ $category->id }}"
                                    {{ in_array($category->id, $selectedCategories, true) ? 'selected' : '' }}>
                                    {{ $categoryTranslation?->name ?? '#' . $category->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kt-form-description">{{ __('A post can belong to multiple categories.') }}</div>
                    @error('category_ids')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="kt-card mt-3">
        <div class="kt-card-header">
            <h3 class="kt-card-title">{{ __('Preview Image') }}</h3>
        </div>
        <div class="kt-card-content p-5">
            @php
                $selectedPreviewMediaId = old('preview_media_id', $post->previewMedia?->id);
            @endphp
            <input type="hidden" name="preview_media_id" value="{{ $selectedPreviewMediaId }}"
                data-preview-media-input>
            <div class="grid grid-cols-1 md:grid-cols-1 gap-5">
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Upload image') }}</label>
                    <div class="kt-form-control">
                        <input type="file" name="preview_image" class="kt-input w-full" data-preview-upload>
                    </div>
                    <div class="kt-form-description">{{ __('Uploading a file will replace the selected media for this post.') }}</div>
                    @error('preview_image')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                    <div class="{{ empty(optional($post->previewMedia)->url()) ? 'hidden' : '' }}"
                        data-preview-selected>
                        <div class="flex items-center gap-3 rounded-lg border border-border bg-muted/20 p-2 max-w-sm">
                            <img src="{{ optional($post->previewMedia)->url() }}"
                                alt="{{ old('preview_image_alt', $post->previewMedia->alt_text ?? '') }}"
                                class="size-12 rounded-md border border-border object-cover"
                                data-preview-selected-image>
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-mono truncate" data-preview-selected-name>
                                    {{ $post->previewMedia->original_name ?? __('Selected media') }}
                                </div>
                                <div class="text-[11px] text-secondary-foreground truncate" data-preview-selected-url>
                                    {{ optional($post->previewMedia)->url() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Alt text') }}</label>
                    <div class="kt-form-control">
                        <input type="text" name="preview_image_alt" class="kt-input w-full"
                            value="{{ old('preview_image_alt', $post->previewMedia->alt_text ?? '') }}">
                    </div>
                    <div class="kt-form-description">{{ __('Describe the preview image for accessibility and SEO.') }}</div>
                    @error('preview_image_alt')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-5 border-t border-border pt-5">
                <div class="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h4 class="font-medium text-mono">{{ __('Choose from media') }}</h4>
                        <div class="text-sm text-secondary-foreground">
                            {{ __('Search the media library by AJAX and select an existing image.') }}</div>
                    </div>
                    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:flex-wrap">
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-primary" data-open-media-library
                            data-media-mode="preview">
                            <i class="ki-filled ki-picture"></i>
                            {{ __('Choose Media') }}
                        </button>
                        <button type="button" class="kt-btn kt-btn-sm kt-btn-outline" data-clear-preview-selection>
                            {{ __('Clear selection') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-card mt-3" data-locale-tabs>
        <div class="kt-card-header flex-wrap gap-3">
            <div>
                <h3 class="kt-card-title">{{ __('Translations') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Switch between languages to edit localized content.') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach ($locales as $locale)
                    <button type="button"
                        class="kt-btn kt-btn-sm {{ $locale === $activeLocale ? 'kt-btn-primary' : 'kt-btn-outline' }}"
                        data-locale-tab="{{ $locale }}">
                        {{ strtoupper($locale) }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="kt-card-content p-5">
            @foreach ($locales as $locale)
                @php
                    $translation = $post->translationFor($locale);
                    $bodyValue = old("translations.$locale.content", $translation->content ?? '');
                @endphp
                <div data-locale-panel="{{ $locale }}" class="{{ $locale === $activeLocale ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Title') }} @if ($locale === $defaultLocale)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="kt-form-control">
                                <input type="text" name="translations[{{ $locale }}][title]"
                                    class="kt-input w-full"
                                    value="{{ old("translations.$locale.title", $translation->title ?? '') }}"
                                    @if ($locale === $defaultLocale) required @endif>
                            </div>
                            <div class="kt-form-description">{{ __('Post title for this language.') }}</div>
                            @error("translations.$locale.title")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Slug') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="translations[{{ $locale }}][slug]"
                                    class="kt-input w-full"
                                    value="{{ old("translations.$locale.slug", $translation->slug ?? '') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Leave blank to generate it from the title.') }}</div>
                            @error("translations.$locale.slug")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="kt-form-item md:col-span-2">
                            <label class="kt-form-label">{{ __('Excerpt') }}</label>
                            <div class="kt-form-control">
                                <textarea id="post-excerpt-{{ $locale }}" name="translations[{{ $locale }}][excerpt]"
                                    class="kt-input w-full min-h-48 js-post-rich-editor" data-editor-locale="{{ $locale }}"
                                    data-editor-kind="excerpt">{{ old("translations.$locale.excerpt", $translation->excerpt ?? '') }}</textarea>
                            </div>
                            <div class="kt-form-description">{{ __('Short summary used in cards and meta descriptions.') }}</div>
                            @error("translations.$locale.excerpt")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="kt-form-item md:col-span-2">
                            <label class="kt-form-label">{{ __('Content') }} @if ($locale === $defaultLocale)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <div class="kt-form-control">
                                <textarea id="post-content-{{ $locale }}" name="translations[{{ $locale }}][content]"
                                    class="kt-input w-full min-h-72 js-post-rich-editor" data-editor-locale="{{ $locale }}"
                                    data-editor-kind="content">{{ $bodyValue }}</textarea>
                            </div>
                            <div class="kt-form-description">
                                {{ $locale === $defaultLocale ? __('German content is required.') : __('If empty, default language content can be reused.') }}
                            </div>
                            @error("translations.$locale.content")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">{{ __('SEO') }}</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-3">
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('SEO Title') }}</label>
                                    <div class="kt-form-control">
                                        <input type="text" name="translations[{{ $locale }}][seo_title]"
                                            class="kt-input w-full"
                                            value="{{ old("translations.$locale.seo_title", $translation->seo_title ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional search/browser title override.') }}</div>
                                    @error("translations.$locale.seo_title")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Meta Description') }}</label>
                                    <div class="kt-form-control">
                                        <input type="text" name="translations[{{ $locale }}][meta_description]"
                                            class="kt-input w-full"
                                            value="{{ old("translations.$locale.meta_description", $translation->meta_description ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional search result description.') }}</div>
                                    @error("translations.$locale.meta_description")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('OG Title') }}</label>
                                    <div class="kt-form-control">
                                        <input type="text" name="translations[{{ $locale }}][og_title]"
                                            class="kt-input w-full"
                                            value="{{ old("translations.$locale.og_title", $translation->og_title ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional social sharing title.') }}</div>
                                    @error("translations.$locale.og_title")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('OG Description') }}</label>
                                    <div class="kt-form-control">
                                        <input type="text" name="translations[{{ $locale }}][og_description]"
                                            class="kt-input w-full"
                                            value="{{ old("translations.$locale.og_description", $translation->og_description ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional social sharing description.') }}</div>
                                    @error("translations.$locale.og_description")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item md:col-span-2">
                                    <label class="kt-form-label">{{ __('Canonical URL') }}</label>
                                    <div class="kt-form-control">
                                        <input type="url" name="translations[{{ $locale }}][canonical_url]"
                                            class="kt-input w-full"
                                            value="{{ old("translations.$locale.canonical_url", $translation->canonical_url ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional canonical URL if this content has a preferred source.') }}</div>
                                    @error("translations.$locale.canonical_url")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mt-5">
        <button type="submit" class="kt-btn kt-btn-primary">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.posts.index') }}" class="kt-btn kt-btn-mono">
            {{ __('Back') }}
        </a>
    </div>
</form>

@include('admin.blog.media._selector_modal')

@push('script')
    <script src="{{ URL('assets/vendors/tinymce/tinymce.min.js') }}"></script>
    <script>
        jQuery(function($) {
            const $previewMediaInput = $('[data-preview-media-input]');
            const $previewUploadInput = $('[data-preview-upload]');
            const $selectedPreview = $('[data-preview-selected]');
            const $selectedPreviewImage = $('[data-preview-selected-image]');
            const $selectedPreviewName = $('[data-preview-selected-name]');
            const $selectedPreviewUrl = $('[data-preview-selected-url]');

            function usePreviewMedia(media) {
                $previewMediaInput.val(media.id || '');
                $previewUploadInput.val('');
                $selectedPreview.removeClass('hidden');
                $selectedPreviewImage.attr({
                    src: media.url || '',
                    alt: media.alt_text || media.original_name || '',
                });
                $selectedPreviewName.text(media.original_name || media.filename || @json(__('Selected media')));
                $selectedPreviewUrl.text(media.url || '');
            }

            $('[data-clear-preview-selection]').on('click', function() {
                $previewMediaInput.val('');
                $selectedPreview.addClass('hidden');
            });

            $('[data-open-media-library]').on('click', function() {
                window.AdminMediaLibrary.open({
                    mode: $(this).data('media-mode') || 'preview',
                    onSelect: usePreviewMedia,
                });
            });

            if (window.tinymce) {
                window.tinymce.init({
                    selector: 'textarea.js-post-rich-editor',
                    license_key: 'gpl',
                    height: 380,
                    menubar: false,
                    branding: false,
                    promotion: false,
                    convert_urls: false,
                    relative_urls: false,
                    remove_script_host: false,
                    plugins: 'autoresize code fullscreen image link lists media preview table',
                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist blockquote | alignleft aligncenter alignright | link image media table | code preview fullscreen',
                    file_picker_types: 'image',
                    file_picker_callback(callback) {
                        window.AdminMediaLibrary.open({
                            mode: 'editor',
                            onSelect(media) {
                                callback(media.url, {
                                    title: media.title || media.original_name || '',
                                    alt: media.alt_text || media.original_name || '',
                                });
                            },
                        });
                    },
                    setup(editor) {
                        editor.on('change keyup undo redo setcontent', () => {
                            editor.save();
                        });
                    },
                });
            }

            $('[data-locale-tabs]').each(function() {
                const $container = $(this);
                const $tabs = $container.find('[data-locale-tab]');
                const $panels = $container.find('[data-locale-panel]');

                $tabs.on('click', function() {
                    const $tab = $(this);
                    const locale = $tab.data('locale-tab');

                    window.tinymce?.triggerSave();

                    $tabs.each(function() {
                        $(this)
                            .toggleClass('kt-btn-primary', this === $tab[0])
                            .toggleClass('kt-btn-outline', this !== $tab[0]);
                    });

                    $panels.each(function() {
                        $(this).toggleClass('hidden', $(this).data('locale-panel') !==
                            locale);
                    });

                    if (window.tinymce) {
                        $.each(['excerpt', 'content'], function(index, kind) {
                            const activeEditor = window.tinymce.get(
                                `post-${kind}-${locale}`);

                            if (activeEditor) {
                                setTimeout(function() {
                                    activeEditor.execCommand('mceAutoResize');
                                }, 0);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush

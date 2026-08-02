@php
    $defaultLocale = config('blog.default_locale', 'en');
    $locales = $locales ?? config('blog.supported_locales', [$defaultLocale]);
    $activeLocale = in_array($defaultLocale, $locales, true) ? $defaultLocale : ($locales[0] ?? $defaultLocale);
@endphp

<form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="kt-card mt-3">
        <div class="kt-card-header">
            <h3 class="kt-card-title">{{ __('Category settings') }}</h3>
        </div>
        <div class="kt-card-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-3">
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Parent Category') }}</label>
                    <div class="kt-form-control">
                        <select class="select2 kt-input w-full" name="parent_id">
                            <option value="">{{ __('None') }}</option>
                            @foreach ($parents as $parent)
                                @php $parentTranslation = $parent->translationFor($defaultLocale); @endphp
                                <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                                    {{ $parentTranslation?->name ?? ('#'.$parent->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kt-form-description">{{ __('Choose a parent to create nested categories.') }}</div>
                    @error('parent_id')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Status') }}</label>
                    <div class="kt-form-control">
                        <select class="select2 kt-input w-full" name="status">
                            @foreach (['active', 'inactive'] as $status)
                                <option value="{{ $status }}" {{ old('status', $category->status ?? 'active') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kt-form-description">{{ __('Only active categories appear on the frontend.') }}</div>
                    @error('status')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="kt-form-item">
                    <label class="kt-form-label">{{ __('Sort Order') }}</label>
                    <div class="kt-form-control">
                        <input type="number" name="sort_order" min="0" class="kt-input w-full" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                    </div>
                    <div class="kt-form-description">{{ __('Lower numbers are shown first.') }}</div>
                    @error('sort_order')
                        <div class="kt-form-message">{{ $message }}</div>
                    @enderror
                </div>
                <div class="kt-form-item md:col-span-2">
                    @php
                        $selectedPreviewMediaId = old('preview_media_id', $category->previewMedia?->id);
                    @endphp
                    <input type="hidden" name="preview_media_id" value="{{ $selectedPreviewMediaId }}"
                        data-category-preview-media-input>
                    <label class="kt-form-label">{{ __('Preview image') }}</label>
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <div>
                            <div class="kt-form-control">
                                <input type="file" name="preview_image" class="kt-input w-full"
                                    data-category-preview-upload>
                            </div>
                            <div class="kt-form-description">
                                {{ __('Optional. Uploading a file replaces the selected media.') }}</div>
                            @error('preview_image')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <div class="kt-form-control">
                                <input type="text" name="preview_image_alt" class="kt-input w-full"
                                    value="{{ old('preview_image_alt', $category->previewMedia->alt_text ?? '') }}"
                                    placeholder="{{ __('Alt text') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Describe this category image for SEO and accessibility.') }}</div>
                            @error('preview_image_alt')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="{{ empty(optional($category->previewMedia)->url()) ? 'hidden' : '' }}"
                            data-category-preview-selected>
                            <div class="flex items-center gap-3 rounded-lg border border-border bg-muted/20 p-2 max-w-md">
                                <img src="{{ optional($category->previewMedia)->variantUrl('sm') ?? optional($category->previewMedia)->url() }}"
                                    alt="{{ old('preview_image_alt', $category->previewMedia->alt_text ?? '') }}"
                                    class="size-12 rounded-md border border-border object-cover"
                                    data-category-preview-selected-image>
                                <div class="min-w-0">
                                    <div class="text-xs font-medium text-mono truncate"
                                        data-category-preview-selected-name>
                                        {{ $category->previewMedia->original_name ?? __('Selected media') }}
                                    </div>
                                    <div class="text-[11px] text-secondary-foreground truncate"
                                        data-category-preview-selected-url>
                                        {{ optional($category->previewMedia)->url() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 sm:flex-row">
                            <button type="button" class="kt-btn kt-btn-sm kt-btn-primary"
                                data-category-open-media-library>
                                <i class="ki-filled ki-picture"></i>
                                {{ __('Choose from media') }}
                            </button>
                            <button type="button" class="kt-btn kt-btn-sm kt-btn-outline"
                                data-category-clear-preview-selection>
                                {{ __('Clear') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="kt-card mt-3" data-locale-tabs>
        <div class="kt-card-header flex-wrap gap-3">
            <div>
                <h3 class="kt-card-title">{{ __('Translations') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Switch between languages to edit the category.') }}</div>
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
                    $translation = $category->translationFor($locale);
                @endphp
                <div data-locale-panel="{{ $locale }}" class="{{ $locale === $activeLocale ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-3">
                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Name') }} @if($locale === $defaultLocale)<span class="text-danger">*</span>@endif</label>
                            <div class="kt-form-control">
                                <input type="text" name="translations[{{ $locale }}][name]" class="kt-input w-full" value="{{ old("translations.$locale.name", $translation->name ?? '') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Category name for this language.') }}</div>
                            @error("translations.$locale.name")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Slug') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="translations[{{ $locale }}][slug]" class="kt-input w-full" value="{{ old("translations.$locale.slug", $translation->slug ?? '') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Leave blank to generate it from the name.') }}</div>
                            @error("translations.$locale.slug")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="kt-form-item md:col-span-2">
                            <label class="kt-form-label">{{ __('Description') }}</label>
                            <div class="kt-form-control">
                                <textarea name="translations[{{ $locale }}][description]" class="kt-input w-full min-h-28">{{ old("translations.$locale.description", $translation->description ?? '') }}</textarea>
                            </div>
                            <div class="kt-form-description">{{ __('Short category summary for frontend and metadata.') }}</div>
                            @error("translations.$locale.description")
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
                                        <input type="text" name="translations[{{ $locale }}][seo_title]" class="kt-input w-full" value="{{ old("translations.$locale.seo_title", $translation->seo_title ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional browser/search title.') }}</div>
                                    @error("translations.$locale.seo_title")
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Meta Description') }}</label>
                                    <div class="kt-form-control">
                                        <input type="text" name="translations[{{ $locale }}][meta_description]" class="kt-input w-full" value="{{ old("translations.$locale.meta_description", $translation->meta_description ?? '') }}">
                                    </div>
                                    <div class="kt-form-description">{{ __('Optional search result description.') }}</div>
                                    @error("translations.$locale.meta_description")
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
        <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-mono">
            {{ __('Back') }}
        </a>
    </div>
</form>

@include('admin.blog.media._selector_modal')

@push('script')
    <script>
        jQuery(function($) {
            const $previewMediaInput = $('[data-category-preview-media-input]');
            const $previewUploadInput = $('[data-category-preview-upload]');
            const $selectedPreview = $('[data-category-preview-selected]');
            const $selectedPreviewImage = $('[data-category-preview-selected-image]');
            const $selectedPreviewName = $('[data-category-preview-selected-name]');
            const $selectedPreviewUrl = $('[data-category-preview-selected-url]');

            function useCategoryPreviewMedia(media) {
                $previewMediaInput.val(media.id || '');
                $previewUploadInput.val('');
                $selectedPreview.removeClass('hidden');
                $selectedPreviewImage.attr({
                    src: media.thumbnail_url || media.url || '',
                    alt: media.alt_text || media.original_name || '',
                });
                $selectedPreviewName.text(media.original_name || media.filename || @json(__('Selected media')));
                $selectedPreviewUrl.text(media.url || '');
            }

            $('[data-category-clear-preview-selection]').on('click', function() {
                $previewMediaInput.val('');
                $previewUploadInput.val('');
                $selectedPreview.addClass('hidden');
            });

            $('[data-category-open-media-library]').on('click', function() {
                window.AdminMediaLibrary.open({
                    mode: 'preview',
                    onSelect: useCategoryPreviewMedia,
                });
            });

            $('[data-locale-tabs]').each(function() {
                const $container = $(this);
                const $tabs = $container.find('[data-locale-tab]');
                const $panels = $container.find('[data-locale-panel]');

                $tabs.on('click', function() {
                    const $tab = $(this);
                    const locale = $tab.data('locale-tab');

                    $tabs.each(function() {
                        $(this)
                            .toggleClass('kt-btn-primary', this === $tab[0])
                            .toggleClass('kt-btn-outline', this !== $tab[0]);
                    });

                    $panels.each(function() {
                        $(this).toggleClass('hidden', $(this).data('locale-panel') !== locale);
                    });
                });
            });
        });
    </script>
@endpush

@php
    $defaultLocale = config('blog.default_locale', 'en');
    $locales = $locales ?? config('blog.supported_locales', [$defaultLocale]);
    $activeLocale = $locales[0] ?? $defaultLocale;
@endphp

<form action="{{ $formAction }}" method="post">
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
                <div>
                    <label class="kt-form-label">{{ __('Parent Category') }}</label>
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
                <div>
                    <label class="kt-form-label">{{ __('Status') }}</label>
                    <select class="select2 kt-input w-full" name="status">
                        @foreach (['active', 'inactive'] as $status)
                            <option value="{{ $status }}" {{ old('status', $category->status ?? 'active') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" min="0" class="kt-input w-full" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
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
                        class="kt-btn kt-btn-sm {{ $loop->first ? 'kt-btn-primary' : 'kt-btn-outline' }}"
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
                        <div>
                            <label class="kt-form-label">{{ __('Name') }} @if($locale === $defaultLocale)<span class="text-danger">*</span>@endif</label>
                            <input type="text" name="translations[{{ $locale }}][name]" class="kt-input w-full" value="{{ old("translations.$locale.name", $translation->name ?? '') }}">
                        </div>
                        <div>
                            <label class="kt-form-label">{{ __('Slug') }}</label>
                            <input type="text" name="translations[{{ $locale }}][slug]" class="kt-input w-full" value="{{ old("translations.$locale.slug", $translation->slug ?? '') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="kt-form-label">{{ __('Description') }}</label>
                            <textarea name="translations[{{ $locale }}][description]" class="kt-input w-full min-h-28">{{ old("translations.$locale.description", $translation->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="kt-card mt-3">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">{{ __('SEO') }}</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-3">
                                <div>
                                    <label class="kt-form-label">{{ __('SEO Title') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][seo_title]" class="kt-input w-full" value="{{ old("translations.$locale.seo_title", $translation->seo_title ?? '') }}">
                                </div>
                                <div>
                                    <label class="kt-form-label">{{ __('Meta Description') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][meta_description]" class="kt-input w-full" value="{{ old("translations.$locale.meta_description", $translation->meta_description ?? '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3 mt-5">
        <button type="submit" class="kt-btn kt-btn-primary">
            {{ $submitLabel }}
        </button>
    <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-mono">
        {{ __('Back') }}
    </a>
</div>

@push('script')
    <script>
        document.querySelectorAll('[data-locale-tabs]').forEach((container) => {
            const tabs = container.querySelectorAll('[data-locale-tab]');
            const panels = container.querySelectorAll('[data-locale-panel]');

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    const locale = tab.dataset.localeTab;

                    tabs.forEach((item) => {
                        item.classList.toggle('kt-btn-primary', item === tab);
                        item.classList.toggle('kt-btn-outline', item !== tab);
                    });

                    panels.forEach((panel) => {
                        panel.classList.toggle('hidden', panel.dataset.localePanel !== locale);
                    });
                });
            });
        });
    </script>
@endpush
</form>

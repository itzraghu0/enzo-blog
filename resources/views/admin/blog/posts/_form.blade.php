@php
    $defaultLocale = config('blog.default_locale', 'en');
    $locales = $locales ?? config('blog.supported_locales', [$defaultLocale]);
    $activeLocale = $locales[0] ?? $defaultLocale;
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
                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                    <span class="kt-form-label max-w-32 w-full">{{ __('Author') }}</span>
                    <div class="grow min-w-48">
                        <select class="select2 kt-input w-full" name="user_id">
                            @foreach ($authors as $author)
                                <option value="{{ $author->id }}"
                                    {{ old('user_id', $post->user_id ?? auth()->id()) == $author->id ? 'selected' : '' }}>
                                    {{ $author->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                    <span class="kt-form-label max-w-32 w-full">{{ __('Status') }}</span>
                    <div class="grow min-w-48">
                        <select class="select2 kt-input w-full" name="status">
                            @foreach (['draft', 'pending', 'published', 'scheduled', 'archived'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('status', $post->status ?? 'draft') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                    <label class="kt-label">
                        <input class="kt-checkbox kt-checkbox-sm" type="checkbox" name="is_featured" value="1"
                            {{ old('is_featured', $post->is_featured ?? false) ? 'checked' : '' }} />
                        <span class="kt-checkbox-label">{{ __('Featured') }}</span>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-5 p-3">
                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                    <span class="kt-form-label max-w-32 w-full">{{ __('Published At') }}</span>
                    <div class="grow min-w-48">
                        <input type="text" name="published_at" class="kt-input w-full datepicker"
                            value="{{ old('published_at', optional($post->published_at ?? null)->format('Y-m-d H:i')) }}">
                    </div>
                </div>

                <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                    <span class="kt-form-label max-w-32 w-full">{{ __('Categories') }}</span>
                    <div class="grow min-w-48">
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
            @if (!empty(optional($post->previewMedia)->url()))
                <div class="mb-4">
                    <img src="{{ $post->previewMedia->url() }}"
                        alt="{{ old('preview_image_alt', $post->previewMedia->alt_text ?? '') }}"
                        class="max-w-xs rounded-xl border border-border">
                </div>
            @endif
            <input type="hidden" name="preview_media_id" value="{{ $selectedPreviewMediaId }}" data-preview-media-input>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-2">
                    <label class="kt-form-label">{{ __('Upload image') }}</label>
                    <input type="file" name="preview_image" class="kt-input w-full" data-preview-upload>
                    <p class="text-xs text-secondary-foreground">
                        {{ __('Uploading a file will replace the selected media for this post.') }}
                    </p>
                </div>
                <div>
                    <label class="kt-form-label">{{ __('Alt text') }}</label>
                    <input type="text" name="preview_image_alt" class="kt-input w-full"
                        value="{{ old('preview_image_alt', $post->previewMedia->alt_text ?? '') }}">
                </div>
            </div>

            <div class="mt-5 border-t border-border pt-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h4 class="font-medium text-mono">{{ __('Choose from media') }}</h4>
                        <div class="text-sm text-secondary-foreground">{{ __('Select an existing image from the library instead of uploading a new one.') }}</div>
                    </div>
                    <button type="button" class="kt-btn kt-btn-sm kt-btn-outline" data-clear-preview-selection>
                        {{ __('Clear selection') }}
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4" data-preview-media-grid>
                    @forelse ($mediaLibrary ?? [] as $mediaItem)
                        @php
                            $isSelected = (string) $selectedPreviewMediaId === (string) $mediaItem->id;
                        @endphp
                        <button
                            type="button"
                            class="text-left rounded-xl border {{ $isSelected ? 'border-primary ring-2 ring-primary/20' : 'border-border' }} bg-background overflow-hidden transition hover:border-primary"
                            data-preview-media-card
                            data-media-id="{{ $mediaItem->id }}"
                            data-media-url="{{ $mediaItem->url() }}"
                            data-media-alt="{{ $mediaItem->alt_text ?? '' }}">
                            <img src="{{ $mediaItem->url() }}"
                                alt="{{ $mediaItem->alt_text ?? ($mediaItem->original_name ?? '') }}"
                                class="h-36 w-full object-cover">
                            <div class="p-3 space-y-2">
                                <div>
                                    <div class="font-medium text-mono text-sm truncate">{{ $mediaItem->original_name }}</div>
                                    <div class="text-xs text-secondary-foreground truncate">{{ $mediaItem->path }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs {{ $isSelected ? 'text-primary' : 'text-secondary-foreground' }}" data-preview-status>
                                        {{ $isSelected ? __('Selected') : __('Available') }}
                                    </span>
                                    <span class="kt-btn kt-btn-sm {{ $isSelected ? 'kt-btn-primary' : 'kt-btn-outline' }}">
                                        {{ __('Use') }}
                                    </span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="text-sm text-secondary-foreground">
                            {{ __('No media uploaded yet.') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="kt-card mt-3" data-locale-tabs>
        <div class="kt-card-header flex-wrap gap-3">
            <div>
                <h3 class="kt-card-title">{{ __('Translations') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Switch between languages to edit localized content.') }}</div>
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
                    $translation = $post->translationFor($locale);
                    $bodyValue = old("translations.$locale.content", $translation->content ?? '');
                @endphp
                <div data-locale-panel="{{ $locale }}" class="{{ $locale === $activeLocale ? '' : 'hidden' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="kt-form-label">{{ __('Title') }} @if ($locale === $defaultLocale)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="text" name="translations[{{ $locale }}][title]" class="kt-input w-full"
                                value="{{ old("translations.$locale.title", $translation->title ?? '') }}">
                        </div>
                        <div>
                            <label class="kt-form-label">{{ __('Slug') }}</label>
                            <input type="text" name="translations[{{ $locale }}][slug]" class="kt-input w-full"
                                value="{{ old("translations.$locale.slug", $translation->slug ?? '') }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="kt-form-label">{{ __('Excerpt') }}</label>
                            <textarea name="translations[{{ $locale }}][excerpt]" class="kt-input w-full min-h-24">{{ old("translations.$locale.excerpt", $translation->excerpt ?? '') }}</textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="kt-form-label">{{ __('Content') }}</label>
                            <div class="rounded-xl border border-border overflow-hidden" data-tiptap-wrapper="{{ $locale }}">
                                <div class="flex flex-wrap items-center gap-2 p-3 border-b border-border bg-muted/20" data-tiptap-toolbar>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="paragraph">{{ __('P') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="heading2">{{ __('H2') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="bold">{{ __('Bold') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="italic">{{ __('Italic') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="bulletList">{{ __('Bullet') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="orderedList">{{ __('Numbered') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="blockquote">{{ __('Quote') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="link">{{ __('Link') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="image">{{ __('Image') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="undo">{{ __('Undo') }}</button>
                                    <button type="button" class="kt-btn kt-btn-sm kt-btn-mono" data-command="redo">{{ __('Redo') }}</button>
                                </div>
                                <div class="p-4 min-h-72 bg-white" data-tiptap-editor></div>
                            </div>
                            <textarea name="translations[{{ $locale }}][content]" class="hidden" data-body-input>{{ $bodyValue }}</textarea>
                            <p class="text-xs text-secondary-foreground mt-2">
                                {{ __('Store the rendered HTML from the editor here.') }}
                            </p>
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
                                    <input type="text" name="translations[{{ $locale }}][seo_title]" class="kt-input w-full"
                                        value="{{ old("translations.$locale.seo_title", $translation->seo_title ?? '') }}">
                                </div>
                                <div>
                                    <label class="kt-form-label">{{ __('Meta Description') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][meta_description]" class="kt-input w-full"
                                        value="{{ old("translations.$locale.meta_description", $translation->meta_description ?? '') }}">
                                </div>
                                <div>
                                    <label class="kt-form-label">{{ __('OG Title') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][og_title]" class="kt-input w-full"
                                        value="{{ old("translations.$locale.og_title", $translation->og_title ?? '') }}">
                                </div>
                                <div>
                                    <label class="kt-form-label">{{ __('OG Description') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][og_description]" class="kt-input w-full"
                                        value="{{ old("translations.$locale.og_description", $translation->og_description ?? '') }}">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="kt-form-label">{{ __('Canonical URL') }}</label>
                                    <input type="url" name="translations[{{ $locale }}][canonical_url]" class="kt-input w-full"
                                        value="{{ old("translations.$locale.canonical_url", $translation->canonical_url ?? '') }}">
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
        <a href="{{ route('admin.posts.index') }}" class="kt-btn kt-btn-mono">
            {{ __('Back') }}
        </a>
    </div>
</form>

@push('script')
    <script type="module">
        import {
            Editor
        } from 'https://cdn.jsdelivr.net/npm/@tiptap/core@2/+esm';
        import StarterKit from 'https://cdn.jsdelivr.net/npm/@tiptap/starter-kit@2/+esm';
        import Link from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-link@2/+esm';
        import Image from 'https://cdn.jsdelivr.net/npm/@tiptap/extension-image@2/+esm';

        window.blogPostEditors = window.blogPostEditors || {};

        document.querySelectorAll('[data-tiptap-wrapper]').forEach((wrapper) => {
            const locale = wrapper.dataset.tiptapWrapper;
            const editorElement = wrapper.querySelector('[data-tiptap-editor]');
            const inputElement = wrapper.querySelector('[data-body-input]');
            const toolbar = wrapper.querySelector('[data-tiptap-toolbar]');

            const editor = new Editor({
                element: editorElement,
                extensions: [
                    StarterKit,
                    Link.configure({
                        openOnClick: false,
                        autolink: true,
                        linkOnPaste: true,
                    }),
                    Image.configure({
                        inline: false,
                        allowBase64: true,
                    }),
                ],
                content: inputElement.value || '<p></p>',
                onUpdate({
                    editor
                }) {
                    inputElement.value = editor.getHTML();
                },
            });

            window.blogPostEditors[locale] = editor;

            toolbar.addEventListener('click', (event) => {
                const button = event.target.closest('[data-command]');

                if (!button) {
                    return;
                }

                const command = button.dataset.command;

                if (command === 'paragraph') {
                    editor.chain().focus().setParagraph().run();
                } else if (command === 'heading2') {
                    editor.chain().focus().toggleHeading({
                        level: 2
                    }).run();
                } else if (command === 'bold') {
                    editor.chain().focus().toggleBold().run();
                } else if (command === 'italic') {
                    editor.chain().focus().toggleItalic().run();
                } else if (command === 'bulletList') {
                    editor.chain().focus().toggleBulletList().run();
                } else if (command === 'orderedList') {
                    editor.chain().focus().toggleOrderedList().run();
                } else if (command === 'blockquote') {
                    editor.chain().focus().toggleBlockquote().run();
                } else if (command === 'link') {
                    const url = window.prompt(@json(__('Enter a URL')), 'https://');

                    if (url) {
                        editor.chain().focus().extendMarkRange('link').setLink({
                            href: url
                        }).run();
                    }
                } else if (command === 'image') {
                    const url = window.prompt(@json(__('Enter an image URL')), 'https://');

                    if (url) {
                        editor.chain().focus().setImage({
                            src: url,
                            alt: ''
                        }).run();
                    }
                } else if (command === 'undo') {
                    editor.chain().focus().undo().run();
                } else if (command === 'redo') {
                    editor.chain().focus().redo().run();
                }
            });
        });

        const previewMediaInput = document.querySelector('[data-preview-media-input]');
        const previewUploadInput = document.querySelector('[data-preview-upload]');
        const clearPreviewSelectionButton = document.querySelector('[data-clear-preview-selection]');
        const previewCards = document.querySelectorAll('[data-preview-media-card]');

        function resetPreviewCardState() {
            previewCards.forEach((card) => {
                card.classList.remove('border-primary', 'ring-2', 'ring-primary/20');
                card.classList.add('border-border');

                const badge = card.querySelector('.kt-btn');
                const status = card.querySelector('[data-preview-status]');

                if (badge) {
                    badge.classList.remove('kt-btn-primary');
                    badge.classList.add('kt-btn-outline');
                }

                if (status) {
                    status.textContent = @json(__('Available'));
                    status.classList.remove('text-primary');
                    status.classList.add('text-secondary-foreground');
                }
            });
        }

        previewCards.forEach((card) => {
            card.addEventListener('click', () => {
                if (previewMediaInput) {
                    previewMediaInput.value = card.dataset.mediaId || '';
                }

                if (previewUploadInput) {
                    previewUploadInput.value = '';
                }

                resetPreviewCardState();

                card.classList.remove('border-border');
                card.classList.add('border-primary', 'ring-2', 'ring-primary/20');

                const badge = card.querySelector('.kt-btn');
                const status = card.querySelector('[data-preview-status]');

                if (badge) {
                    badge.classList.remove('kt-btn-outline');
                    badge.classList.add('kt-btn-primary');
                }

                if (status) {
                    status.textContent = @json(__('Selected'));
                    status.classList.remove('text-secondary-foreground');
                    status.classList.add('text-primary');
                }
            });
        });

        if (clearPreviewSelectionButton) {
            clearPreviewSelectionButton.addEventListener('click', () => {
                if (previewMediaInput) {
                    previewMediaInput.value = '';
                }

                resetPreviewCardState();
            });
        }

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

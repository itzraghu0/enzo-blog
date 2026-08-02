@php
    $isEdit = isset($media) && $media->exists;
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if (($formMethod ?? 'POST') !== 'POST')
        @method($formMethod)
    @endif

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <h3 class="kt-card-title">{{ $isEdit ? __('Edit media') : __('Upload media') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Files are stored in the public media folder and can be reused across blog content.') }}
                </div>
            </div>
        </div>
        <div class="kt-card-content p-5">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-12">
                @if ($isEdit)
                    <div class="lg:col-span-4">
                        <div class="rounded-2xl border border-border bg-background overflow-hidden">
                            @if (str_starts_with((string) $media->mime_type, 'image/'))
                                <img src="{{ $media->url() }}"
                                    alt="{{ $media->alt_text ?? ($media->original_name ?? '') }}"
                                    class="h-72 w-full object-cover">
                            @else
                                <div class="h-72 flex items-center justify-center bg-muted text-secondary-foreground">
                                    <i class="ki-filled ki-file text-4xl"></i>
                                </div>
                            @endif
                            <div class="p-4 space-y-2">
                                <div class="font-medium text-mono break-all">{{ $media->original_name }}</div>
                                <div class="text-xs text-secondary-foreground break-all">{{ $media->path }}</div>
                                <button type="button" class="kt-btn kt-btn-sm kt-btn-mono w-full justify-center"
                                    data-copy-url="{{ $media->url() }}">
                                    {{ __('Copy URL') }}
                                </button>
                            </div>
                        </div>

                        @if (str_starts_with((string) $media->mime_type, 'image/'))
                            <div class="mt-4 rounded-2xl border border-border bg-background p-4">
                                <h4 class="font-medium text-mono">{{ __('Generated WebP variants') }}</h4>
                                <div class="mt-3 grid gap-2">
                                    @foreach (array_keys(config('blog.media_variants', [])) as $variant)
                                        @php($variantPath = $media->variantPath($variant))
                                        <div class="rounded-xl border border-border p-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="font-semibold uppercase text-mono">{{ $variant }}</span>
                                                @if ($variantPath)
                                                    <button type="button" class="kt-btn kt-btn-xs kt-btn-outline"
                                                        data-copy-url="{{ asset($variantPath) }}">
                                                        {{ __('Copy URL') }}
                                                    </button>
                                                @else
                                                    <span class="kt-badge kt-badge-warning">{{ __('Missing') }}</span>
                                                @endif
                                            </div>
                                            <div class="mt-2 text-xs text-secondary-foreground break-all">
                                                {{ $variantPath ?? __('Not generated yet') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="{{ $isEdit ? 'lg:col-span-8' : 'lg:col-span-12' }}">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="kt-form-item md:col-span-2">
                            <label class="kt-form-label">
                                {{ $isEdit ? __('Replace file') : __('File') }}
                                @unless ($isEdit)
                                    <span class="text-danger">*</span>
                                @endunless
                            </label>
                            <div class="kt-form-control">
                                <label class="kt-input w-full">
                                    <i class="ki-filled ki-file-up"></i>
                                    <input type="file" name="file" @required(!$isEdit)>
                                </label>
                            </div>
                            @if ($isEdit)
                                <div class="kt-form-description">{{ __('Leave empty to update metadata only.') }}</div>
                            @else
                                <div class="kt-form-description">
                                    {{ __('Upload an image file to the public media folder.') }}</div>
                            @endif
                            @error('file')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Alt text') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="alt_text" class="kt-input w-full"
                                    value="{{ old('alt_text', $media->alt_text ?? '') }}"
                                    placeholder="{{ __('Optional') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Describe the image for accessibility and SEO.') }}
                            </div>
                            @error('alt_text')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Title') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="title" class="kt-input w-full"
                                    value="{{ old('title', $media->title ?? '') }}"
                                    placeholder="{{ __('Optional') }}">
                            </div>
                            <div class="kt-form-description">{{ __('Optional media title shown in metadata.') }}</div>
                            @error('title')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="kt-form-item md:col-span-2">
                            <label class="kt-form-label">{{ __('Caption') }}</label>
                            <div class="kt-form-control">
                                <textarea name="caption" class="kt-input w-full min-h-24" placeholder="{{ __('Optional') }}">{{ old('caption', $media->caption ?? '') }}</textarea>
                            </div>
                            <div class="kt-form-description">
                                {{ __('Optional caption for frontend display and JSON-LD.') }}</div>
                            @error('caption')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Collection') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="collection" class="kt-input w-full"
                                    value="{{ old('collection', $media->collection ?? 'default') }}">
                            </div>
                            <div class="kt-form-description">
                                {{ __('Group media by usage, for example preview or default.') }}</div>
                            @error('collection')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Locale') }}</label>
                            <div class="kt-form-control">
                                <select name="locale" class="kt-input w-full">
                                    <option value="">{{ __('Any') }}</option>
                                    @foreach ($locales as $locale)
                                        <option value="{{ $locale }}" @selected(old('locale', $media->locale ?? '') === $locale)>
                                            {{ strtoupper($locale) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="kt-form-description">{{ __('Restrict this media to a language if needed.') }}
                            </div>
                            @error('locale')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Sort order') }}</label>
                            <div class="kt-form-control">
                                <input type="number" name="sort_order" min="0" class="kt-input w-full"
                                    value="{{ old('sort_order', $media->sort_order ?? 0) }}">
                            </div>
                            <div class="kt-form-description">
                                {{ __('Lower numbers appear first when media is ordered.') }}</div>
                            @error('sort_order')
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-3 md:col-span-2">
                        <div class="rounded-xl border border-border p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-mono">{{ __('SEO metadata') }}</h4>
                                <div class="text-sm text-secondary-foreground">
                                    {{ __('Used when this media is attached to blogs, previews, and structured data.') }}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Keywords') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="seo_keywords" class="kt-input w-full min-h-24" placeholder="{{ __('Comma separated keywords') }}">{{ old('seo_keywords', $media->seo_keywords ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Comma separated keywords for media search and structured data.') }}
                                    </div>
                                    @error('seo_keywords')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Hashtags') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="hashtags" class="kt-input w-full min-h-24" placeholder="{{ __('#blog, #topic, #brand') }}">{{ old('hashtags', $media->hashtags ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Use hashtags relevant to this media.') }}</div>
                                    @error('hashtags')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item md:col-span-2">
                                    <label class="kt-form-label">{{ __('Relevance notes') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="relevance_notes" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('Explain where this media is relevant and why it should be used.') }}">{{ old('relevance_notes', $media->relevance_notes ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Explain where this media is relevant and why it should be used.') }}
                                    </div>
                                    @error('relevance_notes')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 md:col-span-2">
                        <div class="rounded-xl border border-border p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-mono">{{ __('AEO metadata') }}</h4>
                                <div class="text-sm text-secondary-foreground">
                                    {{ __('Answer-engine fields help AI/search systems understand the image context.') }}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('AEO summary') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="aeo_summary" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('Short factual answer-style summary for this media') }}">{{ old('aeo_summary', $media->aeo_summary ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Short answer-style summary for AI/search engines.') }}</div>
                                    @error('aeo_summary')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('AEO questions') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="aeo_questions" class="kt-input w-full min-h-28" placeholder="{{ __('One question per line') }}">{{ old('aeo_questions', isset($media->aeo_questions) ? implode("\n", $media->aeo_questions ?? []) : '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">{{ __('One likely question per line.') }}
                                    </div>
                                    @error('aeo_questions')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class=" mt-3 md:col-span-2">
                        <div class="rounded-xl border border-border p-4">
                            <div class="mb-4">
                                <h4 class="font-medium text-mono">{{ __('GEO metadata') }}</h4>
                                <div class="text-sm text-secondary-foreground">
                                    {{ __('Generative Engine Optimization fields help AI engines cite and understand this media.') }}
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('GEO summary') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="geo_summary" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('Generative-engine ready summary for this media') }}">{{ old('geo_summary', $media->geo_summary ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Write the concise meaning of this media for AI answer engines.') }}
                                    </div>
                                    @error('geo_summary')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Entities') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="geo_entities" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('Brands, people, products, places, concepts') }}">{{ old('geo_entities', $media->geo_entities ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Comma separated entities AI engines should associate with this media.') }}
                                    </div>
                                    @error('geo_entities')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Generative prompts') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="geo_prompts" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('One AI discovery prompt per line') }}">{{ old('geo_prompts', $media->geo_prompts ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Prompts or questions where this media should be considered relevant.') }}
                                    </div>
                                    @error('geo_prompts')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="kt-form-item">
                                    <label class="kt-form-label">{{ __('Generative context') }}</label>
                                    <div class="kt-form-control">
                                        <textarea name="geo_context" class="kt-input w-full min-h-28"
                                            placeholder="{{ __('Context for AI summaries, retrieval, and citations') }}">{{ old('geo_context', $media->geo_context ?? '') }}</textarea>
                                    </div>
                                    <div class="kt-form-description">
                                        {{ __('Extra context for AI retrieval, summaries, and citations.') }}</div>
                                    @error('geo_context')
                                        <div class="kt-form-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>



                    <div
                        class="mt-5 flex flex-col gap-3 border-t border-border pt-5 sm:flex-row sm:items-center sm:justify-end">
                        <a href="{{ route('admin.media.index') }}" class="kt-btn kt-btn-mono justify-center">
                            {{ __('Back') }}
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary justify-center">
                            <i class="ki-filled {{ $isEdit ? 'ki-check' : 'ki-plus-circle' }}"></i>
                            {{ $submitLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@if ($isEdit)
    @push('script')
        <script>
            jQuery(function($) {
                $('[data-copy-url]').on('click', function() {
                    navigator.clipboard.writeText($(this).data('copy-url') || '');
                });
            });
        </script>
    @endpush
@endif

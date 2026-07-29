<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canManageBlog();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(['draft', 'pending', 'published', 'scheduled', 'archived'])],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'preview_image' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,gif,svg'],
            'preview_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'preview_image_alt' => ['nullable', 'string', 'max:255'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'geo' => ['nullable', 'array'],
            'geo.country_code' => ['nullable', 'string', 'max:2'],
            'geo.region' => ['nullable', 'string', 'max:255'],
            'geo.city' => ['nullable', 'string', 'max:255'],
            'geo.latitude' => ['nullable', 'numeric'],
            'geo.longitude' => ['nullable', 'numeric'],
            'geo.timezone' => ['nullable', 'string', 'max:100'],
            'translations' => ['required', 'array'],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.slug' => ['nullable', 'string', 'max:255'],
            'translations.*.excerpt' => ['nullable', 'string'],
            'translations.*.content' => ['nullable', 'string'],
            'translations.*.seo_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:500'],
            'translations.*.og_title' => ['nullable', 'string', 'max:255'],
            'translations.*.og_description' => ['nullable', 'string', 'max:500'],
            'translations.*.canonical_url' => ['nullable', 'url', 'max:2048'],
            'translations.*.preview_image_alt' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $defaultLocale = config('blog.default_locale', 'en');

            if (! $this->filled("translations.$defaultLocale")) {
                $validator->errors()->add("translations.$defaultLocale", __('validation.required', ['attribute' => $defaultLocale]));
                return;
            }

            if (! $this->input("translations.$defaultLocale.title")) {
                $validator->errors()->add("translations.$defaultLocale.title", __('validation.required', ['attribute' => 'title']));
            }
        });
    }
}

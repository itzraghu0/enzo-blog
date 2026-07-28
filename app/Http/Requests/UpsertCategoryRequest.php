<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canManageBlog();
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'translations' => ['required', 'array'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.slug' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string'],
            'translations.*.seo_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:500'],
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

            if (! $this->input("translations.$defaultLocale.name")) {
                $validator->errors()->add("translations.$defaultLocale.name", __('validation.required', ['attribute' => 'name']));
            }
        });
    }
}

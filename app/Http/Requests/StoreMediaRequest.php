<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'file' => [$this->isMethod('post') ? 'required' : 'nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,gif,svg'],
            'collection' => ['nullable', 'string', 'max:100'],
            'locale' => ['nullable', 'string', Rule::in(config('blog.supported_locales', []))],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
            'seo_keywords' => ['nullable', 'string', 'max:2000'],
            'hashtags' => ['nullable', 'string', 'max:2000'],
            'relevance_notes' => ['nullable', 'string', 'max:4000'],
            'aeo_summary' => ['nullable', 'string', 'max:4000'],
            'aeo_questions' => ['nullable', 'string', 'max:4000'],
            'geo_summary' => ['nullable', 'string', 'max:4000'],
            'geo_entities' => ['nullable', 'string', 'max:4000'],
            'geo_prompts' => ['nullable', 'string', 'max:4000'],
            'geo_context' => ['nullable', 'string', 'max:4000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

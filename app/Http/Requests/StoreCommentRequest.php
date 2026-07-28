<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasVerifiedEmail();
    }

    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'content' => ['required', 'string', 'min:2', 'max:5000'],
        ];
    }
}

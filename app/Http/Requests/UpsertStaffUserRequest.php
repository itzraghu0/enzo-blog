<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpsertStaffUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $user = $this->route('staff');
        $userId = $user instanceof User ? $user->getKey() : null;
        $passwordRules = $this->isMethod('post')
            ? ['required', 'string', 'min:8', 'confirmed']
            : ['nullable', 'string', 'min:8', 'confirmed'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'role' => [
                'required',
                'integer',
                Rule::in([
                    User::ROLE_ADMIN,
                    User::ROLE_EDITOR,
                    User::ROLE_AUTHOR,
                ]),
            ],
            'password' => $passwordRules,
            'email_verified_at' => ['sometimes', 'boolean'],
        ];
    }
}

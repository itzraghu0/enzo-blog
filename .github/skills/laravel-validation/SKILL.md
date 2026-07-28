---
name: laravel-validation
description: Guide for creating form validation rules and custom validation for this LMS project. Use this when creating form requests, validation rules, or custom form validators.
---

# Laravel Validation

## Creating Form Requests

When implementing form validation, create dedicated Form Request classes:

```bash
php artisan make:request StoreYourModelRequest
```

## Form Request Structure

Place custom rules in `app/Rules/` directory and use them in form requests:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required|exists:letter_types,id',
            'firm_id' => 'required|exists:firms,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,sent,received',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'Letter name']),
            'type_id.exists' => __('validation.exists', ['attribute' => 'Letter type']),
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            if ($this->status === 'sent' && !$this->has('recipient_email')) {
                $validator->errors()->add('recipient_email', 'Recipient email is required when sending.');
            }
        });
    }
}
```

## Using Form Requests in Controllers

```php
public function store(StoreLetterRequest $request)
{
    $validated = $request->validated();

    Letter::create($validated);

    return redirect()->route('admin.letters')->with('success', 'Letter created');
}
```

## Common Validation Rules for LMS

```php
[
    // Text fields
    'name' => 'required|string|max:255',
    'email' => 'required|email:rfc,dns',
    'phone' => 'nullable|regex:/^[\d\s\-\+\(\)]+$/',

    // Numbers (customer numbers, contract numbers, etc)
    'number' => 'required|unique:table_name|regex:/^[A-Z0-9-]+$/',

    // Relationships
    'firm_id' => 'required|exists:firms,id',
    'user_id' => 'required|exists:users,id',
    'type_id' => 'required|exists:types,id',

    // Status fields
    'status' => 'required|in:active,inactive,draft,sent,received',

    // Booleans
    'is_active' => 'boolean',

    // Arrays
    'tags' => 'array|max:10',
    'tags.*' => 'string|distinct|max:50',

    // Files
    'document' => 'nullable|mimes:pdf,doc,docx|max:5120',
]
```

## Custom Validation Rules

Create custom rules in `app/Rules/`:

```bash
php artisan make:rule UniqueCustomerNumber
```

Example custom rule:

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueCustomerNumber implements Rule
{
    private $exceptId;

    public function __construct($exceptId = null)
    {
        $this->exceptId = $exceptId;
    }

    public function passes($attribute, $value)
    {
        $query = CustomerNumber::where('number', $value);

        if ($this->exceptId) {
            $query->where('id', '!=', $this->exceptId);
        }

        return !$query->exists();
    }

    public function message()
    {
        return __('validation.unique', ['attribute' => 'Customer number']);
    }
}
```

## Using Custom Rules

```php
use App\Rules\UniqueCustomerNumber;

public function rules(): array
{
    return [
        'number' => [
            'required',
            'string',
            new UniqueCustomerNumber($this->model->id ?? null),
        ],
    ];
}
```

## Field-Specific Rules for LMS Models

### Letters

```php
[
    'name' => 'required|string|max:255',
    'letter_type_id' => 'required|exists:letter_types,id',
    'letter_case_id' => 'required|exists:letter_cases,id',
    'status' => 'required|in:draft,sent,received,archived',
]
```

### Users

```php
[
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email,' . $this->user?->id,
    'role' => 'required|in:admin,user,viewer',
]
```

### Firms

```php
[
    'name' => 'required|string|max:255',
    'email' => 'nullable|email',
    'phone' => 'nullable|regex:/^[\d\s\-\+\(\)]+$/',
]
```

## Validation in API Requests

For API endpoints, use consistent validation responses:

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'type_id' => 'required|exists:letter_types,id',
    ]);

    try {
        $letter = Letter::create($validated);
        return response()->json($letter, 201);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => ['server' => $e->getMessage()]
        ], 422);
    }
}
```

## Localized Validation Messages

Messages are in `lang/` directory:

```json
{
    "validation": {
        "required": "The {attribute} field is required.",
        "unique": "The {attribute} has already been taken.",
        "exists": "The selected {attribute} is invalid."
    }
}
```

## Best Practices for This LMS

1. **Always use Form Requests**: Don't validate in controllers, use dedicated request classes
2. **Custom messages**: Provide user-friendly error messages in user's language
3. **Cross-field validation**: Use `withValidator` callback for complex validations
4. **Relationships**: Always validate that related records exist with `exists` rule
5. **Unique constraints**: Validate unique fields against business logic, not just database
6. **Phone/Number formats**: Define regex patterns for standardized formats
7. **File uploads**: Always validate file types and sizes for security
8. **Translation support**: Use `__()` for multilingual validation messages (supports `en`, `de`)

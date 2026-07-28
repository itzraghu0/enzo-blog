---
name: laravel-admin-panel
description: Guide for building and maintaining the Laravel admin panel interface for the LMS. Use this when creating admin pages, navigation items, or admin UI components using Blade templates.
---

# Laravel Admin Panel Development

## Admin Panel Structure

This LMS project uses a navbar-based admin panel with Blade templates. The main layout is in `resources/views/layouts/admin/navbar.blade.php`.

## Adding Menu Items to Navbar

The navbar uses a Keenthemes-based menu system. Follow this pattern to add new menu items:

```blade
<div class="kt-menu-item {{ Helper::setActive('admin/your-resource') }}">
    <a class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono"
        href="{{ route('admin.your_resource') }}">
        <span class="kt-menu-title font-medium text-foreground text-sm">
            {{ __('Your Resource') }}
        </span>
    </a>
</div>
```

## Dropdown Menu Items

For menu items with submenus:

```blade
<div class="kt-menu-item {{ Helper::setActive('admin/your-resource') }}"
    data-kt-menu-item-offset="-158px, 0"
    data-kt-menu-item-placement="bottom-start"
    data-kt-menu-item-toggle="accordion|lg:dropdown"
    data-kt-menu-item-trigger="click|lg:hover">

    <div class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground">
        <span class="kt-menu-title font-medium text-foreground text-sm">
            {{ __('Your Resource') }}
        </span>
        <span class="kt-menu-arrow flex lg:hidden">
            <span class="flex kt-menu-item-show:hidden">
                <i class="ki-filled ki-plus text-xs text-secondary-foreground"></i>
            </span>
            <span class="hidden kt-menu-item-show:inline-flex">
                <i class="ki-filled ki-minus text-xs text-secondary-foreground"></i>
            </span>
        </span>
    </div>

    <div class="kt-menu-dropdown">
        <div class="lg:w-[250px] mt-2 lg:mt-0 lg:border-e lg:border-e-border rounded-xl lg:rounded-l-xl lg:rounded-r-none shrink-0 px-3 py-4 lg:p-7.5 bg-muted/25">
            <div class="kt-menu kt-menu-default kt-menu-fit flex-col">
                <div class="kt-menu-item {{ Helper::setActive('admin/your-resource') }}">
                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                        href="{{ route('admin.your_resource') }}">
                        <span class="kt-menu-icon">
                            <i class="ki-filled ki-icon-name"></i>
                        </span>
                        <span class="kt-menu-title grow-0">
                            {{ __('List') }}
                        </span>
                    </a>
                </div>
                <div class="kt-menu-item {{ Helper::setActive('admin/your-resource/create') }}">
                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                        href="{{ route('admin.your_resource.create') }}">
                        <span class="kt-menu-icon">
                            <i class="ki-filled ki-plus"></i>
                        </span>
                        <span class="kt-menu-title grow-0">
                            {{ __('Create') }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
```

## Creating Admin Pages

### List Page Template

```blade
<!-- resources/views/admin/your-resource/index.blade.php -->
@extends('layouts.admin.app')

@section('content')
<div class="card">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ __('Your Resources') }}</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('admin.your_resource.create') }}" class="btn btn-sm btn-light-primary">
                {{ __('Create New') }}
            </a>
        </div>
    </div>
    <div class="card-body pt-0">
        <table class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @forelse($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>
                        <span class="badge badge-light-{{ $item->is_active ? 'success' : 'danger' }}">
                            {{ $item->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </td>
                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.your_resource.edit', $item) }}" class="btn btn-icon btn-bg-light btn-active-light-primary btn-sm">
                            <i class="ki-filled ki-pencil"></i>
                        </a>
                        <form action="{{ route('admin.your_resource.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-icon btn-bg-light btn-active-light-danger btn-sm">
                                <i class="ki-filled ki-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">{{ __('No items found') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $items->links() }}
    </div>
</div>
@endsection
```

### Create/Edit Form Template

```blade
<!-- resources/views/admin/your-resource/form.blade.php -->
@extends('layouts.admin.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            {{ isset($item) ? __('Edit') : __('Create') }} {{ __('Your Resource') }}
        </h3>
    </div>
    <div class="card-body">
        <form action="{{ isset($item) ? route('admin.your_resource.update', $item) : route('admin.your_resource.store') }}" method="POST">
            @csrf
            @if(isset($item))
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $item->name ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Status') }}</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="">{{ __('Select Status') }}</option>
                    <option value="active" {{ old('status', $item->status ?? '') === 'active' ? 'selected' : '' }}>
                        {{ __('Active') }}
                    </option>
                    <option value="inactive" {{ old('status', $item->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        {{ __('Inactive') }}
                    </option>
                </select>
                @error('status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    {{ isset($item) ? __('Update') : __('Create') }}
                </button>
                <a href="{{ route('admin.your_resource') }}" class="btn btn-secondary">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
```

## Icons Reference

The admin panel uses Keenthemes icons. Common icons:

```
ki-filled ki-plus              - Add/Create
ki-filled ki-pencil            - Edit
ki-filled ki-trash             - Delete
ki-filled ki-magnifier         - Search
ki-filled ki-eye              - View
ki-filled ki-users            - Users
ki-filled ki-setting-2         - Settings
ki-filled ki-calendar          - Calendar
ki-filled ki-sms              - Messages
ki-filled ki-file-sheet       - Documents
ki-filled ki-rocket           - Events
ki-filled ki-support          - Support/Info
```

## Using the Helper Class

The `Helper::setActive()` function highlights active menu items:

```php
// In your routes
Route::name('admin.')->middleware('admin')->group(function () {
    Route::resource('letters', LetterController::class);
});

// In your view
<div class="kt-menu-item {{ Helper::setActive('admin/letters') }}">
```

The active class is determined by the current route.

## Flash Messages

Display messages after actions:

```blade
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
```

## Best Practices for Admin Panel

1. **Localization**: Always use `__()` helper for text (supports `en`, `de`)
2. **Active states**: Use `Helper::setActive()` for menu highlighting
3. **Permissions**: Check authorization in controllers and views
4. **Icons**: Use consistent Keenthemes icon set
5. **Forms**: Always include CSRF token with `@csrf`
6. **Responsive**: Test on mobile since navbar has mobile dropdown support
7. **Pagination**: Show pagination for large lists
8. **Search/Filter**: Add search functionality for resource lists
9. **Confirmation**: Ask confirmation before destructive operations
10. **Breadcrumbs**: Consider adding breadcrumbs for navigation clarity

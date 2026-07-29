@extends('layouts.admin.app')
@section('title', __('Categories'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Categories') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Categories') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
            <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.categories.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus-circle text-lg me-1"></i>
                    {{ __('Add new') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid xl:grid-cols-4 gap-5 lg:gap-7.5 mb-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Total categories') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Active') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Inactive') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Root categories') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['parents'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card kt-card-grid mb-7.5">
        <div class="kt-card-header">
            <div>
                <h3 class="kt-card-title">{{ __('Filter categories') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Search translated names and change locale context.') }}</div>
            </div>
        </div>
        <div class="kt-card-content p-5">
            <form action="{{ route('admin.categories.index') }}" method="GET">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                    <div class="lg:col-span-3">
                        <label class="kt-form-label">{{ __('Locale') }}</label>
                        <select name="locale" class="kt-input w-full select2">
                            @foreach ($locales ?? [] as $item)
                                <option value="{{ $item }}" {{ ($locale ?? '') === $item ? 'selected' : '' }}>
                                    {{ strtoupper($item) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-7">
                        <label class="kt-form-label">{{ __('Keyword Search') }}</label>
                        <div class="kt-input">
                            <i class="ki-filled ki-magnifier"></i>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search by name') }}" />
                        </div>
                    </div>
                    <div class="lg:col-span-2 flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary w-full justify-center">
                            <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-outline w-full justify-center">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-card kt-card-grid">
        <div class="kt-card-header">
            <div>
                <h3 class="kt-card-title">{{ __('Category list') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Manage parents, translation data, and publishing state.') }}</div>
            </div>
        </div>
        <div class="kt-card-table">
            <div class="kt-table-wrapper kt-scrollable-x-auto">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Parent') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            @php
                                $translation = $category->translationFor($locale ?? config('blog.default_locale'));
                                $parentTranslation = $category->parent?->translationFor($locale ?? config('blog.default_locale'));
                                $statusClasses = $category->status === 'active' ? 'kt-badge-success' : 'kt-badge-secondary';
                            @endphp
                            <tr>
                                <td class="font-medium text-secondary-foreground">{{ $category->id }}</td>
                                <td>
                                    <div class="font-medium text-mono">{{ $translation?->name ?? '-' }}</div>
                                    @if ($translation?->description)
                                        <div class="text-sm text-secondary-foreground">
                                            {{ \Illuminate\Support\Str::limit($translation->description, 90) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $parentTranslation?->name ?? '-' }}</td>
                                <td>
                                    <span class="kt-badge {{ $statusClasses }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="kt-btn kt-btn-sm kt-btn-danger">
                                                <i class="ki-filled ki-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary-foreground py-10">
                                    {{ __('No items found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-5">
            {{ $categories->links() }}
        </div>
    </div>
@endsection

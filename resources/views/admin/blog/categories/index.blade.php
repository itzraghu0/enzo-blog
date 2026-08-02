@extends('layouts.admin.app')
@section('title', __('Categories'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
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
            <div class="flex flex-wrap items-center gap-1.5 lg:gap-3.5">
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

    <div class="kt-card mb-7.5">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Filter categories') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Search categories and control Laravel pagination.') }}</div>
            </div>
        </div>

        <div class="kt-card-content border-b border-border p-5">
            <form action="{{ route('admin.categories.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:items-end">
                    <div class="kt-form-item xl:col-span-3">
                        <label class="kt-form-label">{{ __('Locale') }}</label>
                        <div class="kt-form-control">
                            <select name="locale" class="kt-input w-full select2">
                                @foreach ($locales ?? [] as $item)
                                    <option value="{{ $item }}" @selected(($filters['locale'] ?? $locale ?? '') === $item)>
                                        {{ strtoupper($item) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="kt-form-item xl:col-span-4">
                        <label class="kt-form-label">{{ __('Search') }}</label>
                        <div class="kt-form-control">
                            <label class="kt-input w-full">
                                <i class="ki-filled ki-magnifier"></i>
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    placeholder="{{ __('Search by name') }}">
                            </label>
                        </div>
                    </div>
                    <div class="kt-form-item xl:col-span-3">
                        <label class="kt-form-label">{{ __('Status') }}</label>
                        <div class="kt-form-control">
                            <select name="status" class="kt-input w-full">
                                <option value="">{{ __('All') }}</option>
                                <option value="active" @selected(($filters['status'] ?? null) === 'active')>{{ __('Active') }}</option>
                                <option value="inactive" @selected(($filters['status'] ?? null) === 'inactive')>{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="kt-form-item xl:col-span-2">
                        <label class="kt-form-label">{{ __('Per page') }}</label>
                        <div class="kt-form-control">
                            <select name="per_page" class="kt-input w-full">
                                @foreach ($perPageOptions as $count)
                                    <option value="{{ $count }}" @selected((int) ($filters['per_page'] ?? 20) === $count)>
                                        {{ $count }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:max-w-64">
                    <button type="submit" class="kt-btn kt-btn-primary justify-center">
                        <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-outline justify-center">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="kt-card kt-card-grid min-w-full overflow-hidden">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Category list') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Manage parents, translation data, and publishing state.') }}</div>
            </div>
        </div>

        <div class="kt-card-content p-6 lg:p-7.5">
            <div id="categories_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-fixed kt-table-border">
                    <thead>
                        <tr>
                            <th class="w-[420px]">
                                <span class="kt-table-col">
                                    <span class="kt-table-col-label">{{ __('Category') }}</span>
                                    <span class="kt-table-col-sort"></span>
                                </span>
                            </th>
                            <th class="w-[260px]">
                                <span class="kt-table-col">
                                    <span class="kt-table-col-label">{{ __('Parent') }}</span>
                                    <span class="kt-table-col-sort"></span>
                                </span>
                            </th>
                            <th class="w-[180px]">
                                <span class="kt-table-col">
                                    <span class="kt-table-col-label">{{ __('Status') }}</span>
                                    <span class="kt-table-col-sort"></span>
                                </span>
                            </th>
                            <th class="w-[120px] text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            @php
                                $translation = $category->translationFor($locale ?? config('blog.default_locale'));
                                $parentTranslation = $category->parent?->translationFor(
                                    $locale ?? config('blog.default_locale'),
                                );
                                $statusClasses =
                                    $category->status === 'active' ? 'kt-badge-success' : 'kt-badge-secondary';
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($category->previewMedia)
                                            <img src="{{ $category->previewMedia->variantUrl('sm') ?? $category->previewMedia->url() }}"
                                                alt="{{ $category->previewMedia->alt_text ?? ($translation?->name ?? '') }}"
                                                class="size-12 rounded-xl border border-border object-cover">
                                        @else
                                            <div
                                                class="size-12 rounded-xl bg-gradient-to-br from-emerald-900 to-lime-600 text-white flex items-center justify-center font-semibold text-base">
                                                {{ strtoupper(\Illuminate\Support\Str::substr($translation?->name ?? 'C', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex flex-col gap-1.5 min-w-0">
                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                                class="leading-none font-medium text-sm text-mono hover:text-primary truncate">
                                                {{ $translation?->name ?? '-' }}
                                            </a>
                                            <span
                                                class="text-sm text-secondary-foreground font-normal">#{{ $category->id }}</span>
                                            @if ($translation?->description)
                                                <span class="text-sm text-secondary-foreground truncate">
                                                    {{ \Illuminate\Support\Str::limit($translation->description, 90) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm text-foreground font-normal">{{ $parentTranslation?->name ?? '-' }}</td>
                                <td>
                                    <span class="kt-badge {{ $statusClasses }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="kt-btn kt-btn-icon kt-btn-bg-light kt-btn-active-light-primary kt-btn-sm">
                                            <i class="ki-filled ki-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                            data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="kt-btn kt-btn-icon kt-btn-bg-light kt-btn-active-light-danger kt-btn-sm">
                                                <i class="ki-filled ki-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary-foreground py-10">
                                    {{ __('No items found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="kt-card-footer flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-secondary-foreground">
                {{ __('Showing') }} {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} {{ __('of') }}
                {{ $categories->total() }}
            </div>
            {{ $categories->links() }}
        </div>
    </div>
@endsection

@push('script')
    <script>
        jQuery(function($) {
            $('[data-confirm-delete]').on('submit', function(event) {
                if (!confirm(@json(__('Are you sure?')))) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush

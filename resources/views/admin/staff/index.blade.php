@extends('layouts.admin.app')
@section('title', __('Staff'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Staff') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Staff') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.staff.create') }}" class="kt-btn kt-btn-primary">
                    <i class="ki-filled ki-plus-circle text-lg me-1"></i>
                    {{ __('Add new') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@php
    $roleClasses = [
        1 => 'kt-badge-danger',
        2 => 'kt-badge-primary',
        3 => 'kt-badge-success',
    ];
@endphp

@section('content')
    <div class="grid xl:grid-cols-4 gap-5 lg:gap-7.5 mb-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Total staff') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Admins') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['admins'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Editors') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['editors'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Authors') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['authors'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card mb-7.5">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Filter staff') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Search staff.') }}
                </div>
            </div>

        </div>

        <div class="kt-card-content border-b border-border p-5">
            <form id="staff-filter-form" action="{{ route('admin.staff.index') }}" method="GET">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12 xl:items-end">
                    <div class="kt-form-item xl:col-span-5">
                        <label class="kt-form-label">{{ __('Search') }}</label>
                        <div class="kt-form-control">
                            <label class="kt-input w-full">
                                <i class="ki-filled ki-magnifier"></i>
                                <input name="search" placeholder="{{ __('Search staff') }}" type="text"
                                    value="{{ $filters['search'] ?? '' }}">
                            </label>
                        </div>
                    </div>
                    <div class="kt-form-item xl:col-span-3">
                        <label class="kt-form-label">{{ __('Role') }}</label>
                        <div class="kt-form-control">
                            <select name="role" class="kt-input w-full">
                                <option value="">{{ __('All roles') }}</option>
                                @foreach ($roleOptions as $value => $label)
                                    <option value="{{ $value }}" @selected((string) ($filters['role'] ?? '') === (string) $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
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
                    <div class="kt-form-item xl:col-span-2">
                        <label class="kt-form-label">{{ __('Status') }}</label>
                        <div class="kt-form-control">
                            <select name="verified" class="kt-input w-full">
                                <option value="">{{ __('All') }}</option>
                                <option value="1" @selected(($filters['verified'] ?? null) === true)>{{ __('Verified') }}</option>
                                <option value="0" @selected(($filters['verified'] ?? null) === false)>{{ __('Pending') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 sm:max-w-64">
                    <button type="submit" class="kt-btn kt-btn-primary justify-center">
                        <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                    </button>
                    <a href="{{ route('admin.staff.index') }}" class="kt-btn kt-btn-outline justify-center">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

    </div>

    <div class="kt-card kt-card-grid min-w-full overflow-hidden">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Staff list') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Admin, editor, and author accounts in a compact team view.') }}
                </div>
            </div>
        </div>

        <div class="kt-card-content p-6 lg:p-7.5">
            <div id="staff_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-fixed kt-table-border">
                        <thead>
                            <tr>
                                <th class="w-[320px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Staff member') }}</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="w-[220px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Role') }}</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="w-[220px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Status') }}</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="w-[200px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Joined') }}</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="w-[120px] text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($staff as $user)
                                @php
                                    $initial = strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 1));
                                    $roleLabel = $roleOptions[$user->role] ?? __('User');
                                    $roleClass = $roleClasses[$user->role] ?? 'kt-badge-secondary';
                                    $statusClass = $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-12 rounded-full bg-gradient-to-br from-slate-900 to-sky-600 text-white flex items-center justify-center font-semibold text-base">
                                                {{ $initial }}
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <a href="{{ route('admin.staff.edit', $user) }}"
                                                    class="leading-none font-medium text-sm text-mono hover:text-primary">
                                                    {{ $user->name }}
                                                </a>
                                                <span
                                                    class="text-sm text-secondary-foreground font-normal">#{{ $user->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="kt-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                    </td>
                                    <td>
                                        <span class="kt-badge {{ $statusClass }}">
                                            {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-foreground font-normal">
                                        {{ $user->created_at?->format('d M, Y') }}
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap items-center justify-end gap-2">
                                            <a href="{{ route('admin.staff.edit', $user) }}"
                                                class="kt-btn kt-btn-icon kt-btn-bg-light kt-btn-active-light-primary kt-btn-sm">
                                                <i class="ki-filled ki-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.staff.destroy', $user) }}" method="POST"
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
                                    <td colspan="5" class="text-center text-secondary-foreground py-10">
                                        {{ __('No items found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="kt-card-footer flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between">
            <div class="text-sm text-secondary-foreground">
                {{ __('Showing') }} {{ $staff->firstItem() ?? 0 }}-{{ $staff->lastItem() ?? 0 }} {{ __('of') }}
                {{ $staff->total() }}
            </div>
            {{ $staff->links() }}
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

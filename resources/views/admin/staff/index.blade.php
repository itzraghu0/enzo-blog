@extends('layouts.admin.app')
@section('title', __('Staff'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
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
            <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
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

    @php
        $staffTabs = [
            '' => __('All'),
            \App\Models\User::ROLE_ADMIN => __('Admins'),
            \App\Models\User::ROLE_EDITOR => __('Editors'),
            \App\Models\User::ROLE_AUTHOR => __('Authors'),
        ];
    @endphp

    <div class="kt-card kt-card-grid min-w-full overflow-hidden">
        <div class="kt-card-content border-b border-border p-6 lg:p-7.5">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl">
                    <div class="text-xs font-semibold uppercase tracking-[0.24em] text-secondary-foreground">
                        {{ __('Account teams') }}
                    </div>
                    <h3 class="mt-2 text-2xl font-semibold text-mono">
                        {{ __('Staff list') }}
                    </h3>
                    <p class="mt-2 text-sm text-secondary-foreground">
                        {{ __('Admin, editor, and author accounts in a compact team view.') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <label class="kt-input min-w-[260px]">
                        <i class="ki-filled ki-magnifier"></i>
                        <input form="staff-filter-form" name="search" placeholder="{{ __('Search staff') }}" type="text" value="{{ $filters['search'] ?? '' }}">
                    </label>
                    <label class="kt-label whitespace-nowrap">
                        {{ __('Verified only') }}
                        <input form="staff-filter-form" class="kt-switch kt-switch-sm" name="verified" type="checkbox" value="1" @checked((bool) ($filters['verified'] ?? false))>
                    </label>
                    <a href="{{ route('admin.staff.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus-circle text-lg me-1"></i>
                        {{ __('Add new') }}
                    </a>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @foreach ($staffTabs as $value => $label)
                    <a href="{{ route('admin.staff.index', array_filter(['role' => $value])) }}"
                        class="kt-btn kt-btn-sm {{ (string) ($filters['role'] ?? '') === (string) $value ? 'kt-btn-primary' : 'kt-btn-outline' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form id="staff-filter-form" action="{{ route('admin.staff.index') }}" method="GET" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                    <div class="lg:col-span-3">
                        <label class="kt-form-label">{{ __('Role') }}</label>
                        <select name="role" class="kt-input w-full">
                            <option value="">{{ __('All roles') }}</option>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected((string) ($filters['role'] ?? '') === (string) $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-3">
                        <label class="kt-form-label">{{ __('Per page') }}</label>
                        <select name="per_page" class="kt-input w-full">
                            @foreach ($perPageOptions as $count)
                                <option value="{{ $count }}" @selected((int) ($filters['per_page'] ?? 20) === $count)>
                                    {{ $count }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-6 flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary w-full justify-center">
                            <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                        </button>
                        <a href="{{ route('admin.staff.index') }}" class="kt-btn kt-btn-outline w-full justify-center">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="kt-card-content p-6 lg:p-7.5">
            <div data-kt-datatable="true" id="staff_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-fixed kt-table-border" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th class="w-[60px] text-center">
                                    <input class="kt-checkbox kt-checkbox-sm" type="checkbox">
                                </th>
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
                                    <td class="text-center">
                                        <input class="kt-checkbox kt-checkbox-sm" type="checkbox" value="{{ $user->id }}">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="size-12 rounded-full bg-gradient-to-br from-slate-900 to-sky-600 text-white flex items-center justify-center font-semibold text-base">
                                                {{ $initial }}
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <a href="{{ route('admin.staff.edit', $user) }}" class="leading-none font-medium text-sm text-mono hover:text-primary">
                                                    {{ $user->name }}
                                                </a>
                                                <span class="text-sm text-secondary-foreground font-normal">#{{ $user->id }}</span>
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
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.staff.edit', $user) }}" class="kt-btn kt-btn-icon kt-btn-bg-light kt-btn-active-light-primary kt-btn-sm">
                                                <i class="ki-filled ki-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.staff.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="kt-btn kt-btn-icon kt-btn-bg-light kt-btn-active-light-danger kt-btn-sm">
                                                    <i class="ki-filled ki-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary-foreground py-10">{{ __('No items found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="p-5">
            {{ $staff->links() }}
        </div>
    </div>
@endsection

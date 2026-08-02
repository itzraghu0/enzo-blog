@extends('layouts.admin.app')
@section('title', __('Users'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Users') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Users') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
    $roleClasses = [
        1 => 'kt-badge-danger',
        2 => 'kt-badge-primary',
        3 => 'kt-badge-success',
        4 => 'kt-badge-secondary',
    ];
@endphp

@section('content')
    <div class="grid xl:grid-cols-4 gap-5 lg:gap-7.5 mb-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Registered users') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Verified users') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['verified'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Authors and editors') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['authors'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Viewers') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['viewers'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card kt-card-grid min-w-full mb-7.5">
        <div class="kt-card-header py-5 flex-wrap gap-2">
            <div>
                <h3 class="kt-card-title">{{ __('Registered users') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Search by name or email and filter by role or verification state.') }}</div>
            </div>
            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center">
                <div class="kt-form-item w-full sm:w-auto">
                    <div class="kt-form-control">
                        <label class="kt-input w-full min-w-0 sm:w-auto sm:min-w-[260px]">
                            <i class="ki-filled ki-magnifier"></i>
                            <input form="users-filter-form" name="search" placeholder="{{ __('Search users') }}" type="text" value="{{ $filters['search'] ?? '' }}">
                        </label>
                    </div>
                </div>
                <label class="kt-label whitespace-nowrap">
                    {{ __('Verified only') }}
                    <input form="users-filter-form" class="kt-switch kt-switch-sm" name="verified" type="checkbox" value="1" @checked((bool) ($filters['verified'] ?? false))>
                </label>
            </div>
        </div>

        <div class="kt-card-content p-5">
            <form id="users-filter-form" action="{{ route('admin.users.index') }}" method="GET" class="mb-5">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
                    <div class="kt-form-item lg:col-span-3">
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
                    <div class="kt-form-item lg:col-span-3">
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
                    <div class="lg:col-span-6 grid grid-cols-1 gap-2 sm:grid-cols-2 min-w-0">
                        <button type="submit" class="kt-btn kt-btn-primary w-full justify-center">
                            <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="kt-btn kt-btn-outline w-full justify-center">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="kt-card kt-card-grid">
                <div class="kt-card-header">
                    <div>
                        <h3 class="kt-card-title">{{ __('User list') }}</h3>
                        <div class="text-sm text-secondary-foreground">{{ __('Registered customers, authors, and staff accounts in one place.') }}</div>
                    </div>
                    <div class="text-sm text-secondary-foreground">
                        {{ __('Total entries') }}: <span class="font-semibold text-mono">{{ $users->total() }}</span>
                    </div>
                </div>
                <div class="kt-card-table">
                    <div class="kt-table-wrapper kt-scrollable-x-auto">
                        <table class="kt-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Member') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Joined') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    @php
                                        $initial = strtoupper(\Illuminate\Support\Str::substr($user->name, 0, 1));
                                        $roleLabel = $roleOptions[$user->role] ?? __('User');
                                        $roleClass = $roleClasses[$user->role] ?? 'kt-badge-secondary';
                                        $statusClass = $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2.5">
                                                <div class="size-10 rounded-full bg-gradient-to-br from-slate-900 to-sky-600 text-white flex items-center justify-center font-semibold">
                                                    {{ $initial }}
                                                </div>
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="leading-none font-medium text-sm text-mono">
                                                        {{ $user->name }}
                                                    </span>
                                                    <span class="text-sm text-secondary-foreground font-normal">
                                                        #{{ $user->id }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-foreground font-normal">
                                            <a href="mailto:{{ $user->email }}" class="hover:text-primary">
                                                {{ $user->email }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="kt-badge {{ $roleClass }}">
                                                {{ $roleLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="kt-badge {{ $statusClass }}">
                                                {{ $user->email_verified_at ? __('Verified') : __('Pending') }}
                                            </span>
                                        </td>
                                        <td class="text-foreground font-normal">
                                            {{ $user->created_at?->format('Y-m-d H:i') }}
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
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

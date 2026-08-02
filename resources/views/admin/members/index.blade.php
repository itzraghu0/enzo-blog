@extends('layouts.admin.app')
@section('title', __('Members'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Members') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Members') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid xl:grid-cols-3 gap-5 lg:gap-7.5 mb-7.5">
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Registered members') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Verified members') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['verified'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Pending members') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['pending'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card mb-7.5">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Filter members') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Search members and control Laravel pagination.') }}
                </div>
            </div>
        </div>

        <div class="kt-card-content border-b border-border p-5">
            <form id="members-filter-form" action="{{ route('admin.members.index') }}" method="GET">
                <div class="grid grid-cols-3 gap-2 xl:items-end">
                    <div class="kt-form-item xl:col-span-7">
                        <label class="kt-form-label">{{ __('Search') }}</label>
                        <div class="kt-form-control">
                            <label class="kt-input w-full">
                                <i class="ki-filled ki-magnifier"></i>
                                <input name="search" placeholder="{{ __('Search members') }}" type="text"
                                    value="{{ $filters['search'] ?? '' }}">
                            </label>
                        </div>
                    </div>
                    <div class="kt-form-item xl:col-span-3">
                        <label class="kt-form-label">{{ __('Verification') }}</label>
                        <div class="kt-form-control">
                            <select name="verified" class="kt-input w-full">
                                <option value="">{{ __('All') }}</option>
                                <option value="1" @selected(($filters['verified'] ?? null) === true)>{{ __('Verified') }}</option>
                                <option value="0" @selected(($filters['verified'] ?? null) === false)>{{ __('Pending') }}</option>
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

                <div class="mt-4 grid grid-cols-3 gap-2">
                    <button type="submit" class="kt-btn kt-btn-primary justify-center">
                        <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                    </button>
                    <a href="{{ route('admin.members.index') }}" class="kt-btn kt-btn-outline justify-center">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>

    </div>

    <div class="kt-card kt-card-grid min-w-full overflow-hidden">
        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Members') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Frontend users who signed up from the public blog.') }}
                </div>
            </div>
        </div>

        <div class="kt-card-content p-6 lg:p-7.5">
            <div data-kt-datatable="true" id="members_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-fixed kt-table-border" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th class="w-[320px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Member') }}</span>
                                        <span class="kt-table-col-sort"></span>
                                    </span>
                                </th>
                                <th class="w-[280px]">
                                    <span class="kt-table-col">
                                        <span class="kt-table-col-label">{{ __('Email') }}</span>
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($members as $member)
                                @php
                                    $initial = strtoupper(\Illuminate\Support\Str::substr($member->name, 0, 1));
                                    $statusClass = $member->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="size-12 rounded-full bg-gradient-to-br from-slate-900 to-sky-600 text-white flex items-center justify-center font-semibold text-base">
                                                {{ $initial }}
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span
                                                    class="leading-none font-medium text-sm text-mono">{{ $member->name }}</span>
                                                <span
                                                    class="text-sm text-secondary-foreground font-normal">#{{ $member->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-foreground font-normal">
                                        <a href="mailto:{{ $member->email }}"
                                            class="hover:text-primary break-all">{{ $member->email }}</a>
                                    </td>
                                    <td>
                                        <span class="kt-badge {{ $statusClass }}">
                                            {{ $member->email_verified_at ? __('Verified') : __('Pending') }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-foreground font-normal">
                                        {{ $member->created_at?->format('d M, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary-foreground py-10">
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
                {{ __('Showing') }} {{ $members->firstItem() ?? 0 }}-{{ $members->lastItem() ?? 0 }} {{ __('of') }}
                {{ $members->total() }}
            </div>
            {{ $members->links() }}
        </div>
    </div>
@endsection

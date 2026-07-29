@extends('layouts.admin.app')
@section('title', __('Members'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
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

    @php
        $memberTabs = [
            '' => __('All'),
            '1' => __('Verified'),
            '0' => __('Pending'),
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
                        {{ __('Members') }}
                    </h3>
                    <p class="mt-2 text-sm text-secondary-foreground">
                        {{ __('Frontend users who signed up from the public blog.') }}
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <label class="kt-input min-w-[260px]">
                        <i class="ki-filled ki-magnifier"></i>
                        <input form="members-filter-form" name="search" placeholder="{{ __('Search members') }}" type="text" value="{{ $filters['search'] ?? '' }}">
                    </label>
                    <label class="kt-label whitespace-nowrap">
                        {{ __('Verified only') }}
                        <input form="members-filter-form" class="kt-switch kt-switch-sm" name="verified" type="checkbox" value="1" @checked((bool) ($filters['verified'] ?? false))>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap items-center gap-2">
                @foreach ($memberTabs as $value => $label)
                    <a href="{{ route('admin.members.index', array_filter(['verified' => $value === '' ? null : $value])) }}"
                        class="kt-btn kt-btn-sm {{ (string) ($filters['verified'] ?? '') === (string) $value ? 'kt-btn-primary' : 'kt-btn-outline' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form id="members-filter-form" action="{{ route('admin.members.index') }}" method="GET" class="mt-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-end">
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
                    <div class="lg:col-span-9 flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary w-full justify-center">
                            <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                        </button>
                        <a href="{{ route('admin.members.index') }}" class="kt-btn kt-btn-outline w-full justify-center">
                            {{ __('Reset') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="kt-card-content p-6 lg:p-7.5">
            <div data-kt-datatable="true" id="members_table">
                <div class="kt-scrollable-x-auto">
                    <table class="kt-table table-fixed kt-table-border" data-kt-datatable-table="true">
                        <thead>
                            <tr>
                                <th class="w-[60px] text-center">
                                    <input class="kt-checkbox kt-checkbox-sm" type="checkbox">
                                </th>
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
                                    <td class="text-center">
                                        <input class="kt-checkbox kt-checkbox-sm" type="checkbox" value="{{ $member->id }}">
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="size-12 rounded-full bg-gradient-to-br from-slate-900 to-sky-600 text-white flex items-center justify-center font-semibold text-base">
                                                {{ $initial }}
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <span class="leading-none font-medium text-sm text-mono">{{ $member->name }}</span>
                                                <span class="text-sm text-secondary-foreground font-normal">#{{ $member->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-foreground font-normal">
                                        <a href="mailto:{{ $member->email }}" class="hover:text-primary break-all">{{ $member->email }}</a>
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
                                    <td colspan="5" class="text-center text-secondary-foreground py-10">{{ __('No items found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="p-5">
            {{ $members->links() }}
        </div>
    </div>
@endsection

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
                    <i class="ki-outline ki-plus-circle text-lg me-1"></i>
                    {{ __('Add new') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5 items-stretch">
        <div class="lg:col-span-3">
            <div class="kt-card kt-card-grid h-full min-w-full">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">{{ __('Search') }} {{ __('Categories') }}</h3>
                </div>
                <div class="kt-card-content">
                    <form action="{{ route('admin.categories.index') }}" method="GET" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Locale') }}</label>
                                <select name="locale" class="select2 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50">
                                    @foreach ($locales ?? [] as $item)
                                        <option value="{{ $item }}" {{ ($locale ?? '') === $item ? 'selected' : '' }}>
                                            {{ strtoupper($item) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Keyword Search') }}</label>
                                <input type="text" name="search" value="{{ $search ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary/50"
                                    placeholder="{{ __('Search by name') }}" />
                            </div>
                            <div class="flex items-end gap-2 mt-1">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    <i class="ki-outline ki-magnifier fs-2"></i>{{ __('search') }}
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="kt-btn kt-btn-primary">
                                    <i class="ki-outline ki-arrows-loop fs-2"></i> {{ __('reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="kt-card kt-card-grid h-full min-w-full">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">{{ __('List') }}</h3>
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
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    @php
                                        $translation = $category->translationFor($locale ?? config('blog.default_locale'));
                                        $parentTranslation = $category->parent?->translationFor($locale ?? config('blog.default_locale'));
                                    @endphp
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>{{ $translation?->name ?? '-' }}</td>
                                        <td>{{ $parentTranslation?->name ?? '-' }}</td>
                                        <td>{{ ucfirst($category->status) }}</td>
                                        <td class="text-center flex justify-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                                                <i class="ki-filled ki-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('No items found') }}</td>
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
        </div>
    </div>
@endsection

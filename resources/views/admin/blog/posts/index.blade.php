@extends('layouts.admin.app')
@section('title', __('Posts'))

@push('style')
    <link href="{{ url('assets/vendors/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Posts') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Posts') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
            <div class="flex items-center flex-wrap gap-1.5 lg:gap-3.5">
                <a href="{{ route('admin.posts.create') }}" class="kt-btn kt-btn-primary">
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
                <div class="text-sm text-secondary-foreground">{{ __('Total posts') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Published') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['published'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Drafts') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['draft'] ?? 0 }}</div>
            </div>
        </div>
        <div class="kt-card">
            <div class="kt-card-content p-5">
                <div class="text-sm text-secondary-foreground">{{ __('Featured') }}</div>
                <div class="text-2xl font-semibold text-mono mt-1">{{ $stats['featured'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <div class="kt-card kt-card-grid mb-7.5">
        <div class="kt-card-header">
            <div>
                <h3 class="kt-card-title">{{ __('Filter posts') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Search by title and switch between locales.') }}</div>
            </div>
        </div>
        <div class="kt-card-content p-5">
            <form action="{{ route('admin.posts.index') }}" method="GET">
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
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Search by title') }}" />
                        </div>
                    </div>
                    <div class="lg:col-span-2 flex gap-2">
                        <button type="submit" class="kt-btn kt-btn-primary w-full justify-center">
                            <i class="ki-filled ki-magnifier me-1"></i>{{ __('Search') }}
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="kt-btn kt-btn-outline w-full justify-center">
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
                <h3 class="kt-card-title">{{ __('Post list') }}</h3>
                <div class="text-sm text-secondary-foreground">{{ __('Manage translated titles, categories, and publishing status.') }}</div>
            </div>
        </div>
        <div class="kt-card-table">
            <div class="kt-table-wrapper kt-scrollable-x-auto">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Author') }}</th>
                            <th>{{ __('Categories') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Published') }}</th>
                            <th class="text-end">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            @php
                                $translation = $post->translationFor($locale ?? config('blog.default_locale'));
                                $statusClasses = match ($post->status) {
                                    'published' => 'kt-badge-success',
                                    'draft' => 'kt-badge-warning',
                                    'archived' => 'kt-badge-secondary',
                                    default => 'kt-badge-info',
                                };
                            @endphp
                            <tr>
                                <td class="font-medium text-secondary-foreground">{{ $post->id }}</td>
                                <td>
                                    <div class="font-medium text-mono">{{ $translation?->title ?? '-' }}</div>
                                    @if ($translation?->excerpt)
                                        <div class="text-sm text-secondary-foreground">
                                            {{ \Illuminate\Support\Str::limit($translation->excerpt, 90) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $post->author?->name ?? '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($post->categories as $category)
                                            @php $categoryTranslation = $category->translationFor($locale ?? config('blog.default_locale')); @endphp
                                            <span class="kt-badge kt-badge-outline kt-badge-primary">
                                                {{ $categoryTranslation?->name ?? '-' }}
                                            </span>
                                        @empty
                                            <span class="text-secondary-foreground">-</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <span class="kt-badge {{ $statusClasses }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td>{{ optional($post->published_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.posts.edit', $post) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                                            <i class="ki-filled ki-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
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
                                <td colspan="7" class="text-center text-secondary-foreground py-10">
                                    {{ __('No items found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-5">
            {{ $posts->links() }}
        </div>
    </div>
@endsection

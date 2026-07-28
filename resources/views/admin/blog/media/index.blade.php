@extends('layouts.admin.app')
@section('title', __('Media Library'))

@push('style')
    <link href="{{ url('assets/vendors/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Media Library') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Media') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('List') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid lg:grid-cols-3 gap-5 lg:gap-7.5">
        <div class="lg:col-span-3">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">{{ __('Upload Media') }}</h3>
                </div>
                <div class="kt-card-content p-5">
                    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-5 items-end">
                        @csrf
                        <div>
                            <label class="kt-form-label">{{ __('File') }}</label>
                            <input type="file" name="file" class="kt-input w-full" required>
                        </div>
                        <div>
                            <label class="kt-form-label">{{ __('Alt text') }}</label>
                            <input type="text" name="alt_text" class="kt-input w-full" placeholder="{{ __('Optional') }}">
                        </div>
                        <div>
                            <label class="kt-form-label">{{ __('Title') }}</label>
                            <input type="text" name="title" class="kt-input w-full" placeholder="{{ __('Optional') }}">
                        </div>
                        <div>
                            <label class="kt-form-label">{{ __('Caption') }}</label>
                            <input type="text" name="caption" class="kt-input w-full" placeholder="{{ __('Optional') }}">
                        </div>
                        <div>
                            <button type="submit" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus-circle"></i>
                                {{ __('Upload') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">{{ __('All Media') }}</h3>
                </div>
                <div class="kt-card-content p-5">
                    <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-5">
                        @forelse ($mediaItems as $media)
                            <div class="rounded-xl border border-border bg-background overflow-hidden">
                                <img src="{{ $media->url() }}" alt="{{ $media->alt_text ?? $media->original_name ?? '' }}" class="h-44 w-full object-cover">
                                <div class="p-4 space-y-2">
                                    <div class="font-medium text-mono text-sm truncate">{{ $media->original_name }}</div>
                                    <div class="text-xs text-secondary-foreground truncate">{{ $media->path }}</div>
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button"
                                            class="kt-btn kt-btn-sm kt-btn-mono"
                                            onclick="navigator.clipboard.writeText('{{ $media->url() }}')">
                                            {{ __('Copy URL') }}
                                        </button>
                                        <form action="{{ route('admin.media.destroy', $media) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="kt-btn kt-btn-sm kt-btn-danger">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-secondary-foreground">
                                {{ __('No media uploaded yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="p-5">
                    {{ $mediaItems->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

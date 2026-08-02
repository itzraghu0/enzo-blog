@extends('layouts.admin.app')
@section('title', __('Settings'))

@section('breadcrumb')
    <div class="mb-5 lg:mb-7.5">
        <div
            class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex items-center justify-between flex-wrap gap-5 mx-auto">
            <div class="flex flex-col justify-center items-start flex-wrap gap-1 lg:gap-2">
                <h1 class="font-medium text-lg text-mono">{{ __('Settings') }}</h1>
                <div class="flex items-center gap-1 text-sm font-normal">
                    <span class="text-secondary-foreground">{{ __('Home') }}</span>
                    <span class="text-muted-foreground text-sm">/</span>
                    <span class="text-secondary-foreground">{{ __('Settings') }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.settings.update') }}" method="POST" class="kt-card">
        @csrf
        @method('PUT')

        <div class="kt-card-header flex-wrap gap-3 py-5">
            <div>
                <h3 class="kt-card-title">{{ __('Site links and footer settings') }}</h3>
                <div class="text-sm text-secondary-foreground">
                    {{ __('Manage frontend social, newsletter, FAQ, and footer link values.') }}
                </div>
            </div>
            <button type="submit" class="kt-btn kt-btn-primary">
                <i class="ki-filled ki-check-circle me-1"></i>
                {{ __('Save settings') }}
            </button>
        </div>

        <div class="kt-card-content p-5">
            <div class="grid gap-4">
                @foreach ($settings as $setting)
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-[320px_minmax(0,1fr)] lg:items-start">
                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Key') }}</label>
                            <div class="kt-form-control">
                                <input type="text" class="kt-input w-full" value="{{ $setting['key'] }}" readonly>
                            </div>
                            <div class="kt-form-description">{{ __('Used by frontend header and footer.') }}</div>
                        </div>

                        <div class="kt-form-item">
                            <label class="kt-form-label">{{ __('Value') }}</label>
                            <div class="kt-form-control">
                                <input type="text" name="settings[{{ $setting['key'] }}]" class="kt-input w-full"
                                    value="{{ $setting['value'] }}">
                            </div>
                            @error("settings.{$setting['key']}")
                                <div class="kt-form-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-col gap-3 border-t border-border pt-5 sm:flex-row sm:items-center">
                <button type="submit" class="kt-btn kt-btn-primary justify-center sm:w-auto">
                    {{ __('Save settings') }}
                </button>
                <a href="{{ route('admin.dashboard') }}" class="kt-btn kt-btn-outline justify-center sm:w-auto">
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </form>
@endsection

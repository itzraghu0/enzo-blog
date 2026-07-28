@extends('frontend.auth.layout')

@section('content')
    <div class="kt-card max-w-[420px] w-full mx-4">
        <form action="{{ route('login') }}" class="kt-card-content flex flex-col gap-5 p-10" method="POST">
            @csrf
            <div class="text-center mb-2.5">
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">{{ __('Sign in') }}</h3>
                <div class="flex items-center justify-center font-medium">
                    <span class="text-sm text-secondary-foreground me-1.5">{{ __('Need an account?') }}</span>
                    <a class="text-sm link" href="{{ route('register') }}">{{ __('Sign up') }}</a>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Email') }}</label>
                <input class="kt-input" name="email" placeholder="email@email.com" type="email" value="{{ old('email') }}" autofocus />
                @error('email')
                    <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Password') }}</label>
                <div class="kt-input" data-kt-toggle-password="true">
                    <input name="password" placeholder="{{ __('Password') }}" type="password" />
                    <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5" data-kt-toggle-password-trigger="true" type="button">
                        <span class="kt-toggle-password-active:hidden"><i class="ki-filled ki-eye text-muted-foreground"></i></span>
                        <span class="hidden kt-toggle-password-active:block"><i class="ki-filled ki-eye-slash text-muted-foreground"></i></span>
                    </button>
                </div>
                @error('password')
                    <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                @enderror
            </div>

            <label class="kt-label">
                <input class="kt-checkbox kt-checkbox-sm" type="checkbox" name="remember_me" value="1" id="remember_me" />
                <span class="kt-checkbox-label">{{ __('Remember Me') }}</span>
            </label>

            <button class="kt-btn kt-btn-primary flex justify-center grow" type="submit">{{ __('Sign In') }}</button>
        </form>
    </div>
@endsection

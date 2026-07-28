@extends('frontend.auth.layout')

@section('content')
    <div class="kt-card max-w-[420px] w-full mx-4">
        <form action="{{ route('register.store') }}" class="kt-card-content flex flex-col gap-5 p-10" method="POST">
            @csrf
            <div class="text-center mb-2.5">
                <h3 class="text-lg font-medium text-mono leading-none mb-2.5">{{ __('Sign up') }}</h3>
                <div class="flex items-center justify-center font-medium">
                    <span class="text-sm text-secondary-foreground me-1.5">{{ __('Already have an account?') }}</span>
                    <a class="text-sm link" href="{{ route('login') }}">{{ __('Sign in') }}</a>
                </div>
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Name') }}</label>
                <input class="kt-input" name="name" type="text" value="{{ old('name') }}" />
                @error('name')
                    <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Email') }}</label>
                <input class="kt-input" name="email" type="email" value="{{ old('email') }}" />
                @error('email')
                    <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Password') }}</label>
                <input class="kt-input" name="password" type="password" />
                @error('password')
                    <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex flex-col gap-1">
                <label class="kt-form-label font-normal text-mono">{{ __('Confirm Password') }}</label>
                <input class="kt-input" name="password_confirmation" type="password" />
            </div>

            <button class="kt-btn kt-btn-primary flex justify-center grow" type="submit">{{ __('Create account') }}</button>
        </form>
    </div>
@endsection

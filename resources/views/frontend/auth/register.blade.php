@extends('frontend.auth.layout')

@section('content')
    <div class="grid lg:grid-cols-2 grow">
        <div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1">
            <div class="kt-card max-w-[370px] w-full">
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
                        <label class="kt-form-label text-mono">{{ __('Name') }}</label>
                        <input class="kt-input" name="name" type="text" value="{{ old('name') }}" />
                        @error('name')
                            <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="kt-form-label text-mono">{{ __('Email') }}</label>
                        <input class="kt-input" name="email" placeholder="email@email.com" type="email"
                            value="{{ old('email') }}" />
                        @error('email')
                            <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="kt-form-label font-normal text-mono">{{ __('Password') }}</label>
                        <div class="kt-input" data-kt-toggle-password="true">
                            <input name="password" placeholder="{{ __('Password') }}" type="password" />
                            <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                                data-kt-toggle-password-trigger="true" type="button">
                                <span class="kt-toggle-password-active:hidden">
                                    <i class="ki-filled ki-eye text-muted-foreground"></i>
                                </span>
                                <span class="hidden kt-toggle-password-active:block">
                                    <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                                </span>
                            </button>
                        </div>
                        @error('password')
                            <span class="kt-badge kt-badge-outline kt-badge-destructive items-center">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="kt-form-label font-normal text-mono">{{ __('Confirm Password') }}</label>
                        <div class="kt-input" data-kt-toggle-password="true">
                            <input name="password_confirmation" placeholder="{{ __('Re-enter Password') }}"
                                type="password" />
                            <button class="kt-btn kt-btn-sm kt-btn-ghost kt-btn-icon bg-transparent! -me-1.5"
                                data-kt-toggle-password-trigger="true" type="button">
                                <span class="kt-toggle-password-active:hidden">
                                    <i class="ki-filled ki-eye text-muted-foreground"></i>
                                </span>
                                <span class="hidden kt-toggle-password-active:block">
                                    <i class="ki-filled ki-eye-slash text-muted-foreground"></i>
                                </span>
                            </button>
                        </div>
                    </div>

                    <button class="kt-btn kt-btn-primary flex justify-center grow" type="submit">
                        {{ __('Create account') }}
                    </button>
                </form>
            </div>
        </div>

        <div
            class="lg:rounded-xl lg:border lg:border-border lg:m-5 order-1 lg:order-2 bg-top xxl:bg-center xl:bg-cover bg-no-repeat branded-bg">
            <div class="flex flex-col p-8 lg:p-16 gap-4">
                <a href="{{ route('blog.index') }}">
                    <img class="h-[28px] max-w-none" src="{{ url('assets/media/app/mini-logo.svg') }}" />
                </a>
                <div class="flex flex-col gap-3">
                    <h3 class="text-2xl font-semibold text-mono">
                        {{ __('Join the discussion') }}
                    </h3>
                    <div class="text-base font-medium text-secondary-foreground">
                        {{ __('Create a reader account to verify your email, subscribe to authors, and join threaded comments.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

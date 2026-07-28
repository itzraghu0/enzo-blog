@extends('frontend.auth.layout')

@section('content')
    <div class="kt-card max-w-[520px] w-full mx-4">
        <div class="kt-card-content flex flex-col gap-5 p-10 text-center">
            <div>
                <h3 class="text-lg font-medium text-mono leading-none mb-3">{{ __('Verify your email') }}</h3>
                <p class="text-sm text-secondary-foreground">
                    {{ __('We sent a verification link to your email address. Please verify it before logging in.') }}
                </p>
            </div>

            @if ($email !== '')
                <div class="rounded-xl border border-border bg-muted/20 p-4 text-sm text-secondary-foreground">
                    {{ __('Email') }}: {{ $email }}
                </div>
            @endif

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('login') }}" class="kt-btn kt-btn-mono">{{ __('Back to login') }}</a>
                <a href="{{ route('register') }}" class="kt-btn kt-btn-primary">{{ __('Create another account') }}</a>
            </div>
        </div>
    </div>
@endsection

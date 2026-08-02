<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta content="follow, index" name="robots" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <title>{{ config('app.name', 'Blog') }}</title>
    <link href="{{ URL('assets/media/app/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180" />
    <link href="{{ URL('assets/media/app/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon.ico') }}" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ URL('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ URL('assets/css/styles.css') }}" rel="stylesheet" />
    <link href="{{ URL('assets/css/custom.css') }}" rel="stylesheet" />
    @stack('style')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background">
    @if (session('success'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[min(92vw,520px)]">
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-[min(92vw,520px)]">
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 shadow-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    @yield('content')

    <script src="{{ URL('assets/js/core.bundle.js') }}"></script>
    <script src="{{ URL('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ URL('assets/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL('assets/js/custom.js') }}"></script>
    @stack('script')
</body>
</html>

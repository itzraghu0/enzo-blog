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
    <style>
        .page-bg {
            background-image: radial-gradient(circle at top left, rgba(15, 23, 42, 0.08), transparent 35%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }
    </style>
    @stack('style')
</head>
<body class="antialiased flex h-full text-base text-foreground bg-background">
    <div class="flex items-center justify-center grow bg-center bg-no-repeat page-bg">
        <div class="w-full">
            @if (session('success'))
                <div class="mx-auto max-w-[420px] w-full mb-4 px-4">
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-auto max-w-[420px] w-full mb-4 px-4">
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
    <script src="{{ URL('assets/js/core.bundle.js') }}"></script>
    <script src="{{ URL('assets/vendors/ktui/ktui.min.js') }}"></script>
    @stack('script')
</body>
</html>

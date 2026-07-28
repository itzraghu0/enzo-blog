<!DOCTYPE html>
<html lang="{{ $locale ?? app()->getLocale() ?? config('blog.default_locale', 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Blog'))</title>
    <meta name="description" content="@yield('metaDescription', $metaDescription ?? '')">
    <link href="{{ URL('assets/css/styles.css') }}" rel="stylesheet" />
    @php
        $siteName = config('app.name', 'Blog');
        $siteUrl = url('/');
        $siteDescription = __('Language based publishing');
        $baseStructuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $siteUrl.'/#organization',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'description' => $siteDescription,
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $siteUrl.'/#website',
                    'name' => $siteName,
                    'url' => $siteUrl,
                    'description' => $siteDescription,
                    'publisher' => [
                        '@id' => $siteUrl.'/#organization',
                    ],
                    'inLanguage' => config('blog.supported_locales', [config('blog.default_locale', 'en')]),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($baseStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('structured-data')
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(15, 23, 42, 0.08), transparent 30%),
                radial-gradient(circle at top right, rgba(71, 85, 105, 0.08), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
        }
    </style>
</head>
<body class="text-slate-900 antialiased">
    <div class="max-w-6xl mx-auto px-6 py-8">
        @if (session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                {{ session('error') }}
            </div>
        @endif

        <header class="flex items-center justify-between gap-4 mb-10">
            <div>
                <a href="{{ route('blog.index') }}" class="text-2xl font-semibold tracking-tight">{{ config('app.name', 'Blog') }}</a>
                <p class="text-sm text-slate-500">{{ __('Language based publishing') }}</p>
            </div>
            <div class="flex items-center gap-3">
                @foreach (config('blog.supported_locales', [config('blog.default_locale', 'en')]) as $item)
                    <a href="{{ request()->fullUrlWithQuery(['locale' => $item]) }}" class="px-3 py-1 rounded-full border text-sm {{ ($locale ?? '') === $item ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200' }}">
                        {{ strtoupper($item) }}
                    </a>
                @endforeach
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-white text-slate-700 text-sm border border-slate-200">{{ __('Sign up') }}</a>
                @endauth
            </div>
        </header>

        @yield('content')
    </div>
</body>
</html>

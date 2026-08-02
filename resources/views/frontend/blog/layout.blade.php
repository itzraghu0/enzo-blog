@php
    $appName = config('app.name', 'Blog');
    $currentLocale = request('locale', $locale ?? (app()->getLocale() ?: config('blog.default_locale', 'en')));
    $supportedLocales = config('blog.supported_locales', ['en', 'de']);
    $authUser = auth()->user();
    $userInitials = $authUser
        ? collect(explode(' ', trim($authUser->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
            ->implode('')
        : null;
    $siteSettings = app(\App\Services\SiteSettingService::class)->values();
    $footerPosts = \Illuminate\Support\Facades\DB::table('posts')
        ->leftJoin('post_translations as pt_locale', function ($join) use ($currentLocale): void {
            $join->on('pt_locale.post_id', '=', 'posts.id')
                ->where('pt_locale.locale', '=', $currentLocale);
        })
        ->leftJoin('post_translations as pt_default', function ($join): void {
            $join->on('pt_default.post_id', '=', 'posts.id')
                ->where('pt_default.locale', '=', config('blog.default_locale', 'de'));
        })
        ->where('posts.status', 'published')
        ->whereNull('posts.deleted_at')
        ->whereNotNull('posts.published_at')
        ->orderByDesc('posts.published_at')
        ->limit(3)
        ->get([
            'posts.published_at',
            \Illuminate\Support\Facades\DB::raw('COALESCE(pt_locale.title, pt_default.title) as title'),
            \Illuminate\Support\Facades\DB::raw('COALESCE(pt_locale.slug, pt_default.slug) as slug'),
        ])
        ->map(function (object $post): object {
            $post->published_at = $post->published_at ? \Illuminate\Support\Carbon::parse($post->published_at) : null;

            return $post;
        });
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $appName)</title>
    <meta name="description" content="@yield('metaDescription', __('A multilingual editorial blog with authors, categories, and structured data.'))">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

    @stack('head')
    @stack('structured-data')
</head>
<body class="min-h-screen">
    <div class="progress-bar" id="reading-progress"></div>

    <header class="relative z-50">
        <div class="site-topbar">
            <div class="editorial-shell flex min-h-[42px] flex-wrap items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="site-topbar-cell">
                        <div class="site-control" data-site-dropdown>
                            <button type="button" class="site-control-button" data-site-dropdown-toggle aria-expanded="false">
                                <span>{{ strtoupper($currentLocale) }}</span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </button>
                            <div class="site-control-menu site-control-menu-left">
                                @foreach ($supportedLocales as $supportedLocale)
                                    <a href="{{ route('set-language', $supportedLocale) }}" class="site-control-item">
                                        <span class="font-semibold">{{ strtoupper($supportedLocale) }}</span>
                                        @if ($supportedLocale === $currentLocale)
                                            <i class="fa-solid fa-check ms-auto text-[color:var(--primary)]"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <div class="site-topbar-cell gap-2">
                        <a href="{{ $siteSettings['social_facebook_url'] ?? '#' }}" class="site-social site-social-facebook" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="{{ $siteSettings['social_instagram_url'] ?? '#' }}" class="site-social site-social-instagram" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="{{ $siteSettings['social_whatsapp_url'] ?? '#' }}" class="site-social site-social-whatsapp" aria-label="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="{{ $siteSettings['social_tiktok_url'] ?? '#' }}" class="site-social site-social-tiktok" aria-label="TikTok">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    <a href="{{ $siteSettings['topbar_newsletter_url'] ?? '#' }}" class="site-topbar-link">{{ __('Newsletter') }}</a>
                    <a href="{{ $siteSettings['topbar_faq_url'] ?? '#' }}" class="site-topbar-link site-topbar-end">{{ __('FAQ') }}</a>
                </div>
            </div>
        </div>

        <div class="site-brandbar">
            <div class="editorial-shell grid items-center gap-5 py-4 lg:grid-cols-[1fr_auto_1fr]">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined site-contact-icon">phone_in_talk</span>
                    <div>
                        <div class="site-contact-title">{{ __('Call') }}</div>
                        <div class="site-contact-text">{{ $siteSettings['footer_phone'] ?? '+49 6187 - 9959050' }}</div>
                    </div>
                </div>

                <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="flex justify-center">
                    <img src="{{ asset('logo.png') }}" alt="{{ $appName }}" class="site-logo">
                </a>

                <div class="flex items-center justify-between gap-6 lg:justify-end">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined site-contact-icon">mail</span>
                        <div>
                            <div class="site-contact-title">{{ __('Für Fragen') }}</div>
                            <div class="site-contact-text">{{ $siteSettings['footer_email'] ?? config('mail.from.address', 'info@example.com') }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="site-control" data-site-dropdown>
                            <button type="button" class="site-icon-button" data-site-dropdown-toggle aria-expanded="false" aria-label="{{ __('Theme') }}">
                                <span class="material-symbols-outlined text-[22px]">contrast</span>
                            </button>
                            <div class="site-control-menu">
                                <button type="button" class="site-control-item" data-theme-option="light">
                                    <span class="material-symbols-outlined text-[18px]">light_mode</span>
                                    {{ __('Light') }}
                                </button>
                                <button type="button" class="site-control-item" data-theme-option="dark">
                                    <span class="material-symbols-outlined text-[18px]">dark_mode</span>
                                    {{ __('Dark') }}
                                </button>
                            </div>
                        </div>

                        @auth
                            <div class="site-control" data-site-dropdown>
                                <button type="button" class="site-user-initials" data-site-dropdown-toggle aria-expanded="false" aria-label="{{ __('Account') }}">
                                    {{ $userInitials ?: 'U' }}
                                </button>
                                <div class="site-control-menu">
                                    <div class="px-3 py-2">
                                        <div class="font-semibold text-sm">{{ $authUser->name }}</div>
                                        <div class="text-xs text-[color:var(--muted)] break-all">{{ $authUser->email }}</div>
                                    </div>
                                    @if ($authUser->canManageBlog())
                                        <a href="{{ route('admin.dashboard') }}" class="site-control-item">
                                            <span class="material-symbols-outlined text-[18px]">dashboard</span>
                                            {{ __('Admin') }}
                                        </a>
                                    @endif
                                    <button type="button" class="site-control-item" data-submit-form="logout-form">
                                        <span class="material-symbols-outlined text-[18px]">logout</span>
                                        {{ __('Logout') }}
                                    </button>
                                </div>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="site-icon-button" aria-label="{{ __('Sign in') }}">
                                <span class="material-symbols-outlined text-[24px]">person</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="py-8 md:py-10">
        @if (session('success'))
            <div class="editorial-shell mb-6">
                <div class="editorial-card border-emerald-200 bg-emerald-50/90 px-5 py-4 text-emerald-900">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="editorial-shell mb-6">
                <div class="editorial-card border-rose-200 bg-rose-50/90 px-5 py-4 text-rose-900">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer mt-16">
        <div class="editorial-shell py-12 md:py-16">
            <div class="grid gap-10 md:grid-cols-2 xl:grid-cols-[1fr_1.1fr_1fr_1.05fr] xl:gap-16">
                <div>
                    <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="inline-flex">
                        <img src="{{ asset('logo.png') }}" alt="{{ $appName }}" class="site-footer-logo">
                    </a>
                    <div class="mt-6 grid gap-3">
                        <div class="flex gap-3 site-footer-text">
                            <i class="fa-solid fa-location-dot site-footer-icon"></i>
                            <span>{{ $siteSettings['footer_address'] ?? '' }}</span>
                        </div>
                        <div class="flex gap-3 site-footer-text">
                            <i class="fa-solid fa-phone-volume site-footer-icon"></i>
                            <span>{{ $siteSettings['footer_phone'] ?? '' }}</span>
                        </div>
                        <div class="flex gap-3 site-footer-text">
                            <i class="fa-solid fa-fax site-footer-icon"></i>
                            <span>{{ $siteSettings['footer_fax'] ?? '' }}</span>
                        </div>
                        <div class="flex gap-3 site-footer-text">
                            <i class="fa-regular fa-envelope site-footer-icon"></i>
                            <a href="mailto:{{ $siteSettings['footer_email'] ?? '' }}" class="site-footer-link">{{ $siteSettings['footer_email'] ?? '' }}</a>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="site-footer-title uppercase">{{ __('Aktuelle Blog Beiträge') }}</div>
                    <div class="mt-6">
                        @forelse ($footerPosts as $footerPost)
                            @if ($footerPost->slug)
                                <a href="{{ route('blog.show', $footerPost->slug) }}" class="site-footer-post block">
                                    <span class="site-footer-link block">{{ \Illuminate\Support\Str::limit($footerPost->title ?? __('Untitled'), 34) }}</span>
                                    <span class="site-footer-text block">{{ optional($footerPost->published_at)->format('d.m.Y') }}</span>
                                </a>
                            @endif
                        @empty
                            <div class="site-footer-text">{{ __('No posts found') }}</div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <div class="site-footer-title">{{ __('Service') }}</div>
                    <div class="mt-6 grid gap-4">
                        <a href="{{ $siteSettings['footer_service_help_url'] ?? '#' }}" class="site-footer-link">{{ __('Hilfe & Support') }}</a>
                        <a href="{{ $siteSettings['footer_service_contact_url'] ?? '#' }}" class="site-footer-link">{{ __('Kontakt') }}</a>
                        <a href="{{ $siteSettings['footer_service_payment_url'] ?? '#' }}" class="site-footer-link">{{ __('Zahlungsmöglichkeiten') }}</a>
                        <a href="{{ $siteSettings['footer_service_shipping_url'] ?? '#' }}" class="site-footer-link">{{ __('Versandinformationen') }}</a>
                    </div>
                </div>

                <div>
                    <div class="site-footer-title">{{ __('Gesetzliche Informationen') }}</div>
                    <div class="mt-6 grid gap-4">
                        <a href="{{ $siteSettings['footer_legal_terms_url'] ?? '#' }}" class="site-footer-link">{{ __('AGB') }}</a>
                        <a href="{{ $siteSettings['footer_legal_privacy_url'] ?? '#' }}" class="site-footer-link">{{ __('Datenschutz') }}</a>
                        <a href="{{ $siteSettings['footer_legal_imprint_url'] ?? '#' }}" class="site-footer-link">{{ __('Impressum') }}</a>
                        <a href="{{ $siteSettings['footer_legal_cancellation_url'] ?? '#' }}" class="site-footer-link">{{ __('Widerrufsrecht') }}</a>
                        <a href="{{ $siteSettings['footer_legal_sitemap_url'] ?? '#' }}" class="site-footer-link">{{ __('Sitemap') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    @stack('scripts')
</body>
</html>

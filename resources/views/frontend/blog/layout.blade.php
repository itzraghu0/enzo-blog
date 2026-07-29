@php
    $appName = config('app.name', 'Blog');
    $currentLocale = request('locale', $locale ?? (app()->getLocale() ?: config('blog.default_locale', 'en')));
    $supportedLocales = config('blog.supported_locales', ['en', 'de']);
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
    <script>
        (function () {
            try {
                if (localStorage.getItem('theme') === 'dark') {
                    document.documentElement.classList.add('dark');
                }
            } catch (error) {
                // Ignore theme storage issues.
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        :root {
            --surface: #f7f9fb;
            --surface-2: #f2f4f6;
            --surface-3: #eceef0;
            --surface-4: #e6e8ea;
            --text: #191c1e;
            --muted: #434655;
            --border: #c3c6d7;
            --primary: #004ac6;
            --primary-soft: #dbe1ff;
            --primary-strong: #003ea8;
            --shadow: 0 18px 50px rgba(25, 28, 30, 0.06);
            --shadow-hover: 0 24px 60px rgba(0, 74, 198, 0.10);
        }

        html.dark {
            --surface: #0b1220;
            --surface-2: #10192c;
            --surface-3: #152036;
            --surface-4: #1c2a44;
            --text: #eef2ff;
            --muted: #b7c2df;
            --border: #2a3a5d;
            --shadow: 0 20px 50px rgba(0, 0, 0, 0.24);
            --shadow-hover: 0 24px 60px rgba(36, 99, 235, 0.22);
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            background: var(--surface);
            color: var(--text);
            font-family: Inter, sans-serif;
        }

        .editorial-shell {
            max-width: 1280px;
            margin: 0 auto;
            padding-left: 24px;
            padding-right: 24px;
        }

        .editorial-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(195, 198, 215, 0.72);
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
            backdrop-filter: blur(12px);
        }

        html.dark .editorial-card {
            background: rgba(16, 25, 44, 0.96);
        }

        .editorial-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(0, 74, 198, 0.20);
        }

        .editorial-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border-radius: 9999px;
            padding: 0.5rem 0.9rem;
            background: var(--surface-2);
            color: var(--text);
            border: 1px solid var(--border);
            font-size: 0.875rem;
            font-weight: 600;
        }

        .editorial-chip-primary {
            background: rgba(0, 74, 198, 0.08);
            color: var(--primary);
            border-color: rgba(0, 74, 198, 0.16);
        }

        .editorial-input,
        .editorial-select,
        .editorial-textarea {
            width: 100%;
            border-radius: 0.875rem;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text);
            padding: 0.9rem 1rem;
            outline: none;
            transition: border-color 180ms ease, box-shadow 180ms ease, background 180ms ease;
        }

        .editorial-input:focus,
        .editorial-select:focus,
        .editorial-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 74, 198, 0.12);
            background: rgba(255, 255, 255, 0.98);
        }

        .editorial-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.875rem;
            padding: 0.875rem 1.1rem;
            font-size: 0.9rem;
            font-weight: 700;
            transition: transform 180ms ease, background 180ms ease, color 180ms ease, border-color 180ms ease;
        }

        .editorial-button:hover {
            transform: translateY(-1px);
        }

        .editorial-button-primary {
            background: var(--primary);
            color: #fff;
        }

        .editorial-button-primary:hover {
            background: var(--primary-strong);
        }

        .editorial-button-secondary {
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 70;
            width: 0;
            height: 2px;
            background: var(--primary);
        }

        .article-content {
            color: var(--muted);
            font-size: 1.05rem;
            line-height: 1.9;
        }

        .article-content > * + * {
            margin-top: 1.15rem;
        }

        .article-content h2,
        .article-content h3,
        .article-content h4 {
            color: var(--text);
            font-weight: 700;
            line-height: 1.25;
            margin-top: 2.1rem;
        }

        .article-content h2 {
            font-size: 1.8rem;
        }

        .article-content h3 {
            font-size: 1.4rem;
        }

        .article-content p,
        .article-content ul,
        .article-content ol,
        .article-content blockquote {
            margin: 0;
        }

        .article-content ul,
        .article-content ol {
            padding-left: 1.25rem;
        }

        .article-content ul li {
            position: relative;
            padding-left: 0.75rem;
            margin: 0.5rem 0;
            list-style: none;
        }

        .article-content ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.75rem;
            width: 0.35rem;
            height: 0.35rem;
            border-radius: 9999px;
            background: var(--primary);
        }

        .article-content blockquote {
            padding: 1.25rem 1.5rem;
            border-radius: 1rem;
            border-left: 4px solid var(--primary);
            background: var(--surface-2);
            color: var(--text);
            font-style: italic;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>

    @stack('head')
    @stack('structured-data')
</head>
<body class="min-h-screen">
    <div class="progress-bar" id="reading-progress"></div>

    <header class="sticky top-0 z-50 border-b border-[color:var(--border)] bg-[color:var(--surface)]/92 backdrop-blur-xl">
        <div class="editorial-shell">
            <div class="flex h-20 items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="text-xl font-extrabold tracking-[-0.02em] text-[color:var(--text)]">
                        {{ $appName }}
                    </a>
                    <nav class="hidden items-center gap-3 md:flex">
                        <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="editorial-chip">{{ __('Home') }}</a>
                        <a href="{{ route('blog.index', ['locale' => $currentLocale, 'sort' => 'recent_desc']) }}#latest" class="editorial-chip">{{ __('Latest') }}</a>
                        <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}#categories" class="editorial-chip">{{ __('Categories') }}</a>
                    </nav>
                </div>

                <div class="flex items-center gap-2">
                    <div class="hidden items-center gap-2 sm:flex">
                        @foreach ($supportedLocales as $supportedLocale)
                            <a href="{{ route('set-language', $supportedLocale) }}" class="editorial-chip {{ $supportedLocale === $currentLocale ? 'editorial-chip-primary' : '' }}">
                                {{ strtoupper($supportedLocale) }}
                            </a>
                        @endforeach
                    </div>

                    <button type="button" class="editorial-chip" onclick="window.toggleTheme && window.toggleTheme()">
                        <span class="material-symbols-outlined text-[18px]">dark_mode</span>
                        <span class="hidden sm:inline">{{ __('Theme') }}</span>
                    </button>

                    @auth
                        @if (auth()->user()->canManageBlog())
                            <a href="{{ route('admin.dashboard') }}" class="editorial-button editorial-button-primary">
                                {{ __('Admin') }}
                            </a>
                        @else
                            <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="editorial-button editorial-button-secondary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="editorial-button editorial-button-secondary">{{ __('Sign in') }}</a>
                        <a href="{{ route('register') }}" class="editorial-button editorial-button-primary">{{ __('Sign up') }}</a>
                    @endauth
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

    <footer class="mt-16 border-t border-[color:var(--border)] bg-[color:var(--surface-2)]">
        <div class="editorial-shell py-10">
            <div class="grid gap-8 md:grid-cols-3">
                <div>
                    <div class="text-lg font-extrabold tracking-[-0.02em]">{{ $appName }}</div>
                    <p class="mt-3 max-w-sm text-sm leading-7 text-[color:var(--muted)]">
                        {{ __('A multilingual editorial system for authors, members, and admin staff.') }}
                    </p>
                </div>
                <div>
                    <div class="text-sm font-bold uppercase tracking-[0.18em] text-[color:var(--muted)]">{{ __('Explore') }}</div>
                    <div class="mt-4 flex flex-col gap-3 text-sm">
                        <a href="{{ route('blog.index', ['locale' => $currentLocale]) }}" class="hover:text-[color:var(--primary)]">{{ __('Home') }}</a>
                        <a href="{{ route('register') }}" class="hover:text-[color:var(--primary)]">{{ __('Join as member') }}</a>
                        <a href="{{ route('login') }}" class="hover:text-[color:var(--primary)]">{{ __('Sign in') }}</a>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-bold uppercase tracking-[0.18em] text-[color:var(--muted)]">{{ __('Language') }}</div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($supportedLocales as $supportedLocale)
                            <a href="{{ route('set-language', $supportedLocale) }}" class="editorial-chip {{ $supportedLocale === $currentLocale ? 'editorial-chip-primary' : '' }}">
                                {{ strtoupper($supportedLocale) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            const progress = document.getElementById('reading-progress');

            function updateProgress() {
                const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const width = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
                if (progress) {
                    progress.style.width = width + '%';
                }
            }

            window.addEventListener('scroll', updateProgress, { passive: true });
            updateProgress();

            window.toggleTheme = function () {
                const root = document.documentElement;
                const isDark = root.classList.toggle('dark');
                try {
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                } catch (error) {
                    // Ignore theme storage issues.
                }
            };
        })();
    </script>
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">

<head>
    <meta name="_token" content="{{ csrf_token() }}" />

    <title>Blog Admin | @yield('title')</title>
    <meta charset="utf-8" />
    <meta content="follow, index" name="robots" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <meta content="" name="description" />
    <link href="{{ URL('assets/media/app/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180" />
    <link href="{{ URL('assets/media/app/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon.ico') }}" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ URL('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ URL('assets/css/styles.css') }}" rel="stylesheet" />

    @stack('style')
</head>

<body class="antialiased flex h-full text-base text-foreground bg-background [--header-height:78px]">
    <script>
        const defaultThemeMode = 'light';
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (document.documentElement.hasAttribute('data-kt-theme-mode')) {
                themeMode = document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>

    <div class="flex grow flex-col in-data-kt-[sticky-header=on]:pt-(--header-height)">
        @include('layouts.admin.header')
        @include('layouts.admin.navbar')

        <div class="w-full kt-container-fluid flex flex-col px-0">
            <main class="flex flex-col grow" id="content" role="content">
                @yield('breadcrumb')

                <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] mx-auto">
                    <div class="grid">
                        @yield('content')
                    </div>
                </div>

                @include('layouts.admin.footer')
            </main>
        </div>
    </div>

    <script src="{{ URL('assets/js/core.bundle.js') }}"></script>
    <script src="{{ URL('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ URL('old-assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL('assets/js/sweetalert/sweetalert.js') }}"></script>

    <script>
        @if (session('error'))
            KTToast.show({
                message: "{{ session('error') }}",
                icon: '<i class="ki-filled text-destructive text-xl"></i>',
                progress: true,
                pauseOnHover: true,
                class: 'bg-error text-white'
            });
        @endif

        @if (session('success'))
            KTToast.show({
                message: "{{ session('success') }}",
                icon: '<i class="ki-filled ki-check-circle text-success text-xl"></i>',
                progress: true,
                pauseOnHover: true,
                class: 'bg-success text-white'
            });
        @endif
    </script>

    @stack('script')
</body>

</html>

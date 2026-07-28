<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">

<head>
    <meta name="_token" content="{{ csrf_token() }}" />

    <title>Admin | @yield('title')</title>
    <meta charset="utf-8" />
    <meta content="follow, index" name="robots" />
    <link href="https://127.0.0.1:8001/metronic-tailwind-html/demo9/index.html" rel="canonical" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="@keenthemes" name="twitter:site" />
    <meta content="@keenthemes" name="twitter:creator" />
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="Metronic - Tailwind CSS " name="twitter:title" />
    <meta content="" name="twitter:description" />
    <link href="{{ URL('assets/media/app/apple-touch-icon.png') }}" rel="apple-touch-icon" sizes="180x180" />
    <link href="{{ URL('assets/media/app/favicon-32x32.png') }}" rel="icon" sizes="32x32" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon-16x16.png') }}" rel="icon" sizes="16x16" type="image/png" />
    <link href="{{ URL('assets/media/app/favicon.ico') }}" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="{{ URL('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet" />
    <link href="{{ URL('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet" />
    <link href="{{ URL('assets/css/styles.css') }}" rel="stylesheet" />

    <style>
        .go-back-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            transition: background 0.3s ease;
        }

        .go-back-btn:hover {
            background-color: #0056b3;
            text-decoration: none;
        }

        .go-back-btn .material-icons {
            vertical-align: middle;
        }
    </style>

    @stack('style')
</head>

<body class="antialiased flex h-full text-base text-foreground bg-background [--header-height:78px]">
    <!-- Theme Mode -->
    <script>
        const defaultThemeMode = 'light'; // light|dark|system
        let themeMode;

        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (
                document.documentElement.hasAttribute('data-kt-theme-mode')
            ) {
                themeMode =
                    document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light';
            }

            document.documentElement.classList.add(themeMode);
        }
    </script>
    <!-- End of Theme Mode -->
    <!-- Page -->
    <!-- Main -->
    <div class="flex grow flex-col in-data-kt-[sticky-header=on]:pt-(--header-height)">

        @include('layouts.admin.header')

        @include('layouts.admin.navbar')

        <!-- Wrapper Container -->
        <div class="w-full kt-container-fluid flex flex-col px-0">
            <!-- Content -->
            <main class="flex flex-col grow" id="content" role="content">
                <!-- Breadcrumb -->
                @yield('breadcrumb')

                <!-- Container -->
                <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] mx-auto">
                    <div class="grid">
                        @yield('content')
                    </div>
                </div>
                <!-- End of Container -->

                @include('layouts.admin.footer')
            </main>
            <!-- End of Content -->
        </div>
        <!-- End of Wrapper Container -->
    </div>

    <!-- End of Main -->

    <!-- Change password -->
    <div class="kt-modal" data-kt-modal="true" id="change_password">
        <div class="kt-modal-content max-w-[500px] top-5 lg:top-[15%]">
            <div class="kt-modal-header">
                <h3 class="kt-modal-title">
                    Change password
                </h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost shrink-0" data-kt-modal-dismiss="true">
                    <i class="ki-filled ki-cross">
                    </i>
                </button>
            </div>
            <div class="kt-modal-body grid gap-5 px-0 py-5">
                <div class="flex flex-col px-5 gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-mono font-semibold text-sm">
                            {{ __('Old Password') }}
                        </label>
                    </div>
                    <label class="kt-input">
                        <input type="password" name="old_password">
                        <button class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost -me-2">
                            <i class="ki-filled ki-lock">
                            </i>
                        </button>
                    </label>
                </div>

                <div class="flex flex-col px-5 gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-mono font-semibold text-sm">
                            {{ __('New Password') }}
                        </label>
                    </div>
                    <label class="kt-input">
                        <input type="password" name="new_password">
                        <button class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost -me-2">
                            <i class="ki-filled ki-lock">
                            </i>
                        </button>
                    </label>
                </div>

                <div class="flex flex-col px-5 gap-2.5">
                    <div class="flex flex-center gap-1">
                        <label class="text-mono font-semibold text-sm">
                            {{ __('Confirm Password') }}
                        </label>
                    </div>
                    <label class="kt-input">
                        <input type="password" name="confirm_password">
                        <button class="kt-btn kt-btn-icon kt-btn-sm kt-btn-ghost -me-2">
                            <i class="ki-filled ki-lock">
                            </i>
                        </button>
                    </label>
                </div>

                <div class="border-b border-b-border"></div>
                <div class="flex flex-col px-5 gap-4">
                    <button class="kt-btn kt-btn-primary justify-center" id="changePassword">
                        {{ _('Change password') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (isset($goBackUrl))
        <a href="{{ $goBackUrl }}" class="go-back-btn" title="{{ __('Go Back') }}">
            <i class="ki-outline ki-arrow-left text-lg me-1">
            </i>
        </a>
    @endif

    <!-- Scripts -->
    <script src="{{ URL('assets/js/core.bundle.js') }}"></script>
    <script src="{{ URL('assets/vendors/ktui/ktui.min.js') }}"></script>
    <script src="{{ URL('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL('assets/js/widgets/general.js') }}"></script>
    <script src="{{ URL('old-assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ URL('assets/js/sweetalert/sweetalert.js') }}"></script>
    <!-- End of Scripts -->
    <script>
        $(document).ready(function() {
            function bindMenuSearch(inputId) {
                const $input = $(inputId);
                let $results = null;

                function ensureResultList() {
                    if (!$results) {
                        $results = $(
                            `<ul class="absolute left-0 w-full bg-white dark:bg-muted border-muted rounded-xl shadow-lg z-50 text-sm hidden" style="top:100%"></ul>`
                        );
                        $input.after($results);
                    }
                }

                $input.on('keyup', function() {
                    ensureResultList();
                    const keyword = $input.val().toLowerCase().trim();
                    $results.empty();

                    if (!keyword) {
                        $results.hide();
                        return;
                    }

                    $('.kt-menu-item').each(function() {
                        const $title = $(this).find('.kt-menu-title').first();
                        const $link = $(this).find('a').first();
                        const titleText = $title.text().trim();
                        const href = $link.attr('href');

                        if (titleText.toLowerCase().includes(keyword)) {
                            $results.append(`
                            <li class="px-4 py-2 hover:bg-muted/40 cursor-pointer text-gray-900 dark:text-white"
                                data-url="${href}">
                                ${titleText}
                            </li>
                        `);
                        }
                    });

                    $results.toggle($results.children().length > 0);
                });

                // Hide dropdown on outside click
                $(document).on('click', function(e) {
                    if (!$(e.target).closest($input).length && !$results?.is(e.target) && !$results?.has(e
                            .target).length) {
                        $results?.hide();
                    }
                });

                // Click to navigate
                $(document).on('click', `${inputId} + ul > li`, function() {
                    const url = $(this).data('url');
                    if (url) window.location.href = url;
                });
            }

            bindMenuSearch('#desktopMenuSearchInput');
            bindMenuSearch('#mobileMenuSearchInput');
        });
    </script>


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

        $(document).ready(function() {

            setTimeout(function() {
                var $search = $('input[placeholder="Search…"], input[placeholder="Search"]');
                $search.val('Search…').trigger('input').trigger('keyup');
                setTimeout(function() {
                    $search.val('').trigger('input').trigger('keyup');
                }, 1000);

            }, 2000);


            $('#changePassword').on('click', function() {
                var old_password = $('[name="old_password"]').val();
                var new_password = $('[name="new_password"]').val();
                var confirm_password = $('[name="confirm_password"]').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': $('meta[name=_token]').attr('content')
                    }
                });
                var url = "{{ route('change-password') }}";
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        'old_password': old_password,
                        'new_password': new_password,
                        'confirm_password': confirm_password,
                    },
                    success: function(data) {
                        if (
                            typeof data.flag !== "undefiend" && data.flag) {
                            KTToast.show({
                                message: data.message,
                                icon: '<i class="ki-filled ki-lock-check text-success text-xl"></i>',
                                type: 'success',
                                progress: true,
                                pauseOnHover: true,
                                delay: 3000
                            });


                            setInterval(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            if (typeof data.errors !== "undefiend" && data.errors) {
                                $.each(data.errors, function(key, value) {
                                    KTToast.show({
                                        message: value[0],
                                        icon: '<i class="ki-filled ki-information-4 text-destructive text-xl"></i>',
                                        type: 'danger',
                                        progress: true,
                                        pauseOnHover: true,
                                        delay: 4000
                                    });
                                });
                            } else {
                                KTToast.show({
                                    message: data.message,
                                    icon: '<i class="ki-filled ki-information-4 text-destructive text-xl"></i>',
                                    type: 'danger',
                                    progress: true,
                                    pauseOnHover: true,
                                    delay: 4000
                                });
                            }
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            const $entityCardEl = $('#entity-card');
            const hoverDelay = 500;
            let hoverTimeout;

            // Hover IN
            $(document).on('mouseenter', '[data-entity-type][data-entity-id]', function(e) {
                const $hoverElement = $(this);

                clearTimeout(hoverTimeout);

                hoverTimeout = setTimeout(function() {
                    const entityType = $hoverElement.data('entity-type');
                    const entityId = $hoverElement.data('entity-id');

                    if (entityType && entityId) {
                        fetchEntityCard(entityType, entityId, e);
                    }
                }, hoverDelay);
            });

            // Hover OUT
            $(document).on('mouseleave', '[data-entity-type][data-entity-id]', function() {
                clearTimeout(hoverTimeout);
                hideEntityCard();
            });

            // Prevent flicker when hovering card itself
            $entityCardEl.on('mouseleave', function() {
                hideEntityCard();
            });

            function fetchEntityCard(entityType, entityId, mouseEvent) {
                $.ajax({
                    url: `{{ route('entity.card') }}?entity_type=${entityType}&entity_id=${entityId}`,
                    type: 'GET',
                    dataType: 'html',
                    success: function(html) {
                        displayEntityCard(html, mouseEvent);
                    },
                    error: function(err) {
                        console.error('Error fetching entity card:', err);
                    }
                });
            }

            function displayEntityCard(html, mouseEvent) {
                $entityCardEl.html(html);

                // Position near cursor
                let left = mouseEvent.clientX + 15;
                let top = mouseEvent.clientY + 15;

                $entityCardEl.css({
                    left: left,
                    top: top,
                    display: 'flex'
                });

                adjustCardPosition();
            }

            function adjustCardPosition() {
                const rect = $entityCardEl[0].getBoundingClientRect();
                const padding = 10;

                let left = parseInt($entityCardEl.css('left'));
                let top = parseInt($entityCardEl.css('top'));

                if (rect.right > window.innerWidth - padding) {
                    left = window.innerWidth - rect.width - padding;
                }

                if (rect.bottom > window.innerHeight - padding) {
                    top = window.innerHeight - rect.height - padding;
                }

                if (left < padding) left = padding;
                if (top < padding) top = padding;

                $entityCardEl.css({
                    left,
                    top
                });
            }

            function hideEntityCard() {
                $entityCardEl.hide();
            }
        });
    </script>

    <!-- Entity Hover Card Container -->
    <div class="kt-card flex flex-col items-center p-5 lg:py-10" id="entity-card"
        style="display: none; position: fixed; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); z-index: 1000; min-width: 250px; max-width: 300px;">
    </div>

    @stack('script')
</body>

</html>

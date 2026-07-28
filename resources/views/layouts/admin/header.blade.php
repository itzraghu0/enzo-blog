<!-- Header -->
<header class="flex items-center transition-[height] shrink-0 bg-background h-(--header-height)" data-kt-sticky="true"
    data-kt-sticky-class="transition-[height] fixed z-10 top-0 left-0 right-0 shadow-xs backdrop-blur-md bg-background/70 border border-border"
    data-kt-sticky-name="header" data-kt-sticky-offset="100px" id="header">

    <!-- Container -->
    <div
        class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] flex lg:justify-between items-center gap-2.5 mx-auto">

        <!-- Logo -->
        <div class="flex items-center gap-1 lg:w-[400px] grow lg:grow-0">
            <button class="kt-btn kt-btn-icon kt-btn-ghost -ms-2.5 lg:hidden" data-kt-drawer-toggle="#navbar">
                <i class="ki-filled ki-menu"></i>
            </button>
            <div class="flex items-center gap-2">
                <a class="flex items-center shrink-0" href="{{ url('/') }}">
                    <img class="dark:hidden w-8 shrink-0" src="{{ url('/assets/media/app/mini-logo-circle.svg') }}" />
                    <img class="hidden dark:inline-block w-8 shrink-0"
                        src="{{ url('/assets/media/app/mini-logo-circle-dark.svg') }}" />
                </a>
                <h3 class="text-mono text-lg font-medium hidden md:block">LMS</h3>
            </div>

            <!-- Navs -->
            <div class="hidden lg:flex items-center">
                <div class="border-e border-border h-5 mx-4"></div>
                <!-- Nav -->
                <div class="kt-menu kt-menu-default w-[120px]" data-kt-menu="true">
                    <div class="kt-menu-item" data-kt-menu-item-offset="0,0" data-kt-menu-item-placement="bottom-start"
                        data-kt-menu-item-placement-rtl="bottom-end" data-kt-menu-item-toggle="dropdown"
                        data-kt-menu-item-trigger="hover">
                        <button class="kt-menu-toggle kt-btn kt-btn-outline flex-nowrap gap-2">
                            <i class="ki-filled ki-icon text-base!"></i>
                            <span class="hidden md:inline text-nowrap">
                                {{ Session::get('locale') == 'en' ? __('English') : __('German') }}
                            </span>
                            <span class="flex items-center">
                                <i class="ki-filled ki-down text-xs!"></i>
                            </span>
                        </button>
                        <div class="kt-menu-dropdown w-48 py-2 kt-scrollable-y max-h-[250px]">
                            @if (Session::get('locale') == 'en')
                                <div class="kt-menu-item {{ Session::get('locale') == 'de' ? 'active' : '' }}">
                                    <a class="kt-menu-link" href="{{ route('set-language', 'de') }}">
                                        <span class="kt-menu-title">{{ __('German') }}</span>
                                    </a>
                                </div>
                            @else
                                <div class="kt-menu-item {{ Session::get('locale') == 'en' ? 'active' : '' }}">
                                    <a class="kt-menu-link" href="{{ route('set-language', 'en') }}">
                                        <span class="kt-menu-title">{{ __('English') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- End of Nav -->
            </div>
            <!-- End of Navs -->
        </div>
        <!-- End of Logo -->

        <!-- Desktop Search -->
        <div class="relative kt-input hidden lg:flex lg:w-60">
            <i class="ki-filled ki-magnifier"></i>
            <input class="min-w-0 w-full" placeholder="{{ __('Search') }}" type="text" id="desktopMenuSearchInput"
                autocomplete="off" />
        </div>

        <!-- Mobile Search -->
        <div class="kt-menu kt-menu-default lg:hidden" data-kt-menu="true">
            <div class="kt-menu-item" data-kt-menu-item-toggle="dropdown" data-kt-menu-item-trigger="click">
                <button class="kt-menu-toggle kt-btn kt-btn-ghost kt-btn-icon">
                    <i class="ki-filled ki-magnifier"></i>
                </button>
                <div class="kt-menu-dropdown w-72 py-2">
                    <div class="relative kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input class="min-w-0 w-full" placeholder="{{ __('Search') }}" type="text"
                            id="mobileMenuSearchInput" autocomplete="off" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Topbar -->
        <div class="flex items-center gap-2 lg:gap-3.5 lg:w-[400px] justify-end">
            <div class="flex items-center gap-2 me-0.5">
                <!-- User -->
                <div data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px"
                    data-kt-dropdown-offset-rtl="-20px, 10px" data-kt-dropdown-placement="bottom-end"
                    data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click">
                    <button
                        class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-transparent hover:[&_i]:text-primary"
                        data-kt-dropdown-toggle="true">
                        <i class="ki-filled ki-profile-circle text-lg"></i>
                    </button>
                    <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
                        <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
                            <div class="flex items-center gap-2 justify-between">
                                <span class="flex items-center gap-2">
                                    <i class="ki-filled ki-moon text-base text-muted-foreground"></i>
                                    <span class="font-medium text-2sm">Dark Mode</span>
                                </span>
                                <input class="kt-switch" data-kt-theme-switch-state="dark"
                                    data-kt-theme-switch-toggle="true" name="check" type="checkbox" value="1" />
                            </div>

                            <div class="flex items-center gap-2 justify-between cursor-pointer"
                                data-kt-dropdown-dismiss="true" data-kt-modal-toggle="#change_password">
                                <span class="flex items-center gap-2">
                                    <i class="ki-filled ki-lock text-base text-muted-foreground"></i>
                                    <span class="font-medium text-2sm">Change Password</span>
                                </span>
                            </div>

                            <a class="kt-btn kt-btn-outline justify-center w-full" href="#"
                                onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                                {{ __('Log Out') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End of User -->
            </div>
        </div>
    </div>
    <!-- End of Container -->
</header>
<!-- End of Header -->

<!-- Navbar -->
<div class="bg-muted hidden lg:flex lg:items-stretch border-y border-input lg:mb-10 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
    data-kt-drawer="true"
    data-kt-drawer-class="kt-drawer kt-drawer-start fixed z-10 top-0 bottom-0 w-full me-5 max-w-[250px] p-5 lg:p-0 overflow-auto"
    id="navbar">
    <div class="w-full kt-container-fluid px-6 lg:px-8 max-w-[2200px] lg:flex lg:flex-wrap lg:justify-between lg:items-center gap-2 mx-auto">
        <div class="kt-menu items-stretch flex-col lg:flex-row gap-5 lg:gap-7.5 grow lg:grow-0" data-kt-menu="true"
            id="mega_menu">
            @if (auth()->check() && auth()->user()->canManageBlog())
                <div class="kt-menu-item {{ Helper::setActive('admin/posts') }} {{ Helper::setActive('admin/posts/create') }} {{ Helper::setActive('admin/posts/*/edit') }}"
                    data-kt-menu-item-offset="-158px, 0"
                    data-kt-menu-item-placement="bottom-start"
                    data-kt-menu-item-toggle="accordion|lg:dropdown"
                    data-kt-menu-item-trigger="click|lg:hover">
                    <div class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono">
                        <span class="kt-menu-title font-medium text-foreground text-sm">
                            {{ __('Posts') }}
                        </span>
                        <span class="kt-menu-arrow flex lg:hidden">
                            <span class="flex kt-menu-item-show:hidden">
                                <i class="ki-filled ki-plus text-xs text-secondary-foreground"></i>
                            </span>
                            <span class="hidden kt-menu-item-show:inline-flex">
                                <i class="ki-filled ki-minus text-xs text-secondary-foreground"></i>
                            </span>
                        </span>
                    </div>
                    <div class="kt-menu-dropdown">
                        <div class="lg:w-[250px] mt-2 lg:mt-0 lg:border-e lg:border-e-border rounded-xl lg:rounded-l-xl lg:rounded-r-none shrink-0 px-3 py-4 lg:p-7.5 bg-muted/25">
                            <div class="kt-menu kt-menu-default kt-menu-fit flex-col">
                                <div class="kt-menu-item {{ Helper::setActive('admin/posts') }}">
                                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                                        href="{{ route('admin.posts.index') }}">
                                        <span class="kt-menu-icon">
                                            <i class="ki-filled ki-book-open"></i>
                                        </span>
                                        <span class="kt-menu-title grow-0">
                                            {{ __('List') }}
                                        </span>
                                    </a>
                                </div>
                                <div class="kt-menu-item {{ Helper::setActive('admin/posts/create') }}">
                                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                                        href="{{ route('admin.posts.create') }}">
                                        <span class="kt-menu-icon">
                                            <i class="ki-filled ki-plus"></i>
                                        </span>
                                        <span class="kt-menu-title grow-0">
                                            {{ __('Add New') }}
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-menu-item {{ Helper::setActive('admin/categories') }} {{ Helper::setActive('admin/categories/create') }} {{ Helper::setActive('admin/categories/*/edit') }}"
                    data-kt-menu-item-offset="-158px, 0"
                    data-kt-menu-item-placement="bottom-start"
                    data-kt-menu-item-toggle="accordion|lg:dropdown"
                    data-kt-menu-item-trigger="click|lg:hover">
                    <div class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono">
                        <span class="kt-menu-title font-medium text-foreground text-sm">
                            {{ __('Categories') }}
                        </span>
                        <span class="kt-menu-arrow flex lg:hidden">
                            <span class="flex kt-menu-item-show:hidden">
                                <i class="ki-filled ki-plus text-xs text-secondary-foreground"></i>
                            </span>
                            <span class="hidden kt-menu-item-show:inline-flex">
                                <i class="ki-filled ki-minus text-xs text-secondary-foreground"></i>
                            </span>
                        </span>
                    </div>
                    <div class="kt-menu-dropdown">
                        <div class="lg:w-[250px] mt-2 lg:mt-0 lg:border-e lg:border-e-border rounded-xl lg:rounded-l-xl lg:rounded-r-none shrink-0 px-3 py-4 lg:p-7.5 bg-muted/25">
                            <div class="kt-menu kt-menu-default kt-menu-fit flex-col">
                                <div class="kt-menu-item {{ Helper::setActive('admin/categories') }}">
                                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                                        href="{{ route('admin.categories.index') }}">
                                        <span class="kt-menu-icon">
                                            <i class="ki-filled ki-category"></i>
                                        </span>
                                        <span class="kt-menu-title grow-0">
                                            {{ __('List') }}
                                        </span>
                                    </a>
                                </div>
                                <div class="kt-menu-item {{ Helper::setActive('admin/categories/create') }}">
                                    <a class="kt-menu-link border border-transparent kt-menu-link-hover:!bg-background kt-menu-link-hover:border-border kt-menu-item-active:!bg-background kt-menu-item-active:border-border"
                                        href="{{ route('admin.categories.create') }}">
                                        <span class="kt-menu-icon">
                                            <i class="ki-filled ki-plus"></i>
                                        </span>
                                        <span class="kt-menu-title grow-0">
                                            {{ __('Add New') }}
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-menu-item {{ Helper::setActive('admin/media') }}">
                    <a class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono"
                        href="{{ route('admin.media.index') }}">
                        <span class="kt-menu-title font-medium text-foreground text-sm">
                            {{ __('Media') }}
                        </span>
                    </a>
                </div>

                @if (auth()->user()->isAdmin())
                    <div class="kt-menu-item {{ Helper::setActive('admin/members') }}">
                        <a class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono"
                            href="{{ route('admin.members.index') }}">
                            <span class="kt-menu-title font-medium text-foreground text-sm">
                                {{ __('Members') }}
                            </span>
                        </a>
                    </div>

                    <div class="kt-menu-item {{ Helper::setActive('admin/staff') }}">
                        <a class="kt-menu-link lg:py-3.5 border-b border-b-transparent kt-menu-item-active:border-b-mono text-foreground kt-menu-item-hover:text-mono kt-menu-item-active:text-mono kt-menu-item-here:border-b-mono kt-menu-item-here:text-mono"
                            href="{{ route('admin.staff.index') }}">
                            <span class="kt-menu-title font-medium text-foreground text-sm">
                                {{ __('Staff') }}
                            </span>
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

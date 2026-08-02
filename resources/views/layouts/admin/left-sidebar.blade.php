<section>
    <aside id="leftsidebar" class="sidebar">
        <div class="user-info">
            <a href="{{ route('admin.posts.index') }}">
                <div class="image">
                    <img src="{{ url('assets/images/user.png') }}" width="48" height="48" alt="User" />
                </div>
            </a>
            <div class="info-container">
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="email">{{ Auth::user()->email }}</div>
                <div class="btn-group user-helper-dropdown">
                    <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        keyboard_arrow_down
                    </i>
                    <ul class="dropdown-menu pull-right">
                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="javascript:void(0);"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="material-icons">input</i>{{ __('Sign Out') }}
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </ul>
                </div>
            </div>
        </div>

        <div class="menu">
            <ul class="list">
                <li class="header">{{ __('BLOG NAVIGATION') }}</li>
                <li class="{{ Helper::setActive('admin/blog/posts') }}">
                    <a href="{{ route('admin.posts.index') }}">
                        <i class="material-icons">article</i>
                        <span>{{ __('posts') }}</span>
                    </a>
                </li>
                <li class="{{ Helper::setActive('admin/blog/posts/create') }}">
                    <a href="{{ route('admin.posts.create') }}">
                        <i class="material-icons">add</i>
                        <span>{{ __('add_new') }}</span>
                    </a>
                </li>
                <li class="{{ Helper::setActive('admin/blog/categories') }}">
                    <a href="{{ route('admin.categories.index') }}">
                        <i class="material-icons">category</i>
                        <span>{{ __('categories') }}</span>
                    </a>
                </li>
                <li class="{{ Helper::setActive('admin/blog/media') }}">
                    <a href="{{ route('admin.media.index') }}">
                        <i class="material-icons">perm_media</i>
                        <span>{{ __('media') }}</span>
                    </a>
                </li>
                <li class="{{ Helper::setActive('admin/members') }}">
                    <a href="{{ route('admin.members.index') }}">
                        <i class="material-icons">people</i>
                        <span>{{ __('members') }}</span>
                    </a>
                </li>
                @if (auth()->user()->isAdmin())
                    <li class="{{ Helper::setActive('admin/staff') }}">
                        <a href="{{ route('admin.staff.index') }}">
                            <i class="material-icons">admin_panel_settings</i>
                            <span>{{ __('staff') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="legal">
            <div class="copyright">
                &copy; {{ date('Y') }} <a href="{{ route('admin.posts.index') }}">{{ config('app.name') }}</a>.
            </div>
        </div>
    </aside>
</section>

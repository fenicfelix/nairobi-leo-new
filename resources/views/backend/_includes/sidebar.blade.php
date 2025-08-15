<!-- Sidebar -->
<div class="offcanvas offcanvas-start navbar-dark text-nowrap" tabindex="-1" id="sidebar" aria-label="sidebar">

    <div class="offcanvas-header px-3">
        <a href="{{ route('/') }}" class="logo nav-link px-0 pt d-flex align-items-center">
            <img class="mt-5" src="{{ asset(get_template_url() . 'img/' . config('cms.app_icon_light')) }}"
                alt="App Logo" height="40">
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mt-4 px-2 py-3 h-100" data-simplebar>
        <ul class="navbar-nav mb-4" id="mainMenu">
            <li class="nav-label px-2 small mt-3"><small>MAIN MENU</small></li>
            <li class="nav-item">
                <a class="nav-link px-2 d-flex align-items-center gap-3" target="blank" href="{{ route('/') }}">
                    <i class="fas fa-home"></i> <span>Homepage</span>
                </a>
            </li>
            @if ($logged_user->group_id < 3)
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*dashboard*') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*pages*') ? 'active' : '' }}"
                        href="{{ route('pages.index') }}">
                        <i class="fas fa-list"></i> <span>Pages</span>
                    </a>
                </li>
            @endif
            @if ($logged_user->group_id < 5)
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*posts*') ? 'active' : '' }}"
                        href="{{ route('posts.index', 'all') }}">
                        <i class="fas fa-copy"></i> <span>Posts</span>
                    </a>
                </li>
            @endif
            @if ($logged_user->group_id < 3)
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*categories*') ? 'active' : '' }}"
                        href="{{ route('categories.index') }}">
                        <i class="fas fa-layer-group"></i> <span>Categories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*tags*') ? 'active' : '' }}"
                        href="{{ route('tags.index') }}">
                        <i class="fas fa-tags"></i> <span>Tags</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*users*') || request()->is('*my-profile*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="fa fa-users"></i> <span>Users</span>
                    </a>
                </li>
                @if (config('cms.has_tv_section'))
                    <li class="nav-label px-2 small mt-3"><small>TV SECTION</small></li>
                    <li class="nav-item">
                        <a class="nav-link px-2 d-flex align-items-center gap-3 dropdown-toggle {{ request()->is('*media*') || request()->is('*videos*') ? 'active' : '' }}"
                            href="#media-collapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="authentication-collapse">
                            <i class="fas fa-video"></i> <span class="me-auto">Media</span>
                        </a>
                        <div class="ms-5 collapse {{ request()->is('*media*') || request()->is('*videos*') ? 'show' : '' }}"
                            id="media-collapse" data-bs-parent="#mediaMenu">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a class="nav-link {{ request()->is('*media*') ? 'active' : '' }}"
                                        href="{{ route('media.index') }}">Images</a></li>
                                <li class="nav-item"><a
                                        class="nav-link {{ request()->is('*videos*') ? 'active' : '' }}"
                                        href="{{ route('videos.index') }}">Videos</a></li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 d-flex align-items-center gap-3 dropdown-toggle {{ request()->is('*tv*') ? 'active' : '' }}"
                            href="#schedular-collapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="authentication-collapse">
                            <i class="fas fa-desktop"></i> <span class="me-auto">Tv Schedule</span>
                        </a>
                        <div class="ms-5 collapse {{ request()->is('*tv*') ? 'show' : '' }}" id="schedular-collapse"
                            data-bs-parent="#mainMenu">
                            <ul class="navbar-nav">
                                <li class="nav-item"><a
                                        class="nav-link {{ request()->is('*tv/shows*') ? 'active' : '' }}"
                                        href="{{ route('tv.shows.index') }}">Shows</a></li>
                                <li class="nav-item"><a
                                        class="nav-link {{ request()->is('*tv/program_lineup*') ? 'active' : '' }}"
                                        href="{{ route('tv.program_lineup.index') }}">Program Lineup</a></li>
                            </ul>
                        </div>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link px-2 d-flex align-items-center gap-3 {{ request()->is('*media*') ? 'active' : '' }}"
                            href="{{ route('media.index') }}">
                            <i class="fas fa-video"></i> <span>Media</span>
                        </a>
                    </li>
                @endif
            @endif
            @if (config('cms.ecommerce'))
                <li class="nav-label px-2 small mt-3"><small>eCOMMERCE</small></li>
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 dropdown-toggle {{ request()->is('*ecommerce*') ? 'active' : '' }}"
                        href="#ecommerce-collapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="authentication-collapse">
                        <i class="fas fa-cog"></i> <span class="me-auto">eCommerce</span>
                    </a>
                    <div class="ms-5 collapse {{ request()->is('*ecommerce*') ? 'show' : '' }}"
                        id="ecommerce-collapse" data-bs-parent="#mainMenu">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*ecommerce/products*') ? 'active' : '' }}"
                                    href="{{ route('products.index') }}">Products</a></li>
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*ecommerce/orders*') ? 'active' : '' }}"
                                    href="{{ route('ecommerce.orders') }}">Orders</a></li>
                        </ul>
                    </div>
                </li>
            @endif
            @if ($logged_user->group_id == 1)
                <li class="nav-label px-2 small mt-3"><small>ADMIN</small></li>
                <li class="nav-item">
                    <a class="nav-link px-2 d-flex align-items-center gap-3 dropdown-toggle {{ request()->is('*settings*') ? 'active' : '' }}"
                        href="#settings-collapse" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="authentication-collapse">
                        <i class="fas fa-cog"></i> <span class="me-auto">Settings</span>
                    </a>
                    <div class="ms-5 collapse {{ request()->is('*settings*') ? 'show' : '' }}" id="settings-collapse"
                        data-bs-parent="#mainMenu">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*settings/general*') ? 'active' : '' }}"
                                    href="{{ route('settings.general') }}">General</a></li>
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*settings/advertisements*') ? 'active' : '' }}"
                                    href="{{ route('settings.advertisements') }}">Advertisement</a></li>
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*settings/user_groups*') ? 'active' : '' }}"
                                    href="{{ route('settings.user_groups.index') }}">User Groups</a></li>
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*settings/widgets*') ? 'active' : '' }}"
                                    href="{{ route('settings.widgets.index') }}">Widgets</a></li>
                            <li class="nav-item"><a
                                    class="nav-link {{ request()->is('*settings/menus*') ? 'active' : '' }}"
                                    href="{{ route('settings.menus.index') }}">Menus</a></li>
                        </ul>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</div>
<!-- /Sidebar -->

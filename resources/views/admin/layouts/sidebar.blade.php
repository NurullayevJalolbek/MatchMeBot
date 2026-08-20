<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <!-- ========   Logo   ============ -->
                <img src="{{ asset('assets/images/logo-full.png') }}" alt="Logo" class="logo logo-lg" />
                <img src="{{ asset('assets/images/logo-abbr.png') }}" alt="Logo" class="logo logo-sm" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="nxl-navbar">
                <li class="nxl-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nxl-item {{ request()->routeIs('admin.boosts*') ? 'active' : '' }}">
                    <a href="{{ route('admin.boosts.index') }}" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-zap"></i></span>
                        <span class="nxl-mtext">Boost</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

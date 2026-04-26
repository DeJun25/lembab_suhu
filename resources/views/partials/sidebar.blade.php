<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Lembab Suhu</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <!-- Manage User -->
        <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Analytics">Manage Users</div>
            </a>
        </li>
        <!-- Log Sensor -->
        <li class="menu-item {{ request()->routeIs('log_sensor') ? 'active' : '' }}">
            <a href="{{ route('log_sensor') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-radar"></i>
                <div data-i18n="Analytics">Log Sensor</div>
            </a>
        </li>
        <!-- Profile -->
        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">Profile</div>
            </a>
        </li>

        <li class="menu-item mt-auto mb-4 py-1 border-top">
            <a href="{{ route('logout') }}" class="menu-link" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="menu-icon tf-icons bx bx-log-out"></i>
                <div data-i18n="Analytics">Logout</div>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>
    </ul>
</aside>

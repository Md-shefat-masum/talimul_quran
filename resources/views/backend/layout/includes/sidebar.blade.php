<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item sidebar-user-actions">
            <div class="user-details">
                <div class="d-flex align-items-center">
                    <div class="sidebar-profile-img">
                        <img src="{{ asset('assets/backend/images/default-avatar.svg') }}" alt="User avatar">
                    </div>
                    <div class="sidebar-profile-text">
                        <p class="mb-1">{{ auth()->user()?->name ?? 'Admin User' }}</p>
                        <small class="text-muted">Dashboard access</small>
                    </div>
                </div>
            </div>
        </li>

        <li class="nav-item nav-category">Main</li>
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="icon-bg"><i class="mdi mdi-view-dashboard-outline menu-icon"></i></span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <li class="nav-item nav-category">Management</li>
        <li class="nav-item {{ request()->routeIs('backend.users.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('backend.users.index') }}">
                <span class="icon-bg"><i class="mdi mdi-account-group-outline menu-icon"></i></span>
                <span class="menu-title">User Management</span>
            </a>
        </li>
    </ul>
</nav>

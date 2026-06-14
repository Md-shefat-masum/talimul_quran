@php
    $navbarUser = auth()->user();
    $navbarUser?->loadMissing('roles:id,name,slug');
    $navbarProfileImage = $navbarUser?->profileImageUrl() ?: asset('assets/backend/images/default-avatar.svg');
    $navbarPrimaryRole = $navbarUser?->roles?->first()?->name ?? 'No Role';
@endphp

<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/backend/images/admin-logo.svg') }}" alt="Admin Panel">
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/backend/images/admin-logo-mini.svg') }}" alt="Admin Panel">
        </a>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize" aria-label="Toggle sidebar">
            <span class="mdi mdi-menu"></span>
        </button>

        <div class="search-field d-none d-xl-block">
            <div class="d-flex align-items-center h-100">
                <div class="input-group">
                    <div class="input-group-prepend bg-transparent">
                        <i class="input-group-text border-0 mdi mdi-magnify"></i>
                    </div>
                    <input type="text" class="form-control bg-transparent border-0" placeholder="Search dashboard">
                </div>
            </div>
        </div>

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="nav-profile-img">
                        <img src="{{ $navbarProfileImage }}" alt="User avatar">
                    </div>
                    <div class="nav-profile-text">
                        <p class="mb-1 text-black">{{ $navbarUser?->name ?? 'Admin User' }}</p>
                        <small class="text-muted d-block">{{ $navbarPrimaryRole }}</small>
                    </div>
                </a>
                <div class="dropdown-menu navbar-dropdown dropdown-menu-end p-0 border-0 font-size-sm" aria-labelledby="profileDropdown">
                    <div class="p-3 text-center bg-primary">
                        <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ $navbarProfileImage }}" alt="User avatar">
                        <p class="text-white mb-0 mt-2">{{ $navbarUser?->name ?? 'Admin User' }}</p>
                        <small class="text-white-50">{{ $navbarUser?->email }}</small>
                    </div>
                    <div class="p-2">
                        <h5 class="dropdown-header text-uppercase ps-2 text-dark">User Options</h5>
                        <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="{{ route('backend.profile.edit') }}">
                            <span>Profile</span>
                            <i class="mdi mdi-account-outline ms-1"></i>
                        </a>
                        <a class="dropdown-item py-1 d-flex align-items-center justify-content-between" href="{{ route('backend.profile.edit') }}">
                            <span>Settings</span>
                            <i class="mdi mdi-cog-outline ms-1"></i>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-1 d-flex align-items-center justify-content-between">
                                <span>Logout</span>
                                <i class="mdi mdi-logout ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas" aria-label="Toggle mobile sidebar">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>

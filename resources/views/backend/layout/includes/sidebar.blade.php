@php
    use App\Support\Permissions\PermissionRegistry;
    use App\Support\Sidebar\SidebarMenuBuilder;

    $sidebarUser = auth()->user();
    $sidebarUser?->loadMissing('roles:id,name,slug');
    $sidebarMenus = SidebarMenuBuilder::build(PermissionRegistry::modules(), $sidebarUser);
    $primaryRole = $sidebarUser?->roles?->first()?->name ?? 'No Role';
    $profileImage = $sidebarUser?->profileImageUrl() ?: asset('assets/backend/images/default-avatar.svg');
    $displayId = $sidebarUser ? 'ID #A'.str_pad((string) $sidebarUser->id, 4, '0', STR_PAD_LEFT) : 'ID #A0000';
@endphp

<nav class="sidebar sidebar-offcanvas permission-sidebar" id="sidebar" data-sidebar-root>
    <div class="left_sidebar_profile_info">
        <div class="sidebar-profile-card">
            <a href="{{ route('backend.profile.edit') }}" class="sidebar-profile-card__menu" aria-label="Profile settings">
                <i class="mdi mdi-dots-vertical"></i>
            </a>
            <div class="sidebar-profile-card__avatar">
                <img src="{{ $profileImage }}" alt="User avatar">
            </div>
            <h4>{{ $sidebarUser?->name ?? 'Admin User' }}</h4>
            <span>{{ $primaryRole }}</span>
            <p>{{ $sidebarUser?->email ?? $displayId }}</p>
        </div>
    </div>

    <div class="permission_menues">
        <p class="permission_menues__label">Main Menu</p>
        <ul class="sidebar-tree" role="tree">
            @foreach($sidebarMenus as $menu)
                @include('backend.layout.includes.sidebar-item', [
                    'menu' => $menu,
                    'level' => 0,
                ])
            @endforeach
        </ul>
    </div>

    <div class="right_sidebar_bottom_fixed_menus">
        <ul>
            <li>
                <a href="{{ route('backend.profile.edit') }}" @class(['is-active' => request()->routeIs('backend.profile.*')])>
                    <i class="mdi mdi-account-circle-outline"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="{{ route('backend.profile.edit') }}">
                    <i class="mdi mdi-cog-outline"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <i class="mdi mdi-logout"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>

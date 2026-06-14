<?php

namespace App\Support\Sidebar;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SidebarMenuBuilder
{
    private const VERSION_KEY = 'sidebar:version';

    /**
     * @param array<int, array<string, mixed>> $menus
     * @return array<int, array<string, mixed>>
     */
    public static function build(array $menus, ?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        $filteredMenus = Cache::remember(
            self::cacheKey($user),
            now()->addMinutes(10),
            fn (): array => self::filterMenus($menus, $user),
        );

        return self::applyActiveState($filteredMenus);
    }

    public static function bumpVersion(): void
    {
        Cache::forever(self::VERSION_KEY, (string) Str::uuid());
    }

    public static function forgetForUser(User $user): void
    {
        Cache::forget(self::cacheKey($user));
    }

    /**
     * @param array<int, array<string, mixed>> $menus
     * @return array<int, array<string, mixed>>
     */
    private static function filterMenus(array $menus, User $user): array
    {
        return collect($menus)
            ->map(fn (array $menu): ?array => self::filterSingleMenu($menu, $user))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $menu
     * @return array<string, mixed>|null
     */
    private static function filterSingleMenu(array $menu, User $user): ?array
    {
        $children = self::filterMenus($menu['sub_modules'] ?? [], $user);
        $hasChildren = $children !== [];
        $hasPermission = self::canView($menu, $user);
        $url = self::resolveUrl($menu);

        if (! $hasPermission && ! $hasChildren) {
            return null;
        }

        if (! $hasChildren && $url === null) {
            return null;
        }

        return [
            'name' => $menu['name'] ?? 'Untitled',
            'key' => $menu['key'] ?? Str::slug((string) ($menu['name'] ?? 'menu')),
            'icon' => $menu['icon'] ?? 'mdi mdi-circle-outline',
            'permission' => $menu['permission'] ?? null,
            'route_name' => $menu['route_name'] ?? null,
            'route_patterns' => $menu['route_patterns'] ?? [],
            'url' => $url,
            'children' => $children,
            'has_children' => $hasChildren,
        ];
    }

    /**
     * @param array<string, mixed> $menu
     */
    private static function canView(array $menu, User $user): bool
    {
        $permission = $menu['permission'] ?? null;

        if (! is_string($permission) || $permission === '') {
            return true;
        }

        return $user->can($permission);
    }

    /**
     * @param array<string, mixed> $menu
     */
    private static function resolveUrl(array $menu): ?string
    {
        $routeName = $menu['route_name'] ?? null;

        if (! is_string($routeName) || $routeName === '' || ! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $menus
     * @return array<int, array<string, mixed>>
     */
    private static function applyActiveState(array $menus): array
    {
        return collect($menus)
            ->map(fn (array $menu): array => self::applyActiveStateToSingleMenu($menu))
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $menu
     * @return array<string, mixed>
     */
    private static function applyActiveStateToSingleMenu(array $menu): array
    {
        $children = collect($menu['children'] ?? [])
            ->map(fn (array $child): array => self::applyActiveStateToSingleMenu($child))
            ->values()
            ->all();

        $isSelfActive = self::matchesCurrentRoute($menu);
        $hasActiveChild = collect($children)->contains(fn (array $child): bool => ! empty($child['is_active']));

        $menu['children'] = $children;
        $menu['has_children'] = $children !== [];
        $menu['is_exact_active'] = $isSelfActive && $children === [];
        $menu['is_active'] = $isSelfActive || $hasActiveChild;
        $menu['is_open'] = $menu['is_active'];

        return $menu;
    }

    /**
     * @param array<string, mixed> $menu
     */
    private static function matchesCurrentRoute(array $menu): bool
    {
        $routeName = $menu['route_name'] ?? null;

        if (is_string($routeName) && $routeName !== '' && request()->routeIs($routeName)) {
            return true;
        }

        foreach (($menu['route_patterns'] ?? []) as $pattern) {
            if (is_string($pattern) && request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    }

    private static function cacheKey(User $user): string
    {
        $rolePart = self::rolePart($user);
        $version = Cache::get(self::VERSION_KEY, 'v1');

        return "sidebar:filtered:{$version}:user:{$user->id}:roles:{$rolePart}";
    }

    private static function rolePart(User $user): string
    {
        try {
            return $user->roles()
                ->active()
                ->pluck('roles.id')
                ->sort()
                ->implode('_') ?: 'none';
        } catch (QueryException) {
            return 'none';
        }
    }
}

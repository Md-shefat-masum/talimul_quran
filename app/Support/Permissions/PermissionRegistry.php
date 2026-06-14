<?php

namespace App\Support\Permissions;

use Illuminate\Support\Facades\Route;

class PermissionRegistry
{
    /**
     * Fixed manual permission tree. No automatic route scanning/syncing happens here.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function modules(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'key' => 'dashboard',
                'icon' => 'mdi mdi-home-outline',
                'permission' => 'dashboard.view',
                'route_name' => 'dashboard',
                'route_patterns' => ['dashboard'],
                'permissions' => [
                    self::permission('view', 'dashboard.view', 'dashboard'),
                ],
            ],
            [
                'name' => 'User Management',
                'key' => 'user-management',
                'icon' => 'mdi mdi-account-outline',
                'route_patterns' => ['backend.users.*', 'backend.roles.*', 'backend.permissions.*'],
                'sub_modules' => [
                    [
                        'name' => 'Users',
                        'key' => 'users',
                        'icon' => 'mdi mdi-account-outline',
                        'route_patterns' => ['backend.users.*', 'backend.roles.*', 'backend.permissions.*'],
                        'sub_modules' => [
                            [
                                'name' => 'Users',
                                'key' => 'users-directory',
                                'icon' => 'mdi mdi-account-group-outline',
                                'permission' => 'users.view',
                                'route_name' => 'backend.users.index',
                                'route_patterns' => ['backend.users.*'],
                            ],
                            [
                                'name' => 'User Roles',
                                'key' => 'user-roles',
                                'icon' => 'mdi mdi-shield-account-outline',
                                'permission' => 'roles.view',
                                'route_name' => 'backend.roles.index',
                                'route_patterns' => ['backend.roles.*'],
                            ],
                            [
                                'name' => 'App Modules',
                                'key' => 'app-modules',
                                'icon' => 'mdi mdi-key-chain',
                                'permission' => 'permissions.view',
                                'route_name' => 'backend.permissions.index',
                                'route_patterns' => ['backend.permissions.*'],
                            ],
                        ],
                        'permissions' => [
                            self::permission('view', 'users.view', 'backend.users.index'),
                            self::permission('create', 'users.create', 'backend.users.create'),
                            self::permission('update', 'users.update', 'backend.users.edit'),
                            self::permission('delete', 'users.delete', 'backend.users.destroy'),
                            self::permission('export', 'users.export', 'backend.users.export.csv'),
                        ],
                    ],
                    [
                        'name' => 'User Roles Management',
                        'key' => 'user-roles',
                        'icon' => 'mdi mdi-shield-account-outline',
                        'permissions' => [
                            self::permission('view', 'roles.view', 'backend.roles.index'),
                            self::permission('create', 'roles.create', 'backend.roles.create'),
                            self::permission('update', 'roles.update', 'backend.roles.edit'),
                            self::permission('delete', 'roles.delete', 'backend.roles.destroy'),
                        ],
                    ],
                    [
                        'name' => 'User Permissions Management',
                        'key' => 'user-permissions',
                        'icon' => 'mdi mdi-key-chain',
                        'permissions' => [
                            self::permission('view', 'permissions.view', 'backend.permissions.index'),
                            self::permission('create', 'permissions.create', 'backend.permissions.index'),
                            self::permission('update', 'permissions.update', 'backend.permissions.index'),
                            self::permission('delete', 'permissions.delete', 'backend.permissions.index'),
                        ],
                    ],
                ],
            ],
            [
                'name' => 'File Manager',
                'key' => 'file-manager',
                'icon' => 'mdi mdi-folder-multiple-image',
                'permission' => 'file-manager.view',
                'route_name' => 'backend.file-manager.index',
                'route_patterns' => ['backend.file-manager.*'],
                'permissions' => [
                    self::permission('view', 'file-manager.view', 'backend.file-manager.index'),
                    self::permission('create', 'file-manager.create', 'backend.file-manager.folder'),
                    self::permission('update', 'file-manager.update', 'backend.file-manager.rename'),
                    self::permission('delete', 'file-manager.delete', 'backend.file-manager.destroy'),
                ],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_values(array_map(
            fn (array $permission): string => $permission['key'],
            self::permissions(),
        ));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function permissions(): array
    {
        $permissions = [];
        self::collectPermissions(self::modules(), $permissions);

        return $permissions;
    }

    /**
     * @param array<int, string> $keys
     * @return array<int, string>
     */
    public static function normalize(array $keys): array
    {
        $allowed = self::keys();

        return collect($keys)
            ->filter(fn (mixed $key): bool => is_string($key) && in_array($key, $allowed, true))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $modules
     * @param array<int, array<string, mixed>> $permissions
     */
    private static function collectPermissions(array $modules, array &$permissions): void
    {
        foreach ($modules as $module) {
            foreach ($module['permissions'] ?? [] as $permission) {
                $permissions[] = $permission;
            }

            if (! empty($module['sub_modules'])) {
                self::collectPermissions($module['sub_modules'], $permissions);
            }
        }
    }

    /**
     * @return array<string, string|null>
     */
    private static function permission(string $action, string $key, string $routeName): array
    {
        return [
            'action' => $action,
            'key' => $key,
            'route_name' => $routeName,
            'route' => self::routeUrl($routeName),
        ];
    }

    private static function routeUrl(string $routeName): ?string
    {
        if (! Route::has($routeName)) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }
}

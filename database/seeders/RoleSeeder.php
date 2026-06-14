<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->updateOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Admin',
                'permissions' => PermissionRegistry::keys(),
                'is_system' => true,
                'status' => true,
            ],
        );

        Role::query()->updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'permissions' => [
                    'dashboard.view',
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.export',
                    'roles.view',
                    'permissions.view',
                ],
                'is_system' => true,
                'status' => true,
            ],
        );
    }
}

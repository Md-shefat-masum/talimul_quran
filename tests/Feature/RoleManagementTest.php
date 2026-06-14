<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_role_permission_cannot_open_roles_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('backend.roles.index'))
            ->assertForbidden();
    }

    public function test_authorized_user_can_create_role_with_fixed_permissions(): void
    {
        $admin = $this->userWithPermissions(['dashboard.view', 'roles.create', 'roles.view']);

        $response = $this->actingAs($admin)->post(route('backend.roles.store'), [
            'name' => 'Manager',
            'status' => 1,
            'permissions' => [
                'dashboard.view',
                'users.view',
                'not-in-registry',
            ],
        ]);

        $response->assertRedirect(route('backend.roles.index'));

        $role = Role::query()->where('slug', 'manager')->firstOrFail();
        $this->assertSame(['dashboard.view', 'users.view'], $role->permissions);
    }

    public function test_permission_registry_page_lists_fixed_permissions(): void
    {
        $admin = $this->userWithPermissions(['dashboard.view', 'permissions.view']);

        $this->actingAs($admin)
            ->get(route('backend.permissions.index'))
            ->assertOk()
            ->assertSee('users.view')
            ->assertSee('roles.create');
    }

    private function userWithPermissions(array $permissions): User
    {
        $role = Role::query()->create([
            'name' => 'Test Role '.Role::query()->count(),
            'slug' => 'test-role-'.Role::query()->count(),
            'permissions' => PermissionRegistry::normalize($permissions),
            'status' => true,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }
}

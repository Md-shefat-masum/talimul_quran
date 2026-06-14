<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_hides_unauthorized_permission_menu_items(): void
    {
        $user = $this->userWithPermissions(['dashboard.view', 'users.view']);

        $response = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        $sidebar = $this->sidebarHtml($response->getContent());

        $this->assertStringContainsString('User Management', $sidebar);
        $this->assertStringContainsString('Users', $sidebar);
        $this->assertStringNotContainsString('User Roles', $sidebar);
        $this->assertStringNotContainsString('App Modules', $sidebar);
    }

    public function test_sidebar_removes_parent_with_no_visible_children(): void
    {
        $user = $this->userWithPermissions(['dashboard.view']);

        $response = $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();

        $sidebar = $this->sidebarHtml($response->getContent());

        $this->assertStringContainsString('Dashboard', $sidebar);
        $this->assertStringNotContainsString('User Management', $sidebar);
        $this->assertStringNotContainsString('File Manager', $sidebar);
    }

    public function test_sidebar_marks_active_leaf_and_parent_chain_open(): void
    {
        $user = $this->userWithPermissions(['dashboard.view', 'roles.view']);

        $response = $this->actingAs($user)->get(route('backend.roles.index'));

        $response->assertOk()
            ->assertSee('User Management')
            ->assertSee('User Roles')
            ->assertSee('is-open', false)
            ->assertSee('is-active', false)
            ->assertSee('is-exact-active', false);
    }

    public function test_sidebar_supports_deep_recursive_menu_filtering(): void
    {
        $user = $this->userWithPermissions(['users.view']);
        $menus = [
            [
                'name' => 'Level One',
                'key' => 'level-one',
                'icon' => 'mdi mdi-folder-outline',
                'sub_modules' => [
                    [
                        'name' => 'Level Two',
                        'key' => 'level-two',
                        'icon' => 'mdi mdi-folder-outline',
                        'sub_modules' => [
                            [
                                'name' => 'Level Three',
                                'key' => 'level-three',
                                'icon' => 'mdi mdi-folder-outline',
                                'sub_modules' => [
                                    [
                                        'name' => 'Deep Link',
                                        'key' => 'deep-link',
                                        'icon' => 'mdi mdi-link',
                                        'permission' => 'users.view',
                                        'route_name' => 'dashboard',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $tree = \App\Support\Sidebar\SidebarMenuBuilder::build($menus, $user);

        $this->assertSame('Level One', $tree[0]['name']);
        $this->assertSame('Level Two', $tree[0]['children'][0]['name']);
        $this->assertSame('Level Three', $tree[0]['children'][0]['children'][0]['name']);
        $this->assertSame('Deep Link', $tree[0]['children'][0]['children'][0]['children'][0]['name']);
    }

    private function userWithPermissions(array $permissions): User
    {
        $role = Role::query()->create([
            'name' => 'Sidebar Role '.Role::query()->count(),
            'slug' => 'sidebar-role-'.Role::query()->count(),
            'permissions' => $permissions,
            'status' => true,
        ]);

        $user = User::factory()->create();
        $user->roles()->attach($role);

        return $user;
    }

    private function sidebarHtml(string $content): string
    {
        preg_match('/<nav class="sidebar.*?<\/nav>/s', $content, $matches);

        return $matches[0] ?? '';
    }
}

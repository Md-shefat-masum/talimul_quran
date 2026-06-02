<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_user_management_page(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('backend.users.index'));

        $response->assertOk();
        $response->assertSee('User Management');
    }

    public function test_guest_is_redirected_from_user_management_page(): void
    {
        $response = $this->get(route('backend.users.index'));

        $response->assertRedirect();
    }

    public function test_user_can_be_created_with_valid_data(): void
    {
        $admin = User::factory()->create();
        $type = UserType::query()->create([
            'name' => 'Staff',
            'code' => 'staff',
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->postJson(route('backend.users.store'), [
            'name' => 'Example User',
            'email' => 'example@example.com',
            'phone' => '01700000000',
            'user_type_id' => $type->id,
            'status' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('users', ['email' => 'example@example.com']);
    }

    public function test_blank_password_does_not_replace_existing_password_during_update(): void
    {
        $admin = User::factory()->create();
        $type = UserType::query()->create([
            'name' => 'Manager',
            'code' => 'manager',
            'status' => true,
        ]);
        $user = User::factory()->create([
            'password' => 'existing-password',
        ]);
        $oldPassword = $user->password;

        $response = $this->actingAs($admin)->putJson(route('backend.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '01800000000',
            'user_type_id' => $type->id,
            'status' => 1,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertOk();
        $this->assertSame($oldPassword, $user->fresh()->password);
    }

    public function test_logged_in_user_cannot_delete_own_account(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('backend.users.destroy', $admin));

        $response->assertUnprocessable();
        $this->assertNotSoftDeleted($admin);
    }

    public function test_user_type_select2_endpoint_returns_expected_shape(): void
    {
        $admin = User::factory()->create();
        UserType::query()->create([
            'name' => 'Operator',
            'code' => 'operator',
            'status' => true,
        ]);

        $response = $this->actingAs($admin)->getJson(route('backend.users.options.user-types', ['q' => 'Oper']));

        $response->assertOk();
        $response->assertJsonStructure([
            'results' => [['id', 'text']],
            'pagination' => ['more'],
        ]);
    }
}

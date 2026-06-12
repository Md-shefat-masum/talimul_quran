<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\MediaFolder;
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

    public function test_guest_json_requests_are_denied_from_user_management(): void
    {
        $this->getJson(route('backend.users.data'))->assertUnauthorized();
        $this->postJson(route('backend.users.store'), [])->assertUnauthorized();
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

    public function test_user_documents_can_be_saved_and_tracked_from_multiple_picker(): void
    {
        $admin = User::factory()->create();
        $type = UserType::query()->create([
            'name' => 'Teacher',
            'code' => 'teacher',
            'status' => true,
        ]);
        $folder = MediaFolder::query()->create([
            'name' => 'Documents',
            'saved_name_into_storage' => 'documents',
            'parent_id' => 0,
            'status' => 1,
        ]);
        $first = Media::query()->create([
            'disk' => 'ftp',
            'path' => 'users/documents/nid-front.jpg',
            'filename' => 'nid-front.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'media_folder_id' => $folder->id,
            'status' => 1,
        ]);
        $second = Media::query()->create([
            'disk' => 'ftp',
            'path' => 'users/documents/certificate.jpg',
            'filename' => 'certificate.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'media_folder_id' => $folder->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($admin)->postJson(route('backend.users.store'), [
            'name' => 'Document User',
            'email' => 'document-user@example.com',
            'phone' => '01700000001',
            'document_urls' => json_encode([
                'https://posftp.bme.com.bd/users/documents/nid-front.jpg',
                'https://posftp.bme.com.bd/users/documents/certificate.jpg',
            ]),
            'document_paths' => json_encode([
                'users/documents/nid-front.jpg',
                'users/documents/certificate.jpg',
            ]),
            'user_type_id' => $type->id,
            'status' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();
        $user = User::query()->where('email', 'document-user@example.com')->firstOrFail();

        $this->assertSame([
            'users/documents/nid-front.jpg',
            'users/documents/certificate.jpg',
        ], $user->document_paths);
        $this->assertDatabaseHas('media_in_uses', [
            'media_id' => $first->id,
            'model' => User::class,
            'model_id' => $user->id,
            'col_name' => 'document_urls',
        ]);
        $this->assertDatabaseHas('media_in_uses', [
            'media_id' => $second->id,
            'model' => User::class,
            'model_id' => $user->id,
            'col_name' => 'document_urls',
        ]);
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

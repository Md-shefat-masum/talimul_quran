<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\MediaFolder;
use App\Models\Role;
use App\Models\User;
use App\Models\UserType;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get(route('backend.profile.edit'))->assertRedirect();
    }

    public function test_authenticated_user_can_open_dynamic_profile_page(): void
    {
        config(['filesystems.disks.ftp.url' => 'https://media.example.test']);

        $user = $this->profileUser([
            'name' => 'Dynamic Profile User',
            'email' => 'dynamic-profile@example.com',
            'profile_image_path' => 'users/profile-images/current.jpg',
        ]);

        $response = $this->actingAs($user)->get(route('backend.profile.edit'));

        $response->assertOk()
            ->assertSee('Dynamic Profile User')
            ->assertSee('dynamic-profile@example.com')
            ->assertSee('Super Admin')
            ->assertSee('https://media.example.test/users/profile-images/current.jpg');
    }

    public function test_user_can_update_own_profile_details_password_and_image(): void
    {
        $user = $this->profileUser([
            'password' => Hash::make('old-password'),
            'profile_image_path' => 'users/profile-images/old.jpg',
        ]);
        $folder = MediaFolder::query()->create([
            'name' => 'Profile Images',
            'saved_name_into_storage' => 'profile-images',
            'parent_id' => 0,
            'status' => 1,
        ]);
        $oldMedia = Media::query()->create([
            'disk' => 'ftp',
            'path' => 'users/profile-images/old.jpg',
            'filename' => 'old.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'media_folder_id' => $folder->id,
            'status' => 1,
        ]);
        $newMedia = Media::query()->create([
            'disk' => 'ftp',
            'path' => 'users/profile-images/new.jpg',
            'filename' => 'new.jpg',
            'extension' => 'jpg',
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'media_folder_id' => $folder->id,
            'status' => 1,
        ]);
        app(\App\Services\FileManager\FileManagerUsageService::class)->track([
            ['path' => $oldMedia->path],
        ], [
            'module' => 'user-management',
            'owner_type' => User::class,
            'owner_id' => (string) $user->id,
            'field_name' => 'profile_image_path',
        ]);

        $response = $this->actingAs($user)->patch(route('backend.profile.update'), [
            'name' => 'Updated Profile User',
            'email' => 'updated-profile@example.com',
            'phone' => '01900000000',
            'profile_image_url' => 'https://media.example.test/users/profile-images/new.jpg',
            'profile_image_path' => 'users/profile-images/new.jpg',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('backend.profile.edit'));
        $user->refresh();

        $this->assertSame('Updated Profile User', $user->name);
        $this->assertSame('updated-profile@example.com', $user->email);
        $this->assertSame('01900000000', $user->phone);
        $this->assertSame('users/profile-images/new.jpg', $user->profile_image_path);
        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertDatabaseMissing('media_in_uses', [
            'media_id' => $oldMedia->id,
            'model' => User::class,
            'model_id' => $user->id,
            'col_name' => 'profile_image_path',
        ]);
        $this->assertDatabaseHas('media_in_uses', [
            'media_id' => $newMedia->id,
            'model' => User::class,
            'model_id' => $user->id,
            'col_name' => 'profile_image_path',
        ]);
    }

    public function test_blank_profile_password_keeps_existing_password(): void
    {
        $user = $this->profileUser([
            'password' => Hash::make('existing-password'),
        ]);
        $oldPassword = $user->password;

        $response = $this->actingAs($user)->patch(route('backend.profile.update'), [
            'name' => 'Same Password User',
            'email' => $user->email,
            'phone' => '',
            'profile_image_url' => '',
            'profile_image_path' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('backend.profile.edit'));
        $this->assertSame($oldPassword, $user->fresh()->password);
    }

    private function profileUser(array $attributes = []): User
    {
        $type = UserType::query()->create([
            'name' => 'Admin',
            'code' => 'admin',
            'status' => true,
        ]);
        $role = Role::query()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin-'.Role::query()->count(),
            'permissions' => PermissionRegistry::keys(),
            'is_system' => true,
            'status' => true,
        ]);

        $user = User::factory()->create(array_merge([
            'user_type_id' => $type->id,
            'phone' => '01800000000',
        ], $attributes));
        $user->roles()->attach($role);

        return $user;
    }
}

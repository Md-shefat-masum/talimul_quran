<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'reset-user@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'reset-user@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', trans(Password::RESET_LINK_SENT));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_guest_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset-complete@example.com',
            'password' => Hash::make('old-password'),
        ]);
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'reset-complete@example.com',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('status', trans(Password::PASSWORD_RESET));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }
}

<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (User $notifiable, string $token): MailMessage {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject(config('app.name', 'Laravel').' Password Reset')
                ->greeting('Hello '.$notifiable->name.',')
                ->line('We received a request to reset your admin account password.')
                ->action('Reset Password', $resetUrl)
                ->line('This password reset link will expire in '.config('auth.passwords.users.expire').' minutes.')
                ->line('If you did not request a password reset, no further action is required.');
        });

        Gate::before(function (User $user): ?bool {
            return $user->hasRole('super-admin') ? true : null;
        });

        foreach (PermissionRegistry::keys() as $permission) {
            Gate::define($permission, fn (User $user): bool => $user->hasPermission($permission));
        }
    }
}

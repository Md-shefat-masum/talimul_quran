<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class DeleteUserAction
{
    public function execute(User $user, ?int $authenticatedUserId): void
    {
        if ($authenticatedUserId !== null && $authenticatedUserId === $user->id) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own account while you are logged in.'],
            ]);
        }

        $user->delete();
    }
}

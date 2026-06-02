<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;

class CreateUserAction
{
    public function execute(array $data): User
    {
        return User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'user_type_id' => $data['user_type_id'],
            'status' => $data['status'],
            'password' => $data['password'],
        ]);
    }
}

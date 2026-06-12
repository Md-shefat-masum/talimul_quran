<?php

namespace App\Http\Controllers\User\Actions;

use App\Models\User;

class FindUserAction
{
    public function execute(User $user): array
    {
        $user->loadMissing('userType:id,name');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar_url' => $user->avatar_url,
            'avatar_path' => $user->avatar_path,
            'document_urls' => $user->document_urls ?: [],
            'document_paths' => $user->document_paths ?: [],
            'user_type_id' => $user->user_type_id,
            'user_type_text' => $user->userType?->name,
            'status' => $user->status ? 1 : 0,
        ];
    }
}

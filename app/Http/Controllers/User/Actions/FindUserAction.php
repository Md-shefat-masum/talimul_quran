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
            'profile_image_url' => $user->profileImageUrl(),
            'profile_image_path' => $user->profile_image_path,
            'additional_image_urls' => $user->additionalImageUrls(),
            'additional_image_paths' => $user->additional_image_paths ?: [],
            'document_urls' => $user->document_urls ?: [],
            'document_paths' => $user->document_paths ?: [],
            'user_type_id' => $user->user_type_id,
            'user_type_text' => $user->userType?->name,
            'status' => $user->status ? 1 : 0,
        ];
    }
}

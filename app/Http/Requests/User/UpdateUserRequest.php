<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:190',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar_url' => ['nullable', 'url', 'max:1000'],
            'avatar_path' => ['nullable', 'string', 'max:500'],
            'profile_image_url' => ['nullable', 'url', 'max:1000'],
            'profile_image_path' => ['nullable', 'string', 'max:500'],
            'additional_image_urls' => ['nullable', 'json'],
            'additional_image_paths' => ['nullable', 'json'],
            'document_urls' => ['nullable', 'json'],
            'document_paths' => ['nullable', 'json'],
            'user_type_id' => [
                'required',
                'integer',
                Rule::exists('user_types', 'id')->where('status', true),
            ],
            'status' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}

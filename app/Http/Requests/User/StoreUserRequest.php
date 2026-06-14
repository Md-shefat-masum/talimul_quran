<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
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
            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where('status', true),
            ],
            'status' => ['required', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}

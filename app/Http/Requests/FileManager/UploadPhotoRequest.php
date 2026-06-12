<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'photo' => ['required', 'file', 'image', 'max:10240'],
            'path' => ['nullable', 'string', 'max:500'],
            'folder_id' => ['nullable', 'integer', 'min:1'],
            'name' => ['nullable', 'string', 'max:120'],
            'preset' => ['nullable', 'string', 'max:50'],
            'width' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'height' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'conflict_strategy' => ['nullable', 'in:rename,replace,error'],
        ];
    }
}

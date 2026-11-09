<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'desc'       => 'sometimes|string',
            'img'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'video'      => 'nullable|file|mimetypes:video/mp4,video/x-matroska|max:51200', // (50MB max)
            'is_edited'  => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_edited' => 1,
        ]);
    }
}

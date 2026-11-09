<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'section_id' => 'required|exists:sections,id',
            'service_id' => 'required|exists:services,id',
            'user_id'    => 'required|exists:users,id',
            'desc'       => 'required|string',
            'city'       => 'required|string',
            'img'        => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'video'      => 'nullable|file|mimetypes:video/mp4,video/x-matroska|max:51200', // (50MB max)
        ];
    }
}

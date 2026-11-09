<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeDataRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'company_image' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'company_name' => 'nullable|string',
            'instagram_link' => 'nullable|string',
            'linked_in_link' => 'nullable|string',
            'description' => 'nullable|string',
            'phone_number_2' => 'nullable|string',
            'mobile_number_1' => 'nullable|string',
            'mobile_number_2' => 'nullable|string',
            'fax_number' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
            'facebook_link' => 'nullable|string',
            'tiktok_link' => 'nullable|string',
            'website' => 'nullable|string',
        ];
    }
}

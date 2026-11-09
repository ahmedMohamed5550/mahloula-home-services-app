<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ForgotPasswordRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::exists('users', 'email')->where(function ($query) {
                    $query->whereNotNull('email');
                }),
            ],
        ];
    }

    /**
     * Custom message for validation errors.
     */
    public function messages(): array
    {
        return [
            'email.exists' => 'The provided email address does not exist in our records.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation error',
                'data' => $validator->errors(),
                'status' => 422,
            ], 422)
        );
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // open to all guests
    }

    public function rules(): array
    {
        return [
            'access_code' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'access_code.required' => 'الرجاء إدخال كود الدخول.',
            'access_code.max'      => 'الكود غير صحيح، تواصل مع الإدارة.',
        ];
    }
}

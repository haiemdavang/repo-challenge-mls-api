<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'tên đăng nhập',
            'password' => 'mật khẩu',
        ];
    }


    public function messages(): array
    {
        return [
            'username.required' => 'USERNAME_REQUIRED',
            'password.required' => 'PASSWORD_REQUIRED',
        ];
    }
}

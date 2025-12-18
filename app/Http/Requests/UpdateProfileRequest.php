<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'email' => ['email', Rule::unique('users')->ignore($userId)],
            'firstname' => 'string|max:255',
            'lastname' => 'string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'id_number' => ['nullable', 'string', Rule::unique('users')->ignore($userId)],
            'department' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'EMAIL_INVALID',
            'email.unique' => 'EMAIL_ALREADY_EXISTS',
            'gender.in' => 'GENDER_INVALID',
            'birthday.date' => 'BIRTHDAY_INVALID',
            'id_number.unique' => 'ID_NUMBER_ALREADY_EXISTS',
        ];
    }
}

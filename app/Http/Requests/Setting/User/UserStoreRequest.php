<?php

namespace App\Http\Requests\Setting\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'formData' => ['required'],
            'formData.name' => ['required', 'string'],
            'formData.email' => ['required', 'email', 'unique:users,email'],
            'formData.phone' => ['required', 'regex:/(01)[0-9]{9}$/', 'unique:user_profiles,user_phone'],

            'formData.role' => ['required', Rule::exists("roles", "id")],
        ];
    }
}

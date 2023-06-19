<?php

namespace App\Http\Requests\Setting\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'id' => ['required', 'integer'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->id)],
            'user_status' => ['required', 'string'],

            'profile' => ['required'],
            'profile.id' => ['required', Rule::exists("user_profiles", "id")],
            'profile.user_phone' => ['required', 'regex:/(01)[0-9]{9}$/'],

            'roles.*.id' => ['required', Rule::exists("roles", "id")],
            'roles.*.name' => ['required'],
        ];
    }
}


//'unique:,,' . $this->profile->user_phone]

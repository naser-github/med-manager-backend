<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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

            'formData.id' => ['required', 'numeric'],

            'formData.medicine' => ['required'],
            'formData.medicine.name' => ['required', 'string'],

            'formData.dose' => ['required', 'array'],
            'formData.dose.*.id' => ['required', 'numeric', Rule::exists('dosages', 'id')],
            'formData.dose.*.label' => ['required', 'string'],
            'formData.dose.*.time' => ['required'],
            'formData.dose.*.status' => ['required', 'string'],

            'formData.status' => ['required', 'string'],
            'formData.time_period' => ['required', 'numeric', 'min:1'],
        ];
    }
}

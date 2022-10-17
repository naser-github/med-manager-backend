<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionAddRequest extends FormRequest
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
            'formData' => ['required', 'array'],

            'formData.*.medicineName' => ['required', 'string'],
            'formData.*.timePeriod' => ['required', 'numeric'],
            'formData.*.doseFrequency' => ['required', 'numeric'],
            'formData.*.doseDetails' => ['required', 'array'],

            'formData.*.doseDetails.*.label' => ['required', 'string'],
            'formData.*.doseDetails.*.time' => ['required', 'min:1'],
        ];
    }
}

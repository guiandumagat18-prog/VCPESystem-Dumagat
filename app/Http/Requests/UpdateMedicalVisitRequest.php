<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pet_id'          => ['sometimes', 'exists:pets,id'],
            'veterinarian_id' => ['sometimes', 'exists:veterinarians,id'],
            'examination_date'=> ['sometimes', 'date'],
            'diagnosis'       => ['sometimes', 'string', 'min:5'],
            'procedure_cost'  => ['sometimes', 'numeric', 'min:0'],
            'payment_status'  => ['sometimes', 'in:pending,paid,cancelled'],
            'treatments_submitted' => ['sometimes', 'boolean'],
            'treatments' => ['sometimes', 'array'],
            'treatments.*.treatment_given' => ['required', 'string', 'max:255'],
            'treatments.*.cost' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'diagnosis.min' => 'Diagnosis notes must be at least 5 characters.',
        ];
    }
}
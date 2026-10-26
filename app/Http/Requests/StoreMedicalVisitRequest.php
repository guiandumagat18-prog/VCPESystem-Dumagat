<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pet_id'          => ['required', 'exists:pets,id'],
            'veterinarian_id' => ['required', 'exists:veterinarians,id'],
            'examination_date'=> ['required', 'date'],
            'diagnosis'       => ['required', 'string', 'min:5'],
            'procedure_cost'  => ['required', 'numeric', 'min:0'],
            'payment_status'  => ['required', 'in:pending,paid,cancelled'],
            'treatments'                    => ['sometimes', 'array'],
            'treatments.*.treatment_given'  => ['required_with:treatments', 'string', 'max:255'],
            'treatments.*.cost'             => ['required_with:treatments', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'diagnosis.required' => 'A visit cannot be saved without diagnosis notes.',
            'diagnosis.min'      => 'Diagnosis notes must be at least 5 characters.',
            'pet_id.exists'      => 'The selected pet does not exist.',
        ];
    }
}
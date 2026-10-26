<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['sometimes', 'exists:pet_owners,id'],
            'name'     => ['sometimes', 'string', 'max:100'],
            'species'  => ['sometimes', 'string', 'max:50'],
            'breed'    => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_id.exists' => 'The selected owner does not exist.',
        ];
    }
}
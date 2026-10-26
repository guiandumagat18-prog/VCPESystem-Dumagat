<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['required', 'exists:pet_owners,id'],
            'name'     => ['required', 'string', 'max:100'],
            'species'  => ['required', 'string', 'max:50'],
            'breed'    => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_id.required' => 'Please select an owner.',
            'owner_id.exists'   => 'The selected owner does not exist.',
            'species.required'  => 'Please specify the pet species.',
        ];
    }
}
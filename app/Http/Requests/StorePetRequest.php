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
            'owner_name' => ['required', 'string', 'max:100'],
            'name'     => ['required', 'string', 'max:100'],
            'species'  => ['required', 'string', 'max:50'],
            'breed'    => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_name.required' => 'Please enter the owner name.',
            'species.required'  => 'Please specify the pet species.',
        ];
    }
}
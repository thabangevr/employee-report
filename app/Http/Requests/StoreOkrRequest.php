<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOkrRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'objective_description' => ['nullable', 'string', 'max:1000'],
            'measure_of_success' => ['nullable', 'string', 'max:500'],
            'weight' => ['required', 'integer', 'min:0', 'max:100'],
            'key_results' => ['nullable', 'array'],
            'key_results.*.type' => ['required_with:key_results', 'in:lag_measure,lead_measure'],
            'key_results.*.description' => ['required_with:key_results', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'key_results.*.type.required_with' => 'Each key result must have a type.',
            'key_results.*.type.in' => 'Key result type must be either lag measure or lead measure.',
            'key_results.*.description.required_with' => 'Each key result must have a description.',
        ];
    }
}

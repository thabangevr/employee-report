<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'raw_content' => ['required', 'string', 'min:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'raw_content.required' => 'Please paste your weekly update text.',
            'raw_content.min' => 'Please provide at least 20 characters of content for analysis.',
        ];
    }
}

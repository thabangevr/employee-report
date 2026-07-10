<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\AreaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWeeklySubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'week_start_date' => ['required', 'date'],
            'one_number_value' => ['nullable', 'string', 'max:255'],
            'one_number_label' => ['nullable', 'string', 'max:255'],
            'okr_focus_ids' => ['nullable', 'array'],
            'okr_focus_ids.*' => ['integer', 'exists:okrs,id'],

            'areas' => ['required', 'array', 'min:1'],
            'areas.*.name' => ['required', 'string', 'max:255'],
            'areas.*.manager_area_id' => ['nullable', 'integer', 'exists:manager_areas,id'],
            'areas.*.status' => ['nullable', Rule::in(array_column(AreaStatus::cases(), 'value'))],
            'areas.*.status_justification' => ['nullable', 'string'],

            'areas.*.outcomes' => ['nullable', 'array'],
            'areas.*.outcomes.*.description' => ['required', 'string'],
            'areas.*.outcomes.*.okr_id' => ['nullable', 'integer', 'exists:okrs,id'],

            'areas.*.priorities' => ['nullable', 'array'],
            'areas.*.priorities.*.description' => ['required', 'string'],
            'areas.*.priorities.*.okr_id' => ['nullable', 'integer', 'exists:okrs,id'],

            'flags' => ['nullable', 'array'],
            'flags.*.risk' => ['required', 'string'],
            'flags.*.cause' => ['required', 'string'],
            'flags.*.consequence' => ['required', 'string'],

            'cross_team_actions' => ['nullable', 'array'],
            'cross_team_actions.*.owner_name' => ['required', 'string', 'max:255'],
            'cross_team_actions.*.ask' => ['required', 'string'],
        ];
    }
}

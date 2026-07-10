<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\DTOs\WeeklySubmission\CreateWeeklySubmissionData;
use App\DTOs\WeeklySubmission\UpdateWeeklySubmissionData;
use App\Models\WeeklySubmission;

class SyncSubmissionRelations
{
    public function execute(WeeklySubmission $submission, CreateWeeklySubmissionData|UpdateWeeklySubmissionData $data): void
    {
        $submission->okrFocus()->sync($data->okrFocusIds);

        foreach ($data->areas as $sortOrder => $area) {
            $submissionArea = $submission->areas()->create([
                'manager_area_id' => $area['manager_area_id'] ?? null,
                'name' => $area['name'],
                'status' => $area['status'] ?? null,
                'status_justification' => $area['status_justification'] ?? null,
                'sort_order' => $sortOrder,
            ]);

            foreach (($area['outcomes'] ?? []) as $outIdx => $outcome) {
                $submissionArea->outcomes()->create([
                    'description' => $outcome['description'],
                    'okr_id' => $outcome['okr_id'] ?? null,
                    'sort_order' => $outIdx,
                ]);
            }

            foreach (($area['priorities'] ?? []) as $priIdx => $priority) {
                $submissionArea->priorities()->create([
                    'description' => $priority['description'],
                    'okr_id' => $priority['okr_id'] ?? null,
                    'sort_order' => $priIdx,
                ]);
            }
        }

        foreach (($data->flags ?? []) as $flagIdx => $flag) {
            $submission->flags()->create([
                'risk' => $flag['risk'],
                'cause' => $flag['cause'],
                'consequence' => $flag['consequence'],
                'sort_order' => $flagIdx,
            ]);
        }

        foreach (($data->crossTeamActions ?? []) as $ctaIdx => $cta) {
            $submission->crossTeamActions()->create([
                'owner_name' => $cta['owner_name'],
                'ask' => $cta['ask'],
                'sort_order' => $ctaIdx,
            ]);
        }
    }
}

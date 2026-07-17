<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;

class GetPreviousSubmissionData
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $repository,
    ) {}

    public function execute(int $userId): ?array
    {
        $submissions = $this->repository->findAllByUser($userId);
        $latest = $submissions->first();

        if (!$latest) {
            return null;
        }

        $latest->load([
            'okrFocus',
            'areas.outcomes',
            'areas.priorities',
            'flags',
            'crossTeamActions',
        ]);

        return [
            'one_number_value' => $latest->one_number_value ?? '',
            'one_number_label' => $latest->one_number_label ?? '',
            'okr_focus_ids' => $latest->okrFocus->pluck('id')->toArray(),
            'areas' => $latest->areas->map(fn($area) => [
                'name' => $area->name,
                'manager_area_id' => $area->manager_area_id,
                'status' => null,
                'status_justification' => '',
                'outcomes' => [['description' => '', 'okr_id' => '']],
                'priorities' => [['description' => '', 'okr_id' => '']],
            ])->toArray(),
            'flags' => [],
            'cross_team_actions' => $latest->crossTeamActions->map(fn($c) => [
                'owner_name' => $c->owner_name,
                'ask' => '',
            ])->toArray(),
        ];
    }
}

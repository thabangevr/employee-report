<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;

class GetLatestSubmissionForDashboard
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
    ) {}

    public function execute(int $userId): ?WeeklySubmission
    {
        $submissions = $this->submissionRepository->findAllByUser($userId);
        $latest = $submissions->first();

        if (!$latest) {
            return null;
        }

        $latest->load([
            'okrFocus',
            'areas.outcomes.okr',
            'areas.priorities.okr',
            'flags',
            'crossTeamActions',
        ]);

        return $latest;
    }
}

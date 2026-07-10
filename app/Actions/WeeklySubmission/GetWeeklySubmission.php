<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;

class GetWeeklySubmission
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
    ) {}

    public function execute(int $submissionId): ?WeeklySubmission
    {
        return $this->submissionRepository->findWithRelations($submissionId);
    }
}

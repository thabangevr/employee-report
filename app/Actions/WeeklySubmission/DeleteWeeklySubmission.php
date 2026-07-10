<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;

class DeleteWeeklySubmission
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
    ) {}

    public function execute(int $submissionId): bool
    {
        return $this->submissionRepository->delete($submissionId);
    }
}

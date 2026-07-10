<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\Enums\SubmissionStatus;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;

class SubmitWeeklySubmission
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
    ) {}

    public function execute(int $submissionId): WeeklySubmission
    {
        /** @var WeeklySubmission $submission */
        $submission = $this->submissionRepository->findOrFail($submissionId);

        $submission->update([
            'status' => SubmissionStatus::Submitted->value,
            'submitted_at' => now(),
        ]);

        return $submission->refresh();
    }
}

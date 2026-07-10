<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\DTOs\WeeklySubmission\CreateWeeklySubmissionData;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateWeeklySubmission
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
        private readonly SyncSubmissionRelations $syncRelations,
        private readonly CalculateWordCount $calculateWordCount,
    ) {}

    public function execute(CreateWeeklySubmissionData $data): WeeklySubmission
    {
        return DB::transaction(function () use ($data): WeeklySubmission {
            /** @var WeeklySubmission $submission */
            $submission = $this->submissionRepository->create([
                'user_id' => $data->userId,
                'week_start_date' => $data->weekStartDate,
                'one_number_value' => $data->oneNumberValue,
                'one_number_label' => $data->oneNumberLabel,
                'status' => 'draft',
            ]);

            $this->syncRelations->execute($submission, $data);

            $wordCount = $this->calculateWordCount->execute($submission->refresh());
            $submission->update(['word_count' => $wordCount]);

            return $submission->refresh();
        });
    }
}

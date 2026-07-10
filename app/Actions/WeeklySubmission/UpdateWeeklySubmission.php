<?php

declare(strict_types=1);

namespace App\Actions\WeeklySubmission;

use App\DTOs\WeeklySubmission\UpdateWeeklySubmissionData;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UpdateWeeklySubmission
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
        private readonly SyncSubmissionRelations $syncRelations,
        private readonly CalculateWordCount $calculateWordCount,
    ) {}

    public function execute(UpdateWeeklySubmissionData $data): WeeklySubmission
    {
        return DB::transaction(function () use ($data): WeeklySubmission {
            /** @var WeeklySubmission $submission */
            $submission = $this->submissionRepository->findOrFail($data->submissionId);

            $submission->update([
                'one_number_value' => $data->oneNumberValue,
                'one_number_label' => $data->oneNumberLabel,
            ]);

            $submission->areas()->delete();
            $submission->flags()->delete();
            $submission->crossTeamActions()->delete();

            $this->syncRelations->execute($submission, $data);

            $wordCount = $this->calculateWordCount->execute($submission->refresh());
            $submission->update(['word_count' => $wordCount]);

            return $submission->refresh();
        });
    }
}

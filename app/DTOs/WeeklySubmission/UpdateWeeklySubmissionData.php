<?php

declare(strict_types=1);

namespace App\DTOs\WeeklySubmission;

class UpdateWeeklySubmissionData
{
    public function __construct(
        public readonly int $submissionId,
        public readonly ?string $oneNumberValue,
        public readonly ?string $oneNumberLabel,
        public readonly array $okrFocusIds,
        public readonly array $areas,
        public readonly array $flags,
        public readonly array $crossTeamActions,
    ) {}
}

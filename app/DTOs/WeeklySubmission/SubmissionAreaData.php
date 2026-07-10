<?php

declare(strict_types=1);

namespace App\DTOs\WeeklySubmission;

class SubmissionAreaData
{
    public function __construct(
        public readonly ?int $managerAreaId,
        public readonly string $name,
        public readonly ?string $status,
        public readonly ?string $statusJustification,
        public readonly int $sortOrder,
        public readonly array $outcomes,
        public readonly array $priorities,
    ) {}
}

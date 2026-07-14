<?php

declare(strict_types=1);

namespace App\DTOs\Okr;

use App\Http\Requests\UpdateOkrRequest;

class UpdateOkrData
{
    public function __construct(
        public readonly int $okrId,
        public readonly string $title,
        public readonly ?string $objectiveDescription,
        public readonly ?string $measureOfSuccess,
        public readonly int $weight,
        public readonly bool $isActive,
        public readonly array $keyResults,
    ) {}

    public static function fromRequest(UpdateOkrRequest $request, int $okrId): self
    {
        return new self(
            okrId: $okrId,
            title: $request->validated('title'),
            objectiveDescription: $request->validated('objective_description'),
            measureOfSuccess: $request->validated('measure_of_success'),
            weight: (int) $request->validated('weight'),
            isActive: (bool) $request->validated('is_active'),
            keyResults: $request->validated('key_results', []),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'objective_description' => $this->objectiveDescription,
            'measure_of_success' => $this->measureOfSuccess,
            'weight' => $this->weight,
            'is_active' => $this->isActive,
        ];
    }
}

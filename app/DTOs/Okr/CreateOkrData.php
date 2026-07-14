<?php

declare(strict_types=1);

namespace App\DTOs\Okr;

use App\Http\Requests\StoreOkrRequest;

class CreateOkrData
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly ?string $objectiveDescription,
        public readonly ?string $measureOfSuccess,
        public readonly int $weight,
        public readonly array $keyResults,
    ) {}

    public static function fromRequest(StoreOkrRequest $request): self
    {
        return new self(
            userId: $request->user()->id,
            title: $request->validated('title'),
            objectiveDescription: $request->validated('objective_description'),
            measureOfSuccess: $request->validated('measure_of_success'),
            weight: (int) $request->validated('weight'),
            keyResults: $request->validated('key_results', []),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'title' => $this->title,
            'objective_description' => $this->objectiveDescription,
            'measure_of_success' => $this->measureOfSuccess,
            'weight' => $this->weight,
            'is_active' => true,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\DTOs\Okr\CreateOkrData;
use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;

class CreateOkr
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(CreateOkrData $data): Okr
    {
        /** @var Okr */
        $okr = $this->okrRepository->create($data->toArray());

        foreach ($data->keyResults as $index => $keyResult) {
            $okr->keyResults()->create([
                'type' => $keyResult['type'],
                'description' => $keyResult['description'],
                'sort_order' => $index,
            ]);
        }

        return $okr->load('keyResults');
    }
}

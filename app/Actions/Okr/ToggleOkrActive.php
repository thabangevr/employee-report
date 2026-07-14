<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;

class ToggleOkrActive
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(int $okrId): Okr
    {
        $okr = $this->okrRepository->findOrFail($okrId);

        /** @var Okr */
        return $this->okrRepository->update($okrId, [
            'is_active' => !$okr->is_active,
        ]);
    }
}

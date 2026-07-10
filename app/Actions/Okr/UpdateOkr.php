<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;

class UpdateOkr
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(int $okrId, string $title, bool $isActive): Okr
    {
        /** @var Okr */
        return $this->okrRepository->update($okrId, [
            'title' => $title,
            'is_active' => $isActive,
        ]);
    }
}

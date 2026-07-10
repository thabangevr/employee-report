<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\Repositories\Contracts\OkrRepositoryInterface;

class DeleteOkr
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(int $okrId): bool
    {
        return $this->okrRepository->delete($okrId);
    }
}

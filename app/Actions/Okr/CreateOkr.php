<?php

declare(strict_types=1);

namespace App\Actions\Okr;

use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;

class CreateOkr
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function execute(int $userId, string $title): Okr
    {
        /** @var Okr */
        return $this->okrRepository->create([
            'user_id' => $userId,
            'title' => $title,
            'is_active' => true,
        ]);
    }
}

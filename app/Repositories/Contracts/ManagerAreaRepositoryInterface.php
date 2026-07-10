<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ManagerAreaRepositoryInterface extends BaseRepositoryInterface
{
    public function findActiveByUser(int $userId): Collection;
}

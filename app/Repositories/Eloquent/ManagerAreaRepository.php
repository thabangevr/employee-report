<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\ManagerArea;
use App\Repositories\Contracts\ManagerAreaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ManagerAreaRepository extends BaseRepository implements ManagerAreaRepositoryInterface
{
    public function __construct(ManagerArea $model)
    {
        parent::__construct($model);
    }

    public function findActiveByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}

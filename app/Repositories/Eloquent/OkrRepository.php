<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OkrRepository extends BaseRepository implements OkrRepositoryInterface
{
    public function __construct(Okr $model)
    {
        parent::__construct($model);
    }

    public function findActiveByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->get();
    }
}

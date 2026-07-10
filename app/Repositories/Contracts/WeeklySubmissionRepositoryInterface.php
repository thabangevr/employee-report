<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\WeeklySubmission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface WeeklySubmissionRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUserAndWeek(int $userId, Carbon $weekStartDate): ?WeeklySubmission;

    public function findAllByUser(int $userId): Collection;

    public function findWithRelations(int $id): ?WeeklySubmission;
}

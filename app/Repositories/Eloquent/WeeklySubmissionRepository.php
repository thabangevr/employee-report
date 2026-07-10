<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class WeeklySubmissionRepository extends BaseRepository implements WeeklySubmissionRepositoryInterface
{
    public function __construct(WeeklySubmission $model)
    {
        parent::__construct($model);
    }

    public function findByUserAndWeek(int $userId, Carbon $weekStartDate): ?WeeklySubmission
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('week_start_date', $weekStartDate->toDateString())
            ->first();
    }

    public function findAllByUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderByDesc('week_start_date')
            ->get();
    }

    public function findWithRelations(int $id): ?WeeklySubmission
    {
        return $this->model
            ->with([
                'okrFocus',
                'areas.outcomes.okr',
                'areas.priorities.okr',
                'flags',
                'crossTeamActions',
            ])
            ->find($id);
    }
}

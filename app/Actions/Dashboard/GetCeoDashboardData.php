<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Enums\AreaStatus;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetCeoDashboardData
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function execute(): array
    {
        $managers = $this->userRepository
            ->findAllBy('role', UserRole::Manager->value);

        $currentWeekStart = Carbon::now()->startOfWeek(Carbon::MONDAY);

        $managerSummaries = $managers->map(function (User $manager) use ($currentWeekStart) {
            $latestSubmission = WeeklySubmission::where('user_id', $manager->id)
                ->with([
                    'user',
                    'okrFocus',
                    'areas.outcomes.okr',
                    'areas.priorities.okr',
                    'flags.weeklySubmission.user',
                    'crossTeamActions',
                    'comments.user',
                ])
                ->orderByDesc('week_start_date')
                ->first();

            $hasCurrentWeek = $latestSubmission
                && $latestSubmission->week_start_date->equalTo($currentWeekStart);

            return [
                'manager' => $manager,
                'submission' => $latestSubmission,
                'has_current_week' => $hasCurrentWeek,
                'is_submitted' => $latestSubmission?->isSubmitted() ?? false,
            ];
        });

        $allSubmissions = $managerSummaries
            ->pluck('submission')
            ->filter();

        $allAreas = $allSubmissions->flatMap(fn ($s) => $s->areas);
        $allFlags = $allSubmissions->flatMap(fn ($s) => $s->flags);

        $greenCount = $allAreas->where('status', AreaStatus::Green)->count();
        $amberCount = $allAreas->where('status', AreaStatus::Amber)->count();
        $blockerCount = $allAreas->where('status', AreaStatus::Blocker)->count();
        $submittedThisWeek = $managerSummaries->where('has_current_week', true)->where('is_submitted', true)->count();

        return [
            'managerSummaries' => $managerSummaries,
            'metrics' => [
                'total_managers' => $managers->count(),
                'submitted_this_week' => $submittedThisWeek,
                'green_areas' => $greenCount,
                'amber_areas' => $amberCount,
                'blockers' => $blockerCount,
                'total_flags' => $allFlags->count(),
            ],
            'allFlags' => $allFlags,
        ];
    }
}

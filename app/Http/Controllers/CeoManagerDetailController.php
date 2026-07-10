<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CeoManagerDetailController extends Controller
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
    ) {}

    public function show(Request $request, User $manager): View
    {
        $submissions = $this->submissionRepository->findAllByUser($manager->id);

        $latestSubmission = $submissions->first();
        if ($latestSubmission) {
            $latestSubmission->load([
                'okrFocus',
                'areas.outcomes.okr',
                'areas.priorities.okr',
                'flags',
                'crossTeamActions',
                'comments.user',
            ]);
        }

        return view('dashboards.ceo-manager-detail', [
            'manager' => $manager,
            'submission' => $latestSubmission,
            'submissions' => $submissions,
        ]);
    }

    public function submission(Request $request, WeeklySubmission $weeklySubmission): View
    {
        $weeklySubmission->load([
            'user',
            'okrFocus',
            'areas.outcomes.okr',
            'areas.priorities.okr',
            'flags',
            'crossTeamActions',
            'comments.user',
        ]);

        $manager = $weeklySubmission->user;

        return view('dashboards.ceo-manager-detail', [
            'manager' => $manager,
            'submission' => $weeklySubmission,
            'submissions' => $this->submissionRepository->findAllByUser($manager->id),
        ]);
    }
}

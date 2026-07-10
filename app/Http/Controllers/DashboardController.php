<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetCeoDashboardData;
use App\Actions\WeeklySubmission\GetLatestSubmissionForDashboard;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly GetLatestSubmissionForDashboard $getLatestSubmission,
        private readonly GetCeoDashboardData $getCeoDashboardData,
    ) {}

    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return match ($user->role) {
            UserRole::CEO => $this->ceoDashboard($user),
            UserRole::Manager => $this->managerDashboard($user),
            UserRole::Employee => view('dashboards.employee', compact('user')),
        };
    }

    private function ceoDashboard($user): View
    {
        $data = $this->getCeoDashboardData->execute();

        return view('dashboards.ceo', [
            'user' => $user,
            'managerSummaries' => $data['managerSummaries'],
            'metrics' => $data['metrics'],
            'allFlags' => $data['allFlags'],
        ]);
    }

    private function managerDashboard($user): View
    {
        $latestSubmission = $this->getLatestSubmission->execute($user->id);

        return view('dashboards.manager', compact('user', 'latestSubmission'));
    }
}

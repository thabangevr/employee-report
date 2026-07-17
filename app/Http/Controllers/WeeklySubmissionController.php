<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\WeeklySubmission\AnalyzeSubmissionContent;
use App\Actions\WeeklySubmission\CreateWeeklySubmission;
use App\Actions\WeeklySubmission\DeleteWeeklySubmission;
use App\Actions\WeeklySubmission\GetPreviousSubmissionData;
use App\Actions\WeeklySubmission\GetWeeklySubmission;
use App\Actions\WeeklySubmission\SubmitWeeklySubmission;
use App\Actions\WeeklySubmission\UpdateWeeklySubmission;
use App\DTOs\WeeklySubmission\CreateWeeklySubmissionData;
use App\DTOs\WeeklySubmission\UpdateWeeklySubmissionData;
use App\Http\Requests\AnalyzeSubmissionRequest;
use App\Http\Requests\StoreWeeklySubmissionRequest;
use App\Http\Requests\UpdateWeeklySubmissionRequest;
use App\Models\WeeklySubmission;
use App\Repositories\Contracts\ManagerAreaRepositoryInterface;
use App\Repositories\Contracts\OkrRepositoryInterface;
use App\Repositories\Contracts\WeeklySubmissionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WeeklySubmissionController extends Controller
{
    public function __construct(
        private readonly WeeklySubmissionRepositoryInterface $submissionRepository,
        private readonly OkrRepositoryInterface $okrRepository,
        private readonly ManagerAreaRepositoryInterface $managerAreaRepository,
    ) {}

    public function index(Request $request): View
    {
        $submissions = $this->submissionRepository->findAllByUser($request->user()->id);

        return view('weekly-submissions.index', compact('submissions'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $okrs = $this->okrRepository->findActiveByUser($user->id);
        $managerAreas = $this->managerAreaRepository->findActiveByUser($user->id);
        $weekStartDate = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return view('weekly-submissions.create', compact('okrs', 'managerAreas', 'weekStartDate'));
    }

    public function store(StoreWeeklySubmissionRequest $request, CreateWeeklySubmission $action): RedirectResponse
    {
        $validated = $request->validated();

        $data = new CreateWeeklySubmissionData(
            userId: $request->user()->id,
            weekStartDate: Carbon::parse($validated['week_start_date'])->startOfWeek(Carbon::MONDAY),
            oneNumberValue: $validated['one_number_value'] ?? null,
            oneNumberLabel: $validated['one_number_label'] ?? null,
            okrFocusIds: $validated['okr_focus_ids'] ?? [],
            areas: $validated['areas'],
            flags: $validated['flags'] ?? [],
            crossTeamActions: $validated['cross_team_actions'] ?? [],
        );

        $submission = $action->execute($data);

        return redirect()
            ->route('weekly-submissions.show', $submission)
            ->with('success', 'Weekly submission saved as draft.');
    }

    public function show(GetWeeklySubmission $action, WeeklySubmission $weeklySubmission): View
    {
        $submission = $action->execute($weeklySubmission->id);

        return view('weekly-submissions.show', compact('submission'));
    }

    public function edit(Request $request, WeeklySubmission $weeklySubmission): View|RedirectResponse
    {
        if ($weeklySubmission->isSubmitted()) {
            return redirect()
                ->route('weekly-submissions.show', $weeklySubmission)
                ->with('error', 'Submitted updates cannot be edited.');
        }

        $user = $request->user();
        $okrs = $this->okrRepository->findActiveByUser($user->id);
        $managerAreas = $this->managerAreaRepository->findActiveByUser($user->id);

        $weeklySubmission->load([
            'okrFocus',
            'areas.outcomes.okr',
            'areas.priorities.okr',
            'flags',
            'crossTeamActions',
        ]);

        return view('weekly-submissions.edit', [
            'submission' => $weeklySubmission,
            'okrs' => $okrs,
            'managerAreas' => $managerAreas,
        ]);
    }

    public function update(
        UpdateWeeklySubmissionRequest $request,
        WeeklySubmission $weeklySubmission,
        UpdateWeeklySubmission $action,
    ): RedirectResponse {
        if ($weeklySubmission->isSubmitted()) {
            return redirect()
                ->route('weekly-submissions.show', $weeklySubmission)
                ->with('error', 'Submitted updates cannot be edited.');
        }

        $validated = $request->validated();

        $data = new UpdateWeeklySubmissionData(
            submissionId: $weeklySubmission->id,
            oneNumberValue: $validated['one_number_value'] ?? null,
            oneNumberLabel: $validated['one_number_label'] ?? null,
            okrFocusIds: $validated['okr_focus_ids'] ?? [],
            areas: $validated['areas'],
            flags: $validated['flags'] ?? [],
            crossTeamActions: $validated['cross_team_actions'] ?? [],
        );

        $action->execute($data);

        return redirect()
            ->route('weekly-submissions.show', $weeklySubmission)
            ->with('success', 'Weekly submission updated.');
    }

    public function destroy(WeeklySubmission $weeklySubmission, DeleteWeeklySubmission $action): RedirectResponse
    {
        if ($weeklySubmission->isSubmitted()) {
            return redirect()
                ->route('weekly-submissions.index')
                ->with('error', 'Submitted updates cannot be deleted.');
        }

        $action->execute($weeklySubmission->id);

        return redirect()
            ->route('weekly-submissions.index')
            ->with('success', 'Weekly submission deleted.');
    }

    public function previousData(Request $request, GetPreviousSubmissionData $action): JsonResponse
    {
        $data = $action->execute($request->user()->id);

        if (!$data) {
            return response()->json(['error' => 'No previous submission found.'], 404);
        }

        return response()->json($data);
    }

    public function analyze(AnalyzeSubmissionRequest $request, AnalyzeSubmissionContent $action): JsonResponse
    {
        try {
            $result = $action->execute($request->validated('raw_content'));

            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function submit(WeeklySubmission $weeklySubmission, SubmitWeeklySubmission $action): RedirectResponse
    {
        if ($weeklySubmission->isSubmitted()) {
            return redirect()
                ->route('weekly-submissions.show', $weeklySubmission)
                ->with('error', 'Already submitted.');
        }

        $action->execute($weeklySubmission->id);

        return redirect()
            ->route('weekly-submissions.show', $weeklySubmission)
            ->with('success', 'Weekly submission submitted successfully.');
    }
}

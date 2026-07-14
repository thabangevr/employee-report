<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Okr\CreateOkr;
use App\Actions\Okr\DeleteOkr;
use App\Actions\Okr\ToggleOkrActive;
use App\Actions\Okr\UpdateOkr;
use App\DTOs\Okr\CreateOkrData;
use App\DTOs\Okr\UpdateOkrData;
use App\Http\Requests\StoreOkrRequest;
use App\Http\Requests\UpdateOkrRequest;
use App\Models\Okr;
use App\Repositories\Contracts\OkrRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OkrController extends Controller
{
    public function __construct(
        private readonly OkrRepositoryInterface $okrRepository,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $okrs = $this->okrRepository->findAllByUserWithKeyResults($user->id);

        $activeOkrs = $okrs->where('is_active', true);
        $inactiveOkrs = $okrs->where('is_active', false);
        $totalWeight = $activeOkrs->sum('weight');

        return view('okrs.index', compact('okrs', 'activeOkrs', 'inactiveOkrs', 'totalWeight'));
    }

    public function create(): View
    {
        return view('okrs.create');
    }

    public function store(StoreOkrRequest $request, CreateOkr $action): RedirectResponse
    {
        $data = CreateOkrData::fromRequest($request);
        $action->execute($data);

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR created successfully.');
    }

    public function edit(Request $request, Okr $okr): View
    {
        if ($okr->user_id !== $request->user()->id) {
            abort(403);
        }

        $okr->load('keyResults');

        return view('okrs.edit', compact('okr'));
    }

    public function update(UpdateOkrRequest $request, Okr $okr, UpdateOkr $action): RedirectResponse
    {
        $data = UpdateOkrData::fromRequest($request, $okr->id);
        $action->execute($data);

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR updated successfully.');
    }

    public function toggle(Request $request, Okr $okr, ToggleOkrActive $action): RedirectResponse
    {
        if ($okr->user_id !== $request->user()->id) {
            abort(403);
        }

        $action->execute($okr->id);

        $status = $okr->is_active ? 'deactivated' : 'reactivated';

        return redirect()
            ->route('okrs.index')
            ->with('success', "OKR {$status}.");
    }

    public function destroy(Request $request, Okr $okr, DeleteOkr $action): RedirectResponse
    {
        if ($okr->user_id !== $request->user()->id) {
            abort(403);
        }

        $action->execute($okr->id);

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR deleted.');
    }
}

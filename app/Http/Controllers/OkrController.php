<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Okr\CreateOkr;
use App\Actions\Okr\DeleteOkr;
use App\Actions\Okr\UpdateOkr;
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
        $okrs = $this->okrRepository->findAllBy('user_id', $user->id);

        return view('okrs.index', compact('okrs'));
    }

    public function store(StoreOkrRequest $request, CreateOkr $action): RedirectResponse
    {
        $action->execute($request->user()->id, $request->validated('title'));

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR added.');
    }

    public function update(UpdateOkrRequest $request, Okr $okr, UpdateOkr $action): RedirectResponse
    {
        $action->execute(
            $okr->id,
            $request->validated('title'),
            (bool) $request->validated('is_active'),
        );

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR updated.');
    }

    public function destroy(Okr $okr, DeleteOkr $action): RedirectResponse
    {
        if ($okr->user_id !== request()->user()->id) {
            abort(403);
        }

        $action->execute($okr->id);

        return redirect()
            ->route('okrs.index')
            ->with('success', 'OKR deleted.');
    }
}

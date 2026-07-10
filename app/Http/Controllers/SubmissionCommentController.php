<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SubmissionComment\CreateSubmissionComment;
use App\Actions\SubmissionComment\DeleteSubmissionComment;
use App\Models\SubmissionComment;
use App\Models\WeeklySubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubmissionCommentController extends Controller
{
    public function store(
        Request $request,
        WeeklySubmission $weeklySubmission,
        CreateSubmissionComment $action,
    ): RedirectResponse {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $action->execute(
            $weeklySubmission->id,
            $request->user()->id,
            $validated['body'],
        );

        return back()->with('success', 'Comment added.');
    }

    public function destroy(
        Request $request,
        SubmissionComment $comment,
        DeleteSubmissionComment $action,
    ): RedirectResponse {
        if ($comment->user_id !== $request->user()->id) {
            abort(403);
        }

        $action->execute($comment->id);

        return back()->with('success', 'Comment deleted.');
    }
}

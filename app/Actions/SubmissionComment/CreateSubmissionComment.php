<?php

declare(strict_types=1);

namespace App\Actions\SubmissionComment;

use App\Models\SubmissionComment;
use App\Repositories\Contracts\SubmissionCommentRepositoryInterface;

class CreateSubmissionComment
{
    public function __construct(
        private readonly SubmissionCommentRepositoryInterface $commentRepository,
    ) {}

    public function execute(int $submissionId, int $userId, string $body): SubmissionComment
    {
        /** @var SubmissionComment */
        return $this->commentRepository->create([
            'weekly_submission_id' => $submissionId,
            'user_id' => $userId,
            'body' => $body,
        ]);
    }
}

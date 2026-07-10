<?php

declare(strict_types=1);

namespace App\Actions\SubmissionComment;

use App\Repositories\Contracts\SubmissionCommentRepositoryInterface;

class DeleteSubmissionComment
{
    public function __construct(
        private readonly SubmissionCommentRepositoryInterface $commentRepository,
    ) {}

    public function execute(int $commentId): bool
    {
        return $this->commentRepository->delete($commentId);
    }
}

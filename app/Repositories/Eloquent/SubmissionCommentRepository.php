<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\SubmissionComment;
use App\Repositories\Contracts\SubmissionCommentRepositoryInterface;

class SubmissionCommentRepository extends BaseRepository implements SubmissionCommentRepositoryInterface
{
    public function __construct(SubmissionComment $model)
    {
        parent::__construct($model);
    }
}

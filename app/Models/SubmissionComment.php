<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_submission_id',
        'user_id',
        'body',
    ];

    public function weeklySubmission(): BelongsTo
    {
        return $this->belongsTo(WeeklySubmission::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionCrossTeamAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_submission_id',
        'owner_name',
        'ask',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function weeklySubmission(): BelongsTo
    {
        return $this->belongsTo(WeeklySubmission::class);
    }
}

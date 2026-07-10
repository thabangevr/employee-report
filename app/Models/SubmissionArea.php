<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AreaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubmissionArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_submission_id',
        'manager_area_id',
        'name',
        'status',
        'status_justification',
        'sort_order',
    ];

    protected $casts = [
        'status' => AreaStatus::class,
        'sort_order' => 'integer',
    ];

    public function weeklySubmission(): BelongsTo
    {
        return $this->belongsTo(WeeklySubmission::class);
    }

    public function managerArea(): BelongsTo
    {
        return $this->belongsTo(ManagerArea::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(SubmissionOutcome::class)->orderBy('sort_order');
    }

    public function priorities(): HasMany
    {
        return $this->hasMany(SubmissionPriority::class)->orderBy('sort_order');
    }
}

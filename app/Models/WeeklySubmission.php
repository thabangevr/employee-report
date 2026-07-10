<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklySubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_start_date',
        'one_number_value',
        'one_number_label',
        'word_count',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'week_start_date' => 'date',
        'status' => SubmissionStatus::class,
        'submitted_at' => 'datetime',
        'word_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function okrFocus(): BelongsToMany
    {
        return $this->belongsToMany(Okr::class, 'weekly_submission_okr_focus');
    }

    public function areas(): HasMany
    {
        return $this->hasMany(SubmissionArea::class)->orderBy('sort_order');
    }

    public function flags(): HasMany
    {
        return $this->hasMany(SubmissionFlag::class)->orderBy('sort_order');
    }

    public function crossTeamActions(): HasMany
    {
        return $this->hasMany(SubmissionCrossTeamAction::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SubmissionComment::class)->orderBy('created_at');
    }

    public function isDraft(): bool
    {
        return $this->status === SubmissionStatus::Draft;
    }

    public function isSubmitted(): bool
    {
        return $this->status === SubmissionStatus::Submitted;
    }
}

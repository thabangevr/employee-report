<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionOutcome extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_area_id',
        'description',
        'okr_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function submissionArea(): BelongsTo
    {
        return $this->belongsTo(SubmissionArea::class);
    }

    public function okr(): BelongsTo
    {
        return $this->belongsTo(Okr::class);
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrKeyResult extends Model
{
    protected $fillable = [
        'okr_id',
        'type',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function okr(): BelongsTo
    {
        return $this->belongsTo(Okr::class);
    }
}

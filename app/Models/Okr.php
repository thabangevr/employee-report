<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Okr extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'objective_description',
        'measure_of_success',
        'weight',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'weight' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class)->orderBy('sort_order');
    }

    public function lagMeasures(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class)
            ->where('type', 'lag_measure')
            ->orderBy('sort_order');
    }

    public function leadMeasures(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class)
            ->where('type', 'lead_measure')
            ->orderBy('sort_order');
    }
}

<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HerCyclePeriod extends Model
{
    protected $table = 'her_cycle_periods';

    protected $fillable = [
        'profile_id',
        'start_date',
        'end_date',
        'flow_intensity',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(HerCycleProfile::class, 'profile_id');
    }

    public function getDurationAttribute(): int
    {
        if (!$this->end_date) {
            return 0;
        }
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}

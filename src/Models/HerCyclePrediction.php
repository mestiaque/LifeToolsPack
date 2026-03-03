<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HerCyclePrediction extends Model
{
    protected $table = 'her_cycle_predictions';

    protected $fillable = [
        'profile_id',
        'predicted_period_start',
        'predicted_ovulation',
        'fertile_window_start',
        'fertile_window_end',
        'pms_start',
        'is_active',
    ];

    protected $casts = [
        'predicted_period_start' => 'date',
        'predicted_ovulation' => 'date',
        'fertile_window_start' => 'date',
        'fertile_window_end' => 'date',
        'pms_start' => 'date',
        'is_active' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(HerCycleProfile::class, 'profile_id');
    }
}

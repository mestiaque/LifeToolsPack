<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HerCycleNotification extends Model
{
    protected $table = 'her_cycle_notifications';

    protected $fillable = [
        'profile_id',
        'period_reminder',
        'period_reminder_days',
        'pms_reminder',
        'pms_reminder_days',
        'fertile_reminder',
        'symptom_reminder',
        'reminder_time',
    ];

    protected $casts = [
        'period_reminder' => 'boolean',
        'period_reminder_days' => 'integer',
        'pms_reminder' => 'boolean',
        'pms_reminder_days' => 'integer',
        'fertile_reminder' => 'boolean',
        'symptom_reminder' => 'boolean',
        'reminder_time' => 'datetime:H:i:s',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(HerCycleProfile::class, 'profile_id');
    }
}

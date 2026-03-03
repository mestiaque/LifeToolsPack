<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HerCycleProfile extends Model
{
    protected $table = 'her_cycle_profiles';

    protected $fillable = [
        'user_id',
        'name',
        'age',
        'cycle_length',
        'period_length',
        'last_period_start',
    ];

    protected $casts = [
        'age' => 'integer',
        'cycle_length' => 'integer',
        'period_length' => 'integer',
        'last_period_start' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(HerCyclePeriod::class, 'profile_id');
    }

    public function symptoms(): HasMany
    {
        return $this->hasMany(HerCycleSymptom::class, 'profile_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(HerCyclePrediction::class, 'profile_id');
    }

    public function notification(): HasMany
    {
        return $this->hasMany(HerCycleNotification::class, 'profile_id');
    }

    public function getCurrentPrediction()
    {
        return $this->predictions()->where('is_active', true)->latest()->first();
    }

    public function predictNextPeriod(): ?array
    {
        $lastPeriod = $this->periods()->orderBy('start_date', 'desc')->first();

        if (!$lastPeriod) {
            return null;
        }

        $nextPeriodStart = $lastPeriod->start_date->addDays($this->cycle_length);

        return [
            'start' => $nextPeriodStart,
            'end' => $nextPeriodStart->addDays($this->period_length - 1),
        ];
    }

    public function predictFertileWindow(): ?array
    {
        $prediction = $this->predictNextPeriod();

        if (!$prediction) {
            return null;
        }

        // Ovulation typically occurs 14 days before next period
        $ovulationDate = $prediction['start']->copy()->subDays(14);
        $fertileStart = $ovulationDate->copy()->subDays(5);
        $fertileEnd = $ovulationDate->copy()->addDays(1);

        return [
            'ovulation' => $ovulationDate,
            'start' => $fertileStart,
            'end' => $fertileEnd,
        ];
    }

    public function predictPMS(): ?array
    {
        $prediction = $this->predictNextPeriod();

        if (!$prediction) {
            return null;
        }

        // PMS typically starts 3-7 days before period
        $pmsStart = $prediction['start']->copy()->subDays(7);

        return [
            'start' => $pmsStart,
            'end' => $prediction['start']->copy()->subDays(1),
        ];
    }
}

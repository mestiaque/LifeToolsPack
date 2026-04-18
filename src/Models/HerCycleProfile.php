<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class HerCycleProfile extends Model
{
    protected $table = 'her_cycle_profiles';

    protected $fillable = [
        'user_id',
        'name',
        'name_bn',
        'dob',
        'weight',
        'height',
        'blood_group',
        'notify_emails',
        'notify_phones',
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

    // ─── Calculated stats from actual logged periods ──────────────────────

    /**
     * All individual cycle lengths (days between consecutive period starts).
     */
    public function getCycleLengths(): array
    {
        $periodsArr = $this->periods()->orderBy('start_date', 'asc')->get();
        $lengths = [];
        for ($i = 0; $i < $periodsArr->count() - 1; $i++) {
            $lengths[] = (int) abs(
                $periodsArr[$i + 1]->start_date->diffInDays($periodsArr[$i]->start_date)
            );
        }
        return $lengths;
    }

    /**
     * Average cycle length from logged periods. Null when fewer than 2 periods exist.
     */
    public function getAvgCycle(): ?int
    {
        $lengths = $this->getCycleLengths();
        return count($lengths) ? (int) round(array_sum($lengths) / count($lengths)) : null;
    }

    /**
     * Average period duration (days) from completed logged periods. Null if none have end_date.
     */
    public function getAvgPeriod(): ?int
    {
        $periodLengths = [];
        foreach ($this->periods()->get() as $period) {
            if ($period->end_date) {
                $periodLengths[] = $period->start_date->diffInDays($period->end_date) + 1;
            }
        }
        return count($periodLengths) ? (int) round(array_sum($periodLengths) / count($periodLengths)) : null;
    }

    /**
     * Predicted start of next period based on logged data.
     * 0 periods → null | 1 period → +30 days from end/start | 2+ → last start + avgCycle
     */
    public function getNextPeriodStart(): ?Carbon
    {
        $periodsArr = $this->periods()->orderBy('start_date', 'asc')->get();
        $count = $periodsArr->count();

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            $first = $periodsArr->first();
            return $first->end_date
                ? $first->end_date->copy()->addDays(30)
                : $first->start_date->copy()->addDays(30);
        }

        $avgCycle = $this->getAvgCycle();
        $lastPeriod = $periodsArr->last();
        return $avgCycle ? $lastPeriod->start_date->copy()->addDays($avgCycle) : null;
    }

    /**
     * Predicted end of next period. Null when only 1 period logged or avgPeriod unavailable.
     */
    public function getNextPeriodEnd(): ?Carbon
    {
        $nextStart = $this->getNextPeriodStart();
        if (!$nextStart) {
            return null;
        }

        if ($this->periods()->count() === 1) {
            return null;
        }

        $avgPeriod = $this->getAvgPeriod();
        return $avgPeriod ? $nextStart->copy()->addDays($avgPeriod - 1) : null;
    }

    /**
     * Cycle regularity prediction: 'Good', 'Neutral', or 'Bad'.
     */
    public function getFlowPrediction(): string
    {
        $lengths = $this->getCycleLengths();
        if (count($lengths) > 1) {
            $variance = max($lengths) - min($lengths);
            if ($variance <= 3) return 'Good';
            if ($variance <= 7) return 'Neutral';
            return 'Bad';
        }
        return 'Neutral';
    }
}

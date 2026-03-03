<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HerCycleSymptom extends Model
{
    protected $table = 'her_cycle_symptoms';

    protected $fillable = [
        'profile_id',
        'date',
        'physical_symptoms',
        'emotional_symptoms',
        'sleep_quality',
        'energy_level',
        'custom_symptoms',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'physical_symptoms' => 'array',
        'emotional_symptoms' => 'array',
        'sleep_quality' => 'integer',
        'energy_level' => 'integer',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(HerCycleProfile::class, 'profile_id');
    }

    public static function physicalOptions(): array
    {
        return ['cramps', 'bloating', 'headaches', 'breast_tenderness', 'fatigue', 'backache'];
    }

    public static function emotionalOptions(): array
    {
        return ['happy', 'sad', 'anxious', 'irritable', 'energetic', 'calm', 'moody'];
    }
}

<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class MemorableDay extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'category',
        'location',
        'image_url',
        'color',
        'is_private',
        'reminder_enabled',
        'reminder_days_before',
        'tags',
        'importance_level',
        'repeat_yearly',
        'user_id',
    ];

    protected $casts = [
        'event_date'       => 'date:Y-m-d',
        'tags'             => 'array',
        'is_private'       => 'boolean',
        'reminder_enabled' => 'boolean',
        'repeat_yearly'    => 'boolean',
    ];
}

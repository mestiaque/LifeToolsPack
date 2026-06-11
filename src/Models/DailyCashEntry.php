<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCashEntry extends Model
{
    protected $fillable = [
        'title',
        'amount',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}

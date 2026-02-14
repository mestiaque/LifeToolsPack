<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'start', 'end', 'all_day'
    ];
}

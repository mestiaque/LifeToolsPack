<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title', 'description', 'color', 'is_pinned'
    ];
}

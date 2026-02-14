<?php

namespace EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Disk extends Model
{
    protected $table = 'disks';

    protected $fillable = [
        'code',
        'tag',
        'capacity',
        'used',
        'description',
        'content',
        'status',
    ];
}

<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class NotifyPerson extends Model
{
    protected $table = 'notify_people';

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'types',
        'user_type',
        'user_id',
    ];

    protected $casts = [
        'types' => 'array',
    ];
}

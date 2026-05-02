<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'title', 'description', 'link'
    ];
}

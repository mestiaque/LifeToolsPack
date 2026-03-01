<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'start', 'end', 'all_day'
    ];

    public function expenses()
    {
        return $this->hasMany(EventExpense::class);
    }

    public function totalExpense()
    {
        return $this->expenses()->sum('amount');
    }
}

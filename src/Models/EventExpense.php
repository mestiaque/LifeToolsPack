<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class EventExpense extends Model
{
    protected $table = 'event_expenses';

    protected $fillable = [
        'event_id',
        'title',
        'amount',
        'amount_min',
        'amount_max',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_min' => 'decimal:2',
        'amount_max' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}

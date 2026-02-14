<?php

namespace EmCore\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyExpense extends Model
{
    use HasFactory;

    protected $table = 'daily_expenses';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'title',
        'amount',
        'description',
        'created_at'
    ];

    /**
     * Cast `date` field to Carbon instance
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];
}

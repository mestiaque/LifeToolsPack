<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyFinancialSummary extends Model
{
    protected $table = 'monthly_financial_summaries';

    protected $fillable = [
        'month_label',
        'type',
        'title',
        'amount',
        'date',
        'note',
    ];

    protected $casts = [
        'amount' => 'float',
        'date' => 'date',
    ];
}

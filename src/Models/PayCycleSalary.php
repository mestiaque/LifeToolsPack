<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class PayCycleSalary extends Model
{
    protected $table = 'paycycle_salaries';

    protected $fillable = [
        'month_label',
        'salary_amount',
        'expected_date',
        'received_date',
        'expected_expense',
        'note',
    ];

    protected $casts = [
        'expected_date' => 'date',
        'received_date' => 'date',
        'salary_amount' => 'decimal:2',
        'expected_expense' => 'decimal:2',
    ];
}

<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_user_id', 'amount', 'type', 'date', 'installment', 'completed_installments',
        'installment_labels', 'installment_expected_dates', 'installment_amounts', 'note'
    ];

    protected $casts = [
        'installment_labels' => 'array',
        'installment_expected_dates' => 'array',
        'installment_amounts' => 'array',
    ];

    public function loanUser()
    {
        return $this->belongsTo(LoanUser::class, 'loan_user_id');
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class, 'loan_id');
    }

    public function totalRepayment()
    {
        return $this->repayments()->sum('amount');
    }

    public function dueAmount()
    {
        return $this->amount - $this->totalRepayment();
    }
}

<?php

namespace EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_user_id', 'amount', 'type', 'date', 'note'
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

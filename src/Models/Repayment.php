<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = [
        'loan_user_id', 'loan_id', 'amount', 'date', 'note'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanUser()
    {
        return $this->belongsTo(LoanUser::class, 'loan_user_id');
    }
}

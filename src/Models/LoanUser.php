<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class LoanUser extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'is_active',
    ];
    
    public function loans()
    {
        return $this->hasMany(Loan::class, 'loan_user_id');
    }

    public function customPaymentPlans()
    {
        return $this->hasMany(CustomLoanPaymentPlan::class, 'loan_user_id');
    }
}


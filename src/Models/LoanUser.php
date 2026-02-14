<?php

namespace EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class LoanUser extends Model
{
    protected $fillable = [
        'name'
    ];
    public function loans()
    {
        return $this->hasMany(Loan::class, 'loan_user_id');
    }
}


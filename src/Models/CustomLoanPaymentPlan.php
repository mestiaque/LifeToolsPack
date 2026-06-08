<?php

namespace ME\EmCore\Models;

use Illuminate\Database\Eloquent\Model;

class CustomLoanPaymentPlan extends Model
{
	protected $fillable = [
		'loan_id',
		'loan_user_id',
		'planned_month',
		'planned_amount',
		'note',
	];

	protected $casts = [
		'planned_month' => 'date',
		'planned_amount' => 'float',
	];

	public function loanUser()
	{
		return $this->belongsTo(LoanUser::class, 'loan_user_id');
	}

}


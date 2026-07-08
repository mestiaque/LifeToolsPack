<?php

namespace ME\EmCore\Services;

use Carbon\Carbon;
use ME\EmCore\Models\DailyExpense;
use ME\EmCore\Models\Loan;
use ME\EmCore\Models\PayCycleSalary;

class PayCycleForecastService
{
    private const EXPENSE_AVERAGE_LOOKBACK_DAYS = 60;
    private const CYCLE_LENGTH_DAYS = 35;

    public function forecast(PayCycleSalary $cycle): array
    {
        $windowStart = ($cycle->received_date ?? $cycle->expected_date)->copy();
        $windowEnd = $windowStart->copy()->addDays(self::CYCLE_LENGTH_DAYS);

        $loanBreakdown = $this->loanInstallmentsDue($windowStart, $windowEnd);
        $loanDue = collect($loanBreakdown)->sum('amount');
        $expectedExpense = $cycle->expected_expense !== null
            ? (float) $cycle->expected_expense
            : $this->estimateExpense($windowStart, $windowEnd);

        $salary = (float) $cycle->salary_amount;
        $projectedBalance = $salary - $expectedExpense - $loanDue;

        return [
            'window_start' => $windowStart,
            'window_end' => $windowEnd,
            'salary_amount' => $salary,
            'expected_expense' => $expectedExpense,
            'loan_installments_due' => $loanDue,
            'loan_installments_breakdown' => $loanBreakdown,
            'projected_balance' => $projectedBalance,
            'shortfall' => $projectedBalance < 0 ? abs($projectedBalance) : 0.0,
        ];
    }

    private function loanInstallmentsDue(Carbon $windowStart, Carbon $windowEnd): array
    {
        $breakdown = [];

        Loan::where('type', 'taken')->with('loanUser')->get()->each(function (Loan $loan) use ($windowStart, $windowEnd, &$breakdown) {
            $dates = $loan->installment_expected_dates ?? [];
            $amounts = $loan->installment_amounts ?? [];
            $labels = $loan->installment_labels ?? [];

            foreach ($dates as $index => $date) {
                if ($index < $loan->completed_installments || $date === null) {
                    continue;
                }

                $dueDate = Carbon::parse($date);
                if ($dueDate->between($windowStart, $windowEnd)) {
                    $breakdown[] = [
                        'loan_id' => $loan->id,
                        'loan_user' => $loan->loanUser->name ?? __('Unknown'),
                        'label' => $labels[$index] ?? (($index + 1) . ' Installment'),
                        'due_date' => $dueDate,
                        'amount' => (float) ($amounts[$index] ?? 0),
                    ];
                }
            }
        });

        return $breakdown;
    }

    private function estimateExpense(Carbon $windowStart, Carbon $windowEnd): float
    {
        $lookbackStart = now()->subDays(self::EXPENSE_AVERAGE_LOOKBACK_DAYS);

        $dailyAverage = DailyExpense::where('created_at', '>=', $lookbackStart)->sum('amount')
            / max(self::EXPENSE_AVERAGE_LOOKBACK_DAYS, 1);

        $cycleDays = max($windowStart->diffInDays($windowEnd), 1);

        return round($dailyAverage * $cycleDays, 2);
    }
}

<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Loan;
use ME\EmCore\Models\LoanUser;
use ME\EmCore\Models\Repayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class LoanController extends Controller
{
    private const AUTO_INSTALLMENT_REPAYMENT_NOTE = '[AUTO_INSTALLMENT_REPAYMENT]';

    public function __construct()
    {
        // Skip middleware for the Telegram methods
        if (in_array(request()->route() ? request()->route()->getName() : '', ['getTelegramLoanSummary'])) {
            return;
        }

        $this->middleware('authorization:loan.show')->only(['index', 'paymentPlanner']);
        $this->middleware('authorization:loan.create')->only(['createLoan', 'storeLoan']);
        $this->middleware('authorization:loan.edit')->only(['editLoan', 'updateLoan']);
        $this->middleware('authorization:loan.delete')->only(['deleteLoan']);
        $this->middleware('authorization:loan.history')->only(['history']);
        $this->middleware('authorization:loan.history_create')->only(['storeRepayment', 'updateRepayment']);
        $this->middleware('authorization:loan.history_delete')->only(['destroyRepayment']);
        $this->middleware('authorization:loan.user_history')->only(['userHistory']);
        $this->middleware('authorization:loan.payment_planner')->only(['paymentPlanner']);
    }
    // List all loans with their loan users
    public function index(Request $request)
    {
        $query = Loan::with('loanUser', 'repayments');
        $query->whereHas('loanUser', function($q) {
            $q->where('is_active', true);
        });
        if ($request->filled('name')) {
            $query->whereHas('loanUser', function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }
        if ($request->filled('amount')) {
            $query->where('amount', $request->amount);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        $loans = $query->get();

        if (!$request->boolean('show_settled')) {
            $loans = $loans->filter(fn ($loan) => $loan->dueAmount() > 0);
        }

        // Calculate summary with separate given and taken
        $totalGivenLoan = $loans->where('type', 'given')->sum('amount');
        $totalTakenLoan = $loans->where('type', 'taken')->sum('amount');

        $totalGivenRepayment = $loans->where('type', 'given')->sum(function($loan) {
            return $loan->totalRepayment();
        });
        $totalTakenRepayment = $loans->where('type', 'taken')->sum(function($loan) {
            return $loan->totalRepayment();
        });

        $totalGivenDue = $loans->where('type', 'given')->sum(function($loan) {
            return $loan->dueAmount();
        });
        $totalTakenDue = $loans->where('type', 'taken')->sum(function($loan) {
            return $loan->dueAmount();
        });

        // Add total variables (sum of given and taken)
        $totalLoan = $totalGivenLoan + $totalTakenLoan;
        $totalRepayment = $totalGivenRepayment + $totalTakenRepayment;
        $totalDue = $totalGivenDue + $totalTakenDue;

        // User-wise summary with separate given and taken
        $userSummary = [];
        foreach ($loans as $loan) {
            $userId = $loan->loan_user_id;
            if (!isset($userSummary[$userId])) {
                $userSummary[$userId] = [
                    'user' => $loan->loanUser,
                    'given_loan' => 0,
                    'taken_loan' => 0,
                    'given_repayment' => 0,
                    'taken_repayment' => 0,
                    'given_due' => 0,
                    'taken_due' => 0,
                ];
            }

            if ($loan->type == 'given') {
                $userSummary[$userId]['given_loan'] += $loan->amount;
                $userSummary[$userId]['given_repayment'] += $loan->totalRepayment();
                $userSummary[$userId]['given_due'] += $loan->dueAmount();
            } else {
                $userSummary[$userId]['taken_loan'] += $loan->amount;
                $userSummary[$userId]['taken_repayment'] += $loan->totalRepayment();
                $userSummary[$userId]['taken_due'] += $loan->dueAmount();
            }
        }

        return view('em_core::loans.index', compact('loans',
            'totalGivenLoan', 'totalTakenLoan', 'totalLoan',
            'totalGivenRepayment', 'totalTakenRepayment', 'totalRepayment',
            'totalGivenDue', 'totalTakenDue', 'totalDue',
            'userSummary'));
    }

    // Show history for a loan (with loan users and repayments)
    public function history($loanId)
    {
        $loan = Loan::with('loanUser', 'repayments')->findOrFail($loanId);
        $repayments = $loan->repayments()->orderBy('date')->get();
        return view('em_core::loans.history', compact('loan', 'repayments'));
    }

    // Payment planner for upcoming payable/receivable installment schedule
    public function paymentPlanner()
    {
        $today = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();
        $currentMonthKey = $today->format('Y-m');
        $nextMonthKey = $today->copy()->addMonth()->format('Y-m');
        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $startMonth->copy()->addMonths($i);
            $monthKey = $month->format('Y-m');
            $months[$monthKey] = [
                'month' => $month->format('M Y'),
                'payable' => 0,
                'receivable' => 0,
                'is_current' => $monthKey === $currentMonthKey,
                'is_next' => $monthKey === $nextMonthKey,
            ];
        }

        $schedule = [];

        $loans = Loan::with('loanUser')
            ->whereHas('loanUser', function ($q) {
                $q->where('is_active', true);
            })
            ->get();

        foreach ($loans as $loan) {
            $installmentCount = max((int) ($loan->installment ?? 1), 1);
            $loanAmount = (float) $loan->amount;
            $totalRepayment = (float) $loan->totalRepayment();
            $remainingAmount = round($loanAmount - $totalRepayment, 2);

            // If remaining amount is zero (or less), do not show in planner.
            if ($remainingAmount <= 0) {
                continue;
            }

            $perInstallmentAmount = $loanAmount / $installmentCount;
            $installmentAmounts = is_array($loan->installment_amounts) ? $loan->installment_amounts : [];
            $normalizedAmounts = [];
            for ($idx = 0; $idx < $installmentCount; $idx++) {
                $rawAmount = $installmentAmounts[$idx] ?? null;
                $normalizedAmounts[$idx] = is_numeric($rawAmount) ? (float) $rawAmount : $perInstallmentAmount;
            }

            $installmentDates = is_array($loan->installment_expected_dates) ? $loan->installment_expected_dates : [];

            $manualCompleted = min(max((int) ($loan->completed_installments ?? 0), 0), $installmentCount);
            $repaymentCompleted = 0;
            $runningSum = 0.0;
            foreach ($normalizedAmounts as $idx => $amountValue) {
                $runningSum += (float) $amountValue;
                if (($totalRepayment + 0.00001) >= $runningSum) {
                    $repaymentCompleted = $idx + 1;
                } else {
                    break;
                }
            }
            $repaymentCompleted = min(max($repaymentCompleted, 0), $installmentCount);
            $completedInstallments = max($manualCompleted, $repaymentCompleted);

            if ($completedInstallments >= $installmentCount) {
                continue;
            }

            $baseDate = Carbon::parse($loan->date)->startOfDay();

            for ($i = $completedInstallments + 1; $i <= $installmentCount; $i++) {
                $expectedDateValue = $installmentDates[$i - 1] ?? null;
                $expectedDate = $expectedDateValue ? Carbon::parse($expectedDateValue) : $baseDate->copy()->addMonths($i);
                $monthKey = $expectedDate->format('Y-m');
                $isPayable = $loan->type === 'taken';
                $itemAmount = (float) ($normalizedAmounts[$i - 1] ?? $perInstallmentAmount);

                $item = [
                    'date' => $expectedDate->format('Y-m-d'),
                    'party' => $loan->loanUser->name ?? '-',
                    'amount' => $itemAmount,
                    'direction' => $isPayable ? 'payable' : 'receivable',
                    'direction_label' => $isPayable ? __('Pay') : __('Receive'),
                    'installment_no' => $i,
                    'loan_id' => $loan->id,
                ];

                $schedule[] = $item;

                if (isset($months[$monthKey])) {
                    if ($isPayable) {
                        $months[$monthKey]['payable'] += $itemAmount;
                    } else {
                        $months[$monthKey]['receivable'] += $itemAmount;
                    }
                }
            }
        }

        usort($schedule, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return view('em_core::loans.payment-planner', [
            'schedule' => $schedule,
            'months' => array_values($months),
        ]);
    }

    // Create loan form (show loan users for selection)
    public function createLoan()
    {
        $loanUsers = LoanUser::all();
        return view('em_core::loans.create', compact('loanUsers'));
    }

    // Store loan and assign loan users
    public function storeLoan(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:given,taken',
            'date' => 'required|date',
            'installment' => 'required|integer|min:1',
            'installment_labels' => 'nullable|array',
            'installment_labels.*' => 'nullable|string',
            'installment_expected_dates' => 'nullable|array',
            'installment_expected_dates.*' => 'nullable|date',
            'installment_amounts' => 'nullable|array',
            'installment_amounts.*' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'loan_user_id' => 'required|integer|min:1',
        ]);
        $payload = $request->only(['loan_user_id', 'amount', 'type', 'date', 'installment', 'note']);
        $payload['completed_installments'] = 0;
        $payload = array_merge($payload, $this->buildInstallmentSchedulePayload($request));
        $loan = Loan::create($payload);

        return redirect()->route('admin.loans.index')->with('success', __('Loan created successfully.'));
    }

    // Edit loan form
    public function editLoan($id)
    {
        $loan = Loan::with('loanUser')->findOrFail($id);
        $loanUsers = LoanUser::all();
        return view('em_core::loans.edit', compact('loan', 'loanUsers'));
    }

    // Update loan and its loan users
    public function updateLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:given,taken',
            'date' => 'required|date',
            'installment' => 'required|integer|min:1',
            'installment_labels' => 'nullable|array',
            'installment_labels.*' => 'nullable|string',
            'installment_expected_dates' => 'nullable|array',
            'installment_expected_dates.*' => 'nullable|date',
            'installment_amounts' => 'nullable|array',
            'installment_amounts.*' => 'nullable|numeric|min:0',
            'done_installments' => 'nullable|array',
            'done_installments.*' => 'integer|min:1',
            'note' => 'nullable|string',
            'loan_user_id' => 'required|integer|min:1',
        ]);
        $installmentCount = (int) $request->installment;
        $doneInstallments = collect($request->input('done_installments', []))
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value >= 1 && $value <= $installmentCount)
            ->unique()
            ->values();

        $completedInstallments = $doneInstallments->count();

        $payload = $request->only(['amount', 'type', 'date', 'installment', 'note', 'loan_user_id']);
        $payload['completed_installments'] = $completedInstallments;
        $payload = array_merge($payload, $this->buildInstallmentSchedulePayload($request));

        DB::transaction(function () use ($loan, $payload, $doneInstallments) {
            $loan->update($payload);
            $this->syncAutoInstallmentRepayment($loan, $payload, $doneInstallments->all());
        });


        return redirect()->route('admin.loans.index')->with('success', __('Loan updated successfully.'));
    }

    private function syncAutoInstallmentRepayment(Loan $loan, array $payload, array $doneInstallments): void
    {
        $installmentAmounts = is_array($payload['installment_amounts'] ?? null) ? $payload['installment_amounts'] : [];
        $autoInstallmentAmount = 0.0;

        foreach ($doneInstallments as $installmentNo) {
            $idx = ((int) $installmentNo) - 1;
            $amount = $installmentAmounts[$idx] ?? 0;
            if (is_numeric($amount)) {
                $autoInstallmentAmount += (float) $amount;
            }
        }

        $autoInstallmentAmount = round($autoInstallmentAmount, 2);

        $manualRepaymentTotal = (float) Repayment::where('loan_id', $loan->id)
            ->where(function ($query) {
                $query->whereNull('note')
                    ->orWhere('note', '!=', self::AUTO_INSTALLMENT_REPAYMENT_NOTE);
            })
            ->sum('amount');

        $loanAmount = (float) ($payload['amount'] ?? $loan->amount ?? 0);
        $maxAutoInstallmentAmount = max($loanAmount - $manualRepaymentTotal, 0);
        $finalAutoAmount = round(min($autoInstallmentAmount, $maxAutoInstallmentAmount), 2);

        $autoRepayment = Repayment::where('loan_id', $loan->id)
            ->where('note', self::AUTO_INSTALLMENT_REPAYMENT_NOTE)
            ->first();

        if ($finalAutoAmount <= 0) {
            if ($autoRepayment) {
                $autoRepayment->delete();
            }

            return;
        }

        $autoPayload = [
            'loan_user_id' => $payload['loan_user_id'] ?? $loan->loan_user_id,
            'amount' => $finalAutoAmount,
            'date' => Carbon::today()->format('Y-m-d'),
            'note' => self::AUTO_INSTALLMENT_REPAYMENT_NOTE,
        ];

        if ($autoRepayment) {
            $autoRepayment->update($autoPayload);
            return;
        }

        $autoPayload['loan_id'] = $loan->id;
        Repayment::create($autoPayload);
    }

    public function deleteLoan($id)
    {
        Loan::findOrFail($id)->delete();
        return redirect()->route('admin.loans.index')->with('success', __('Loan deleted successfully.'));
    }

    public function storeRepayment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'note' => 'nullable|string'
        ]);
        $loan = Loan::findOrFail($request->loan_id);
        $request->merge(['loan_user_id' => $loan->loan_user_id]);
        Repayment::create($request->all());
        return redirect()->back()->with('success', __('Repayment created successfully.'));
    }

    public function updateRepayment(Request $request, $id)
    {
        $repayment = Repayment::findOrFail($id);
        $repayment->update($request->all());
        return redirect()->back()->with('success', __('Repayment updated successfully.'));
    }

    public function deleteRepayment($id)
    {
        Repayment::findOrFail($id)->delete();
        return redirect()->back()->with('success', __('Repayment deleted successfully.'));
    }

    // Show user history with all loans and repayments as a statement
    public function userHistory($userId)
    {
        $loanUser = LoanUser::findOrFail($userId);
        $loans = Loan::where('loan_user_id', $userId)->with('repayments')->get();

        // Prepare statement data - combine loans and repayments with dates
        $transactions = [];

        // Add loans to transactions
        foreach ($loans as $loan) {
            $transactions[] = [
                'date' => $loan->date,
                'type' => 'loan',
                'loan_type' => $loan->type,
                'description' => ucfirst($loan->type) . ' Loan - ' . ($loan->note ? $loan->note : 'No note'),
                'amount' => $loan->amount,
                'loan_id' => $loan->id,
                'note' => $loan->note,
            ];

            // Add repayments for this loan
            foreach ($loan->repayments as $repayment) {
                $transactions[] = [
                    'date' => $repayment->date,
                    'type' => 'repayment',
                    'loan_type' => $loan->type,
                    'description' => 'Repayment for ' . ucfirst($loan->type) . ' Loan',
                    'amount' => -$repayment->amount,
                    'loan_id' => $loan->id,
                    'repayment_id' => $repayment->id,
                    'note' => $repayment->note,
                ];
            }
        }

        // Sort transactions by date
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        // Calculate summaries
        $givenLoans = $loans->where('type', 'given');
        $takenLoans = $loans->where('type', 'taken');

        $totalGivenAmount = $givenLoans->sum('amount');
        $totalTakenAmount = $takenLoans->sum('amount');

        $totalGivenRepayment = $givenLoans->sum(function($loan) {
            return $loan->totalRepayment();
        });
        $totalTakenRepayment = $takenLoans->sum(function($loan) {
            return $loan->totalRepayment();
        });

        $totalGivenDue = $givenLoans->sum(function($loan) {
            return $loan->dueAmount();
        });
        $totalTakenDue = $takenLoans->sum(function($loan) {
            return $loan->dueAmount();
        });

        $netBalance = $totalGivenDue - $totalTakenDue;

        return view('em_core::loans.user-history', compact(
            'loanUser', 'loans', 'transactions',
            'totalGivenAmount', 'totalTakenAmount',
            'totalGivenRepayment', 'totalTakenRepayment',
            'totalGivenDue', 'totalTakenDue',
            'netBalance'
        ));
    }

    /**
     * Get loan summary data for Telegram bot
     *
     * @return array
     */
    public function getTelegramLoanSummary()
    {
        $loans = Loan::with('loanUser', 'repayments')->get()
                ->filter(function ($loan) {
                    return $loan->loanUser && $loan->loanUser->is_active;
                })
                ->filter(function ($loan) {
                    return $loan->dueAmount() > 0;
                });

        // Calculate summary with separate given and taken
        $totalGivenLoan = $loans->where('type', 'given')->sum('amount');
        $totalTakenLoan = $loans->where('type', 'taken')->sum('amount');

        $totalGivenRepayment = $loans->where('type', 'given')->sum(function($loan) {
            return $loan->totalRepayment();
        });
        $totalTakenRepayment = $loans->where('type', 'taken')->sum(function($loan) {
            return $loan->totalRepayment();
        });

        $totalGivenDue = $loans->where('type', 'given')->sum(function($loan) {
            return $loan->dueAmount();
        });
        $totalTakenDue = $loans->where('type', 'taken')->sum(function($loan) {
            return $loan->dueAmount();
        });

        // User-wise summary with separate given and taken
        $userSummary = [];
        foreach ($loans as $loan) {
            $userId = $loan->loan_user_id;
            if (!isset($userSummary[$userId])) {
                $userSummary[$userId] = [
                    'user' => $loan->loanUser,
                    'given_loan' => 0,
                    'taken_loan' => 0,
                    'given_repayment' => 0,
                    'taken_repayment' => 0,
                    'given_due' => 0,
                    'taken_due' => 0,
                ];
            }

            if ($loan->type == 'given') {
                $userSummary[$userId]['given_loan'] += $loan->amount;
                $userSummary[$userId]['given_repayment'] += $loan->totalRepayment();
                $userSummary[$userId]['given_due'] += $loan->dueAmount();
            } else {
                $userSummary[$userId]['taken_loan'] += $loan->amount;
                $userSummary[$userId]['taken_repayment'] += $loan->totalRepayment();
                $userSummary[$userId]['taken_due'] += $loan->dueAmount();
            }
        }

        return [
            'totalGivenLoan' => $totalGivenLoan,
            'totalTakenLoan' => $totalTakenLoan,
            'totalGivenRepayment' => $totalGivenRepayment,
            'totalTakenRepayment' => $totalTakenRepayment,
            'totalGivenDue' => $totalGivenDue,
            'totalTakenDue' => $totalTakenDue,
            'userSummary' => $userSummary
        ];
    }

    private function buildInstallmentSchedulePayload(Request $request): array
    {
        $installmentCount = max((int) $request->input('installment', 1), 1);
        $baseDate = Carbon::parse($request->input('date'))->startOfDay();
        $amount = (float) $request->input('amount', 0);
        $perInstallment = $installmentCount > 0 ? ($amount / $installmentCount) : 0;

        $labels = (array) $request->input('installment_labels', []);
        $dates = (array) $request->input('installment_expected_dates', []);
        $amounts = (array) $request->input('installment_amounts', []);

        $finalLabels = [];
        $finalDates = [];
        $finalAmounts = [];

        for ($i = 1; $i <= $installmentCount; $i++) {
            $hasLabel = array_key_exists($i - 1, $labels);
            $labelRaw = $hasLabel ? (string) $labels[$i - 1] : '';
            $dateRaw = (string) ($dates[$i - 1] ?? '');
            $amountRaw = $amounts[$i - 1] ?? null;

            $finalLabels[] = $hasLabel ? $labelRaw : ($i . ' Installment');

            if ($dateRaw !== '' && strtotime($dateRaw) !== false) {
                $finalDates[] = Carbon::parse($dateRaw)->format('Y-m-d');
            } else {
                $finalDates[] = $baseDate->copy()->addMonths($i)->format('Y-m-d');
            }

            $finalAmounts[] = is_numeric($amountRaw) ? round((float) $amountRaw, 2) : round($perInstallment, 2);
        }

        return [
            'installment_labels' => $finalLabels,
            'installment_expected_dates' => $finalDates,
            'installment_amounts' => $finalAmounts,
        ];
    }
}

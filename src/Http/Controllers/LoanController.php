<?php

namespace EmCore\Http\Controllers;

use EmCore\Models\Loan;
use EmCore\Models\LoanUser;
use EmCore\Models\Repayment;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class LoanController extends Controller
{
    public function __construct()
    {
        // Skip middleware for the Telegram methods
        if (in_array(request()->route() ? request()->route()->getName() : '', ['getTelegramLoanSummary'])) {
            return;
        }

        $this->middleware('authorization:loan.show')->only(['index']);
        $this->middleware('authorization:loan.create')->only(['createLoan', 'storeLoan']);
        $this->middleware('authorization:loan.edit')->only(['editLoan', 'updateLoan']);
        $this->middleware('authorization:loan.delete')->only(['deleteLoan']);

        $this->middleware('authorization:loan.history')->only(['history']);
        $this->middleware('authorization:loan.history_create')->only(['storeRepayment', 'updateRepayment']);
        $this->middleware('authorization:loan.history_delete')->only(['destroyRepayment']);
    }
    // List all loans with their loan users
    public function index(Request $request)
    {
        $query = Loan::with('loanUser', 'repayments');
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
            'note' => 'nullable|string',
            'loan_user_id' => 'required|integer|min:1',
        ]);
        $loan = Loan::create($request->only(['loan_user_id', 'amount', 'type', 'date', 'note']));

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
            'note' => 'nullable|string',
            'loan_user_id' => 'required|integer|min:1',
        ]);
        $loan->update($request->only(['amount', 'type', 'date', 'note', 'loan_user_id']));


        return redirect()->route('admin.loans.index')->with('success', __('Loan updated successfully.'));
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

    /**
     * Get loan summary data for Telegram bot
     *
     * @return array
     */
    public function getTelegramLoanSummary()
    {
        // $loans = Loan::with('loanUser', 'repayments')->get();

        $loans = Loan::with('loanUser', 'repayments')->get()
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
}

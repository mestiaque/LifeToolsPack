<?php

namespace ME\EmCore\Http\Controllers;

use Carbon\Carbon;
use ME\Models\Setting;
use ME\EmCore\Models\DailyCashEntry;
use ME\EmCore\Models\DailyExpense;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class DailyExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:daily-expense.show')->only(['index']);
        $this->middleware('authorization:daily-expense.create')->only(['store']);
        $this->middleware('authorization:daily-expense.edit')->only(['update']);
        $this->middleware('authorization:daily-expense.delete')->only(['destroy']);
        $this->middleware('authorization:daily-expense.create')->only(['storeCash']);
        $this->middleware('authorization:daily-expense.edit')->only(['updateCash']);
        $this->middleware('authorization:daily-expense.delete')->only(['destroyCash']);
    }

    public function index(Request $request)
    {
        $query = DailyExpense::query();

        // -----------------------------
        // TITLE SEARCH
        // -----------------------------
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // -----------------------------
        // DATE FILTER LOGIC
        // -----------------------------
        if ($request->filled('from') && !$request->filled('to')) {
            // only FROM → single day
            $from = Carbon::parse($request->from)->startOfDay();
            $to   = Carbon::parse($request->from)->endOfDay();
        } elseif ($request->filled('from') && $request->filled('to')) {
            // FROM + TO
            $from = Carbon::parse($request->from)->startOfDay();
            $to   = Carbon::parse($request->to)->endOfDay();
        } else {
            // DEFAULT → current month
            $from = now()->startOfMonth();
            $to   = now()->endOfMonth();
        }

        $query->whereBetween('created_at', [$from, $to]);

        // -----------------------------
        // SETTINGS
        // -----------------------------
        $settings = Setting::latest()->first();

        // -----------------------------
        // FETCH ALL DATA
        // -----------------------------
        $expenses = $query->orderBy('created_at', 'desc')->get();

        // -----------------------------
        // GRAND TOTAL
        // -----------------------------
        $totalAmount = $expenses->sum('amount');
        $cashEntries = DailyCashEntry::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->orderByDesc('created_at')
            ->get();
        $monthlyExpenseTotal = DailyExpense::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->sum('amount');
        $totalCash = $cashEntries->sum('amount');
        $cashUsagePercent = $totalCash > 0 ? min(100, round(($monthlyExpenseTotal / $totalCash) * 100, 2)) : 0;
        $cashRemaining = $totalCash - $monthlyExpenseTotal;

        // -----------------------------
        // DATE-WISE GROUPING
        // -----------------------------
        $groupedExpenses = $expenses->groupBy(fn ($item) => $item->created_at->format('Y-m-d'));

        // -----------------------------
        // RETURN VIEW
        // -----------------------------
        return view('em_core::daily-expenses.index', compact(
            'expenses',
            'groupedExpenses',
            'totalAmount',
            'settings',
            'from',
            'to',
            'cashEntries',
            'monthlyExpenseTotal',
            'totalCash',
            'cashUsagePercent',
            'cashRemaining'
        ));
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric',
            'description' => 'nullable|string',
            'created_at'  => 'nullable|string',
        ]);

        DailyExpense::create($request->except('redirect_to'));

        return redirect()
            ->to($request->input('redirect_to') ?: route('admin.daily-expenses.index'))
            ->with('success', __('Expense added successfully.'));
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $expense = DailyExpense::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $expense->update($request->all());

        return redirect()
            ->route('admin.daily-expenses.index')
            ->with('success', __('Expense updated successfully.'));
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        $expense = DailyExpense::findOrFail($id);
        $expense->delete();

        return redirect()
            ->route('admin.daily-expenses.index')
            ->with('success', __('Expense deleted successfully.'));
    }

    public function storeCash(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        DailyCashEntry::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'created_at' => now(),
        ]);

        return redirect()
            ->route('admin.daily-expenses.index')
            ->with('success', __('Cash entry added successfully.'));
    }

    public function updateCash(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $cashEntry = DailyCashEntry::findOrFail($id);
        $cashEntry->update([
            'title' => $request->title,
            'amount' => $request->amount,
        ]);

        return redirect()
            ->route('admin.daily-expenses.index')
            ->with('success', __('Cash entry updated successfully.'));
    }

    public function destroyCash($id)
    {
        $cashEntry = DailyCashEntry::findOrFail($id);
        $cashEntry->delete();

        return redirect()
            ->route('admin.daily-expenses.index')
            ->with('success', __('Cash entry deleted successfully.'));
    }
}

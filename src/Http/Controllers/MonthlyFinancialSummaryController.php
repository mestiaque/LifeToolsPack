<?php

namespace ME\EmCore\Http\Controllers;

use Carbon\Carbon;
use ME\EmCore\Models\MonthlyFinancialSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class MonthlyFinancialSummaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:monthly_financial_summary.show')->only(['index']);
        $this->middleware('authorization:monthly_financial_summary.create')->only(['store']);
        $this->middleware('authorization:monthly_financial_summary.edit')->only(['update']);
        $this->middleware('authorization:monthly_financial_summary.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        
        $query = MonthlyFinancialSummary::query();

        if ($request->filled('month')) {
            $query->where('month_label', $request->month);
        } else {
            $query->where('month_label', '>=', now()->format('Y-m'));
        }

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $entries = $query->orderByDesc('month_label')
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $typeLabels = [
            'fund' => __('Fund'),
            'loan_payment' => __('Loan Pay'),
            'expense' => __('Expense'),
        ];
        $typeOrder = ['fund' => 1, 'loan_payment' => 2, 'expense' => 3];

        $monthsData = $entries
            ->groupBy('month_label')
            ->map(function ($monthItems, $monthLabel) use ($typeLabels, $typeOrder) {
                $cards = $monthItems
                    ->groupBy('type')
                    ->map(function ($items, $type) use ($typeLabels) {
                        return [
                            'type' => $type,
                            'card_title' => $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                            'items' => $items,
                            'total' => $items->sum('amount'),
                        ];
                    })
                    ->sortBy(fn ($card) => $typeOrder[$card['type']] ?? 99)
                    ->values();

                $fundTotal = (float) $monthItems->where('type', 'fund')->sum('amount');
                $loanTotal = (float) $monthItems->where('type', 'loan_payment')->sum('amount');
                $expenseTotal = (float) $monthItems->where('type', 'expense')->sum('amount');

                return [
                    'month_label' => $monthLabel,
                    'month_display' => Carbon::createFromFormat('Y-m', $monthLabel)->format('M y'),
                    'cards' => $cards,
                    'fund_total' => $fundTotal,
                    'loan_total' => $loanTotal,
                    'expense_total' => $expenseTotal,
                    'net' => round($fundTotal - ($loanTotal + $expenseTotal), 2),
                ];
            })
            ->sortBy('month_label')
            ->values();

        return view('em_core::monthly-financial-summaries.index', compact('monthsData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month_label' => 'required|date_format:Y-m',
            'type' => 'required|string|max:50',
            'titles' => 'required|array|min:1',
            'titles.*' => 'required|string|max:255',
            'amounts' => 'required|array|min:1',
            'amounts.*' => 'required|numeric',
            'months_counts' => 'required|array|min:1',
            'months_counts.*' => 'required|integer|min:1',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $baseMonth = Carbon::createFromFormat('Y-m', $request->month_label)->startOfMonth();
        $baseDate = $request->filled('date') ? Carbon::parse($request->date) : null;
        $titles = $request->input('titles', []);
        $amounts = $request->input('amounts', []);
        $monthsCounts = $request->input('months_counts', []);

        DB::transaction(function () use ($baseMonth, $baseDate, $titles, $amounts, $monthsCounts, $request) {
            foreach ($titles as $idx => $title) {
                $amount = $amounts[$idx] ?? null;
                $monthsCount = (int) ($monthsCounts[$idx] ?? 1);

                if ($title === '' || $amount === null || $amount === '' || $monthsCount < 1) {
                    continue;
                }

                for ($i = 0; $i < $monthsCount; $i++) {
                    $monthLabel = $baseMonth->copy()->addMonths($i)->format('Y-m');
                    $entryDate = $baseDate ? $baseDate->copy()->addMonths($i)->format('Y-m-d') : null;

                    MonthlyFinancialSummary::create([
                        'month_label' => $monthLabel,
                        'type' => $request->type,
                        'title' => $title,
                        'amount' => $amount,
                        'date' => $entryDate,
                        'note' => $request->note,
                    ]);
                }
            }
        });

        return redirect()->route('admin.monthly-financial-summaries.index')
            ->with('success', __('Entries added successfully.'));
    }

    public function update(Request $request, $id)
    {
        $entry = MonthlyFinancialSummary::findOrFail($id);

        $request->validate([
            'month_label' => 'required|date_format:Y-m',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);

        $entry->update($request->all());

        return redirect()->route('admin.monthly-financial-summaries.index')
            ->with('success', __('Entry updated successfully.'));
    }

    public function destroy($id)
    {
        MonthlyFinancialSummary::findOrFail($id)->delete();

        return redirect()->route('admin.monthly-financial-summaries.index')
            ->with('success', __('Entry deleted successfully.'));
    }
}

<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Event;
use ME\EmCore\Models\EventExpense;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class EventExpenseController extends Controller
{
    public function index(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $amountType = $request->input('amount_type', 'amount');

        if (!in_array($amountType, ['amount', 'amount_min', 'amount_max'])) {
            $amountType = 'amount';
        }

        $query = $event->expenses()
            ->select('*')
            ->selectRaw("$amountType as show_amount")
            ->orderByDesc('id')
            ->when($request->filled('title'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });

        $expenses = $query->get();

        $filterAmount = $expenses->sum('show_amount');

        return view('em_core::events.expenses.index', compact('event', 'expenses', 'filterAmount', 'amountType'));
    }

    public function store(Request $request, $eventId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $event = Event::findOrFail($eventId);

        EventExpense::create([
            'event_id' => $event->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'amount_min' => $request->amount_min ?? 0,
            'amount_max' => $request->amount_max ?? 0,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.events.expenses.index', [
            $event->id,
            'title' => $request->input('filter_title'),
            'amount_type' => $request->input('filter_amount_type', 'amount'),
        ])->with('success', __('Expense added successfully.'));
    }

    public function update(Request $request, $eventId, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $expense = EventExpense::where('event_id', $eventId)->findOrFail($id);

        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'amount_min' => $request->amount_min ?? 0,
            'amount_max' => $request->amount_max ?? 0,
            'description' => $request->description,
            'created_at' => $request->created_at,
        ]);

        return redirect()->route('admin.events.expenses.index', [
            $eventId,
            'title' => $request->input('filter_title'),
            'amount_type' => $request->input('filter_amount_type', 'amount'),
        ])->with('success', __('Expense updated successfully.'));
    }

    public function destroy($eventId, $id)
    {
        $expense = EventExpense::where('event_id', $eventId)->findOrFail($id);
        $expense->delete();

        return redirect()->route('admin.events.expenses.index', $eventId)
            ->with('success', __('Expense deleted successfully.'));
    }

    public function print(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $amountType = $request->input('amount_type', 'amount');

        if (!in_array($amountType, ['amount', 'amount_min', 'amount_max'])) {
            $amountType = 'amount';
        }

        $query = $event->expenses()
            ->select('*')
            ->selectRaw("$amountType as show_amount")
            ->orderByDesc('id')
            ->when($request->filled('title'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });

        $expense = $query->get();

        $filterAmount = $expense->sum('show_amount');
        return view('em_core::events.expenses.print', compact('expense', 'event'));
    }
}

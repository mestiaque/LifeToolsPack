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
        $query = $event->expenses()
            ->orderByDesc('id')
            ->when($request->filled('title'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->title . '%');
            });

        $expenses = $query->get();
        $totalAmount = $event->totalExpense();
        $filterAmount = $expenses->sum('amount');
        
        return view('em_core::events.expenses.index', compact('event', 'expenses', 'totalAmount', 'filterAmount'));
    }

    public function store(Request $request, $eventId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $event = Event::findOrFail($eventId);
        
        EventExpense::create([
            'event_id' => $event->id,
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.events.expenses.index', $event->id)
            ->with('success', __('Expense added successfully.'));
    }

    public function update(Request $request, $eventId, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $expense = EventExpense::where('event_id', $eventId)->findOrFail($id);
        
        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.events.expenses.index', $eventId)
            ->with('success', __('Expense updated successfully.'));
    }

    public function destroy($eventId, $id)
    {
        $expense = EventExpense::where('event_id', $eventId)->findOrFail($id);
        $expense->delete();

        return redirect()->route('admin.events.expenses.index', $eventId)
            ->with('success', __('Expense deleted successfully.'));
    }
}

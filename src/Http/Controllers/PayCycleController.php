<?php

namespace ME\EmCore\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use ME\EmCore\Models\PayCycleSalary;
use ME\EmCore\Services\PayCycleForecastService;
use ME\Http\Controllers\Controller;

class PayCycleController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:paycycle.show')->only(['index']);
        $this->middleware('authorization:paycycle.create')->only(['store']);
        $this->middleware('authorization:paycycle.edit')->only(['update']);
        $this->middleware('authorization:paycycle.delete')->only(['destroy']);
    }

    public function index(Request $request, PayCycleForecastService $forecastService)
    {
        $cycles = PayCycleSalary::orderByDesc('expected_date')->get();

        $currentCycle = $cycles->first(fn (PayCycleSalary $cycle) => $cycle->expected_date->lte(now()))
            ?? $cycles->first();

        $forecast = $currentCycle ? $forecastService->forecast($currentCycle) : null;

        return view('em_core::paycycle.index', compact('cycles', 'currentCycle', 'forecast'));
    }

    public function store(Request $request, PayCycleForecastService $forecastService)
    {
        $request->validate([
            'month_label' => 'required|string|max:20',
            'salary_amount' => 'required|numeric|min:0',
            'expected_date' => 'required|date',
            'received_date' => 'nullable|date',
            'expected_expense' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        PayCycleSalary::create($request->all());

        return redirect()
            ->route('admin.paycycle.index')
            ->with('success', __('Pay cycle added successfully.'));
    }

    public function update(Request $request, $id)
    {
        $cycle = PayCycleSalary::findOrFail($id);

        $request->validate([
            'month_label' => 'required|string|max:20',
            'salary_amount' => 'required|numeric|min:0',
            'expected_date' => 'required|date',
            'received_date' => 'nullable|date',
            'expected_expense' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $cycle->update($request->all());

        return redirect()
            ->route('admin.paycycle.index')
            ->with('success', __('Pay cycle updated successfully.'));
    }

    public function destroy($id)
    {
        PayCycleSalary::findOrFail($id)->delete();

        return redirect()
            ->route('admin.paycycle.index')
            ->with('success', __('Pay cycle deleted successfully.'));
    }
}

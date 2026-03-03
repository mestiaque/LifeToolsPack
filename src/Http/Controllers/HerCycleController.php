<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\HerCycleNotification;
use ME\EmCore\Models\HerCyclePeriod;
use ME\EmCore\Models\HerCyclePrediction;
use ME\EmCore\Models\HerCycleProfile;
use ME\EmCore\Models\HerCycleSymptom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ME\Http\Controllers\Controller;

class HerCycleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return redirect()->route('admin.hercycle.setup');
        }

        $periods = HerCyclePeriod::where('profile_id', $profile->id)
            ->orderBy('start_date', 'desc')
            ->limit(12)
            ->get();

        $symptoms = HerCycleSymptom::where('profile_id', $profile->id)
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Get predictions
        $nextPeriod = $profile->predictNextPeriod();
        $fertileWindow = $profile->predictFertileWindow();
        $pmsPeriod = $profile->predictPMS();

        // Calendar data
        $currentMonth = Carbon::now();
        $calendarData = $this->getCalendarData($profile, $currentMonth);

        // Statistics
        $stats = $this->getStatistics($profile, $periods);

        return view('em_core::hercycle.index', compact(
            'profile',
            'periods',
            'symptoms',
            'nextPeriod',
            'fertileWindow',
            'pmsPeriod',
            'calendarData',
            'stats',
            'currentMonth'
        ));
    }

    public function setup()
    {
        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        if ($profile) {
            return redirect()->route('admin.hercycle.index');
        }

        return view('em_core::hercycle.setup');
    }

    public function storeProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'cycle_length' => 'required|integer|min:20|max:45',
            'period_length' => 'required|integer|min:2|max:10',
        ]);

        $user = Auth::user();

        $profile = HerCycleProfile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'age' => $request->age,
            'cycle_length' => $request->cycle_length,
            'period_length' => $request->period_length,
        ]);

        // Create default notification settings
        HerCycleNotification::create([
            'profile_id' => $profile->id,
        ]);

        return redirect()->route('admin.hercycle.index')->with('success', 'Profile created successfully!');
    }

    public function updateProfile(Request $request, $id)
    {
        $profile = HerCycleProfile::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:10|max:100',
            'cycle_length' => 'required|integer|min:20|max:45',
            'period_length' => 'required|integer|min:2|max:10',
            'last_period_start' => 'nullable|date',
        ]);

        $profile->update($request->all());

        return redirect()->route('admin.hercycle.index')->with('success', 'Profile updated successfully!');
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'flow_intensity' => 'nullable|in:light,medium,heavy',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        $period = HerCyclePeriod::create([
            'profile_id' => $profile->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'flow_intensity' => $request->flow_intensity,
            'notes' => $request->notes,
        ]);

        // Update profile last period start
        $profile->update(['last_period_start' => $request->start_date]);

        // Recalculate predictions
        $this->updatePredictions($profile);

        return redirect()->route('hercycle.index')->with('success', 'Period recorded successfully!');
    }

    public function updatePeriod(Request $request, $id)
    {
        $period = HerCyclePeriod::findOrFail($id);

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'flow_intensity' => 'nullable|in:light,medium,heavy',
            'notes' => 'nullable|string',
        ]);

        $period->update($request->all());

        return redirect()->route('hercycle.index')->with('success', 'Period updated successfully!');
    }

    public function deletePeriod($id)
    {
        $period = HerCyclePeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('hercycle.index')->with('success', 'Period deleted successfully!');
    }

    public function storeSymptom(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'physical_symptoms' => 'nullable|array',
            'emotional_symptoms' => 'nullable|array',
            'sleep_quality' => 'nullable|integer|min:1|max:10',
            'energy_level' => 'nullable|integer|min:1|max:10',
            'custom_symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        HerCycleSymptom::updateOrCreate(
            [
                'profile_id' => $profile->id,
                'date' => $request->date,
            ],
            [
                'physical_symptoms' => $request->physical_symptoms,
                'emotional_symptoms' => $request->emotional_symptoms,
                'sleep_quality' => $request->sleep_quality,
                'energy_level' => $request->energy_level,
                'custom_symptoms' => $request->custom_symptoms,
                'notes' => $request->notes,
            ]
        );

        return redirect()->route('hercycle.index')->with('success', 'Symptoms logged successfully!');
    }

    public function updateNotifications(Request $request, $id)
    {
        $profile = HerCycleProfile::findOrFail($id);
        $notification = HerCycleNotification::where('profile_id', $profile->id)->first();

        $request->validate([
            'period_reminder' => 'boolean',
            'period_reminder_days' => 'integer|min:1|max:7',
            'pms_reminder' => 'boolean',
            'pms_reminder_days' => 'integer|min:1|max:14',
            'fertile_reminder' => 'boolean',
            'symptom_reminder' => 'boolean',
            'reminder_time' => 'date_format:H:i',
        ]);

        $notification->update($request->all());

        return redirect()->route('hercycle.index')->with('success', 'Notification settings updated!');
    }

    public function getMonthData(Request $request)
    {
        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $month = Carbon::parse($request->month . '-01');
        $calendarData = $this->getCalendarData($profile, $month);

        return response()->json($calendarData);
    }

    private function getCalendarData($profile, Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // Get periods in range
        $periods = HerCyclePeriod::where('profile_id', $profile->id)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfMonth);
                    });
            })
            ->get();

        // Get symptoms in range
        $symptoms = HerCycleSymptom::where('profile_id', $profile->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy('date');

        // Predictions
        $nextPeriod = $profile->predictNextPeriod();
        $fertileWindow = $profile->predictFertileWindow();
        $pmsPeriod = $profile->predictPMS();

        // Build calendar days
        $days = [];
        $startDay = $startOfMonth->dayOfWeek;

        // Previous month padding
        $prevMonth = $month->copy()->subMonth();
        for ($i = $startDay - 1; $i >= 0; $i--) {
            $date = $prevMonth->copy()->endOfMonth()->subDays($i);
            $days[] = $this->buildDayData($date, $periods, $symptoms, $nextPeriod, $fertileWindow, $pmsPeriod);
        }

        // Current month
        for ($day = 1; $day <= $month->daysInMonth; $day++) {
            $date = $month->copy()->day($day);
            $days[] = $this->buildDayData($date, $periods, $symptoms, $nextPeriod, $fertileWindow, $pmsPeriod);
        }

        // Next month padding
        $remainingDays = 42 - count($days);
        $nextMonth = $month->copy()->addMonth();
        for ($day = 1; $day <= $remainingDays; $day++) {
            $date = $nextMonth->copy()->day($day);
            $days[] = $this->buildDayData($date, $periods, $symptoms, $nextPeriod, $fertileWindow, $pmsPeriod);
        }

        return [
            'month' => $month->format('Y-m'),
            'monthName' => $month->format('F Y'),
            'days' => $days,
        ];
    }

    private function buildDayData($date, $periods, $symptoms, $nextPeriod, $fertileWindow, $pmsPeriod): array
    {
        $dateStr = $date->format('Y-m-d');
        $isCurrentMonth = $date->month === Carbon::now()->month;

        // Check if period day
        $isPeriod = false;
        $flowIntensity = null;
        foreach ($periods as $period) {
            if ($date->between($period->start_date, $period->end_date ?? $period->start_date)) {
                $isPeriod = true;
                $flowIntensity = $period->flow_intensity;
                break;
            }
        }

        // Check if fertile window
        $isFertile = false;
        if ($fertileWindow && $date->between($fertileWindow['start'], $fertileWindow['end'])) {
            $isFertile = true;
        }

        // Check if PMS
        $isPMS = false;
        if ($pmsPeriod && $date->between($pmsPeriod['start'], $pmsPeriod['end'])) {
            $isPMS = true;
        }

        // Check if predicted period
        $isPredictedPeriod = false;
        if ($nextPeriod && $date->between($nextPeriod['start'], $nextPeriod['end'])) {
            $isPredictedPeriod = true;
        }

        // Check if ovulation
        $isOvulation = false;
        if ($fertileWindow && $date->format('Y-m-d') === $fertileWindow['ovulation']->format('Y-m-d')) {
            $isOvulation = true;
        }

        // Check symptoms
        $daySymptoms = $symptoms[$dateStr] ?? null;

        return [
            'date' => $dateStr,
            'day' => $date->day,
            'isCurrentMonth' => $isCurrentMonth,
            'isToday' => $date->isToday(),
            'isPeriod' => $isPeriod,
            'flowIntensity' => $flowIntensity,
            'isFertile' => $isFertile,
            'isPMS' => $isPMS,
            'isPredictedPeriod' => $isPredictedPeriod,
            'isOvulation' => $isOvulation,
            'hasSymptoms' => $daySymptoms !== null,
            'symptoms' => $daySymptoms,
        ];
    }

    private function getStatistics($profile, $periods): array
    {
        if ($periods->count() < 2) {
            return [
                'averageCycleLength' => $profile->cycle_length,
                'averagePeriodLength' => $profile->period_length,
                'totalCycles' => $periods->count(),
                'isRegular' => null,
            ];
        }

        $cycleLengths = [];
        $periodLengths = [];

        for ($i = 0; $i < $periods->count() - 1; $i++) {
            $cycleLength = $periods[$i]->start_date->diffInDays($periods[$i + 1]->start_date);
            $cycleLengths[] = $cycleLength;

            if ($periods[$i]->end_date) {
                $periodLength = $periods[$i]->start_date->diffInDays($periods[$i]->end_date) + 1;
                $periodLengths[] = $periodLength;
            }
        }

        $averageCycleLength = count($cycleLengths) > 0
            ? round(array_sum($cycleLengths) / count($cycleLengths))
            : $profile->cycle_length;

        $averagePeriodLength = count($periodLengths) > 0
            ? round(array_sum($periodLengths) / count($periodLengths))
            : $profile->period_length;

        // Check regularity (within 3 days variance)
        $isRegular = true;
        if (count($cycleLengths) > 1) {
            $variance = max($cycleLengths) - min($cycleLengths);
            $isRegular = $variance <= 3;
        }

        return [
            'averageCycleLength' => $averageCycleLength,
            'averagePeriodLength' => $averagePeriodLength,
            'totalCycles' => $periods->count(),
            'isRegular' => $isRegular,
            'cycleLengths' => $cycleLengths,
        ];
    }

    private function updatePredictions($profile)
    {
        // Deactivate old predictions
        HerCyclePrediction::where('profile_id', $profile->id)
            ->update(['is_active' => false]);

        $nextPeriod = $profile->predictNextPeriod();
        $fertileWindow = $profile->predictFertileWindow();
        $pmsPeriod = $profile->predictPMS();

        if ($nextPeriod) {
            HerCyclePrediction::create([
                'profile_id' => $profile->id,
                'predicted_period_start' => $nextPeriod['start'],
                'predicted_ovulation' => $fertileWindow['ovulation'] ?? null,
                'fertile_window_start' => $fertileWindow['start'] ?? null,
                'fertile_window_end' => $fertileWindow['end'] ?? null,
                'pms_start' => $pmsPeriod['start'] ?? null,
                'is_active' => true,
            ]);
        }
    }
}

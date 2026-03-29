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
        // return view('em_core::hercycle.setup');
        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            return redirect()->route('admin.hercycle.setup');
        }
        $periods = HerCyclePeriod::where('profile_id', $profile->id)
            ->orderBy('start_date', 'desc')
            ->get();

        // Calculate average cycle and period length from logs
        $cycleLengths = [];
        $periodLengths = [];
        $cycleTrendData = [];
        $periodsArr = $periods->sortBy('start_date')->values();
        $oneYearAgo = Carbon::now()->subYear()->startOfDay();
        $cycleTrendDataRaw = [];

        if ($periodsArr->count() === 1) {
            // Only one log exists
            $first = $periodsArr[0];
            $avgPeriod = $first->end_date ? $first->start_date->diffInDays($first->end_date) + 1 : 1;
            $avgCycle = 30;
            // Next period: 30 days after end if exists, else after start
            $nextStart = $first->end_date
                ? $first->end_date->copy()->addDays(30)
                : $first->start_date->copy()->addDays(30);
            $nextEnd = null;
        } else if ($periodsArr->count() > 1) {
            for ($i = 0; $i < $periodsArr->count() - 1; $i++) {
                $currentCycleLength = abs($periodsArr[$i+1]->start_date->diffInDays($periodsArr[$i]->start_date));
                $cycleLengths[] = $currentCycleLength;

                // Keep last 1 year cycle points for detailed charting.
                if ($periodsArr[$i+1]->start_date->greaterThanOrEqualTo($oneYearAgo)) {
                    $cycleTrendDataRaw[] = [
                        'label' => $periodsArr[$i+1]->start_date->format('M d'),
                        'length' => $currentCycleLength,
                        'from' => $periodsArr[$i]->start_date->format('Y-m-d'),
                        'to' => $periodsArr[$i+1]->start_date->format('Y-m-d'),
                    ];
                }
            }
            foreach ($periods as $period) {
                if ($period->end_date) {
                    $periodLengths[] = $period->start_date->diffInDays($period->end_date) + 1;
                }
            }
            $avgCycle = count($cycleLengths) ? round(array_sum($cycleLengths)/count($cycleLengths)) : null;
            $avgPeriod = count($periodLengths) ? round(array_sum($periodLengths)/count($periodLengths)) : null;

            // Add current ongoing cycle as an extra point (from latest start date to today).
            $latestPeriod = $periods->first();
            $today = Carbon::now()->startOfDay();
            if (
                $latestPeriod &&
                !$latestPeriod->end_date &&
                $latestPeriod->start_date->greaterThanOrEqualTo($oneYearAgo) &&
                $latestPeriod->start_date->lessThanOrEqualTo($today)
            ) {
                $ongoingLength = $latestPeriod->start_date->diffInDays($today);
                if ($ongoingLength > 0) {
                    $cycleTrendDataRaw[] = [
                        'label' => $today->format('M d') . ' (Now)',
                        'length' => $ongoingLength,
                        'from' => $latestPeriod->start_date->format('Y-m-d'),
                        'to' => $today->format('Y-m-d'),
                        'is_ongoing' => true,
                    ];
                }
            }

            foreach ($cycleTrendDataRaw as $item) {
                $deviation = $avgCycle ? abs($item['length'] - $avgCycle) : 0;
                if ($deviation <= 2) {
                    $item['flow'] = 'Good';
                } elseif ($deviation <= 5) {
                    $item['flow'] = 'Neutral';
                } else {
                    $item['flow'] = 'Bad';
                }
                $cycleTrendData[] = $item;
            }

            // Predict next period
            $lastPeriod = $periods->first();
            $nextStart = $lastPeriod && $avgCycle ? $lastPeriod->start_date->copy()->addDays($avgCycle) : null;
            $nextEnd = $nextStart && $avgPeriod ? $nextStart->copy()->addDays($avgPeriod-1) : null;
        } else {
            $avgCycle = null;
            $avgPeriod = null;
            $nextStart = null;
            $nextEnd = null;
        }

        // Predict flow quality
        $flowPrediction = null;
        if (count($cycleLengths) > 1) {
            $variance = max($cycleLengths) - min($cycleLengths);
            if ($variance <= 3) $flowPrediction = 'Good';
            elseif ($variance <= 7) $flowPrediction = 'Neutral';
            else $flowPrediction = 'Bad';
        } else {
            $flowPrediction = 'Neutral';
        }

        // Calculate age from dob
        $age = $profile->dob ? Carbon::parse($profile->dob)->age : null;

        return view('em_core::hercycle.index', [
            'profile' => $profile,
            'periods' => $periods,
            'avgCycle' => $avgCycle,
            'avgPeriod' => $avgPeriod,
            'nextStart' => $nextStart,
            'nextEnd' => $nextEnd,
            'flowPrediction' => $flowPrediction,
            'age' => $age,
            'cycleLengths' => $cycleLengths,
            'cycleTrendData' => $cycleTrendData,
        ]);
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
            'dob' => 'required|date',
            'weight' => 'required|numeric|min:20|max:200',
            'height' => 'required|numeric|min:100|max:250',
            'blood_group' => 'required|string|max:5',
        ]);
        $user = Auth::user();
        $profile = HerCycleProfile::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'dob' => $request->dob,
            'weight' => $request->weight,
            'height' => $request->height,
            'blood_group' => $request->blood_group,
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
            'dob' => 'required|date',
            'weight' => 'required|numeric|min:20|max:200',
            'height' => 'required|numeric|min:100|max:250',
            'blood_group' => 'required|string|max:5',
        ]);
        $profile->update([
            'name' => $request->name,
            'dob' => $request->dob,
            'weight' => $request->weight,
            'height' => $request->height,
            'blood_group' => $request->blood_group,
        ]);
        return redirect()->route('admin.hercycle.index')->with('success', 'Profile updated successfully!');
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
            ],
        ]);
        $user = Auth::user();
        $profile = HerCycleProfile::where('user_id', $user->id)->first();

        // If the same start date already exists, treat this as an update attempt from the log form.
        $existingPeriod = HerCyclePeriod::where('profile_id', $profile->id)
            ->whereDate('start_date', $request->start_date)
            ->first();

        if ($existingPeriod) {
            if (!$request->end_date) {
                return redirect()->route('admin.hercycle.index')->with('error', 'You already logged a period for this start date.');
            }

            $endDateConflict = HerCyclePeriod::where('profile_id', $profile->id)
                ->where('id', '!=', $existingPeriod->id)
                ->where(function ($q) use ($request) {
                    $q->whereDate('start_date', $request->end_date)
                        ->orWhereDate('end_date', $request->end_date);
                })
                ->exists();

            if ($endDateConflict) {
                return redirect()->route('admin.hercycle.index')->with('error', 'You already logged a period for this date.');
            }

            $existingPeriod->update([
                'end_date' => $request->end_date,
            ]);

            return redirect()->route('admin.hercycle.index')->with('success', 'Period updated successfully!');
        }

        // Prevent duplicate start or end date for this profile
        $exists = HerCyclePeriod::where('profile_id', $profile->id)
            ->where(function($q) use ($request) {
                $q->whereDate('start_date', $request->start_date)
                  ->orWhereDate('end_date', $request->start_date);
                if ($request->end_date) {
                    $q->orWhereDate('start_date', $request->end_date)
                      ->orWhereDate('end_date', $request->end_date);
                }
            })
            ->exists();
        if ($exists) {
            return redirect()->route('admin.hercycle.index')->with('error', 'You already logged a period for this date.');
        }
        HerCyclePeriod::create([
            'profile_id' => $profile->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return redirect()->route('admin.hercycle.index')->with('success', 'Period recorded successfully!');
    }

    public function updatePeriod(Request $request, $id)
    {
        $period = HerCyclePeriod::findOrFail($id);
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
            ],
        ]);
        // Only check uniqueness for dates that are actually being changed.
        $currentStartDate = $period->start_date ? $period->start_date->format('Y-m-d') : null;
        $currentEndDate = $period->end_date ? $period->end_date->format('Y-m-d') : null;

        $conflictDates = [];
        if ($request->start_date !== $currentStartDate) {
            $conflictDates[] = $request->start_date;
        }
        if ($request->end_date && $request->end_date !== $currentEndDate) {
            $conflictDates[] = $request->end_date;
        }

        $conflictDates = array_values(array_unique($conflictDates));
        $exists = false;
        if (count($conflictDates) > 0) {
            $profileId = $period->profile_id;
            $exists = HerCyclePeriod::where('profile_id', $profileId)
                ->where('id', '!=', $period->id)
                ->where(function($q) use ($conflictDates) {
                    foreach ($conflictDates as $index => $date) {
                        if ($index === 0) {
                            $q->whereDate('start_date', $date)
                                ->orWhereDate('end_date', $date);
                            continue;
                        }

                        $q->orWhereDate('start_date', $date)
                            ->orWhereDate('end_date', $date);
                    }
                })
                ->exists();
        }

        if ($exists) {
            return redirect()->route('admin.hercycle.index')->with('error', 'You already logged a period for this date.');
        }
        $period->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return redirect()->route('admin.hercycle.index')->with('success', 'Period updated successfully!');
    }

    public function deletePeriod($id)
    {
        $period = HerCyclePeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('admin.hercycle.index')->with('success', 'Period deleted successfully!');
    }

    // Symptom logging removed for simplicity

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

        return redirect()->route('admin.hercycle.index')->with('success', 'Notification settings updated!');
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
            $cycleLength = abs($periods[$i]->start_date->diffInDays($periods[$i + 1]->start_date));
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

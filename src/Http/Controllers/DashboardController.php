<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Event;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index(){
        // Loan: total payable and receivable (active users, unsettled loans only)
        $loans = \ME\EmCore\Models\Loan::with('loanUser', 'repayments')
            ->whereHas('loanUser', fn($q) => $q->where('is_active', true))
            ->get()
            ->filter(fn($loan) => $loan->dueAmount() > 0);

        $totalPayable    = $loans->where('type', 'given')->sum(fn($loan) => $loan->dueAmount());
        $totalReceivable = $loans->where('type', 'taken')->sum(fn($loan) => $loan->dueAmount());

        // Drive: total folders, subfolders, files
        $totalFolders = \ME\EmCore\Models\Folder::count();
        $totalFiles = \ME\EmCore\Models\Document::count();

        // Disk: total disks and storage
        $totalDisks = \ME\EmCore\Models\Disk::count();
        $totalDiskCapacity = \ME\EmCore\Models\Disk::sum('capacity');
        $totalDiskUsed = \ME\EmCore\Models\Disk::sum('used');

        // Gallery: total images
        $totalGalleryImages = \ME\EmCore\Models\Gallery::count();

        // Message: total messages
        $totalMessages = \ME\EmCore\Models\Message::count();

        // Event: running events with expenses
        $today = now()->toDateString();
        $runningEvents = \ME\EmCore\Models\Event::whereDate('start', '<=', $today)->where(function($q) use ($today) {
            $q->whereNull('end')->orWhereDate('end', '>=', $today);
        })->get();
        $runningEventsWithExpenses = $runningEvents->map(function($event) {
            return [
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'expense' => $event->totalExpense(),
            ];
        });

        // HerCycle: last period start date and predicted next start date
        $herCycleProfile = \ME\EmCore\Models\HerCycleProfile::first();
        $lastPeriodStart = null;
        $predictedNextStart = null;
        if ($herCycleProfile) {
            $lastPeriod = $herCycleProfile->periods()->orderByDesc('start_date')->first();
            $lastPeriodStart = $lastPeriod ? $lastPeriod->start_date : null;
            $predictedNextStart = $herCycleProfile->getNextPeriodStart();
        }

        // Project: static array count (from _front/index.blade.php)
        $projects = [
            [ 'title' => 'Stock Management System' ],
            // Add more if needed
        ];
        $totalProjects = count($projects);

        return view('em_core::dashboard', compact(
            'totalPayable', 'totalReceivable',
            'totalFolders', 'totalFiles',
            'totalDisks', 'totalDiskCapacity', 'totalDiskUsed',
            'totalGalleryImages', 'totalMessages',
            'runningEventsWithExpenses',
            'lastPeriodStart', 'predictedNextStart',
            'totalProjects'
        ));
    }

    public function calendarEvents()
    {
        return Event::all()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'allDay' => (bool)$event->all_day,
            ];
        });
    }

    public function calendarStore(Request $request)
    {
        $event = Event::create([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'all_day' => $request->allDay ?? false,
        ]);
        return [
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start,
            'end' => $event->end,
            'allDay' => (bool)$event->all_day,
        ];
    }

    public function calendarUpdate(Request $request)
    {
        $event = Event::findOrFail($request->id);
        $event->update([
            'title' => $request->title ?? $event->title,
            'start' => $request->start ?? $event->start,
            'end' => $request->end ?? $event->end,
            'all_day' => $request->allDay ?? $event->all_day,
        ]);
        return response()->json(['success' => true]);
    }

    public function calendarDelete(Request $request)
    {
        $event = Event::findOrFail($request->id);
        $event->delete();
        return response()->json(['success' => true]);
    }

}

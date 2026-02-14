<?php

namespace EmCore\Http\Controllers;

use EmCore\Models\Event;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class DashboardController extends Controller
{

    public function index(){
        return view('em_core::dashboard');
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

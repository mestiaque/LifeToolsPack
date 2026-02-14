<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Event;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query();
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('start')) {
            $query->whereDate('start', $request->start);
        }
        if ($request->filled('end')) {
            $query->whereDate('end', $request->end);
        }
        $events = $query->orderByDesc('id')->get();
        return view('em_core::events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'all_day' => 'nullable|boolean',
        ]);
        $event = Event::create([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'all_day' => $request->all_day ?? false,
        ]);
        return redirect()->route('admin.events.index')->with('success', __('Event created successfully.'));
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date',
            'all_day' => 'nullable|boolean',
        ]);
        $event->update([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'all_day' => $request->all_day ?? false,
        ]);
        return redirect()->route('admin.events.index')->with('success', __('Event updated successfully.'));
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', __('Event deleted successfully.'));
    }
}

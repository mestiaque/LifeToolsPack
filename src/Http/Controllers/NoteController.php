<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Note;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index()
    {
        return view('em_core::notes.index');
    }
    // List all notes (optionally filtered by search & color)
    public function getNotes(Request $request)
    {
        $q = Note::query();
        if ($search = $request->get('search')) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        if ($color = $request->get('color')) {
            $q->where('color', $color);
        }
        $notes = $q->orderByDesc('is_pinned')
                   ->orderByDesc('updated_at')
                   ->get();
        return response()->json($notes);
    }

    // Store new note
    public function store(Request $request)
    {
        $note = Note::create([
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'color' => $request->input('color', '#ffffff'),
            'is_pinned' => $request->input('is_pinned', false),
        ]);
        return response()->json($note);
    }

    // Update note
    public function update(Request $request, Note $note)
    {
        $note->update([
            'title' => $request->input('title'),
            'description' => $request->input('description', ''),
            'color' => $request->input('color', $note->color),
            'is_pinned' => $request->input('is_pinned', false),
        ]);
        return response()->json($note);
    }

    // Delete note
    public function destroy(Note $note)
    {
        $note->delete();
        return response()->json(['success' => true]);
    }
}

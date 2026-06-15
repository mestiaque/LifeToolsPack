<?php

namespace ME\EmCore\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use ME\EmCore\Models\MemorableDay;
use ME\Http\Controllers\Controller;

class MemorableDayController extends Controller
{
    public function __construct()
    {
        $this->middleware('authorization:memorable_day.show')->only(['index', 'list']);
        $this->middleware('authorization:memorable_day.create')->only(['store']);
        $this->middleware('authorization:memorable_day.edit')->only(['update']);
        $this->middleware('authorization:memorable_day.delete')->only(['destroy']);
    }

    public function index()
    {
        return view('em_core::memorable-days.index');
    }

    public function list(Request $request)
    {
        $q = MemorableDay::query();

        if ($search = $request->get('search')) {
            $q->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            $q->where('category', $category);
        }

        $days = $q->orderBy('event_date')->get();

        return response()->json($days);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'image'      => 'nullable|image|max:4096',
        ]);

        $data = $this->extractData($request);

        if ($request->hasFile('image')) {
            $data['image_url'] = $this->storeImage($request->file('image'));
        }

        $day = MemorableDay::create($data);

        return response()->json($day);
    }

    public function update(Request $request, MemorableDay $memorableDay)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'event_date' => 'required|date',
            'image'      => 'nullable|image|max:4096',
        ]);

        $data = $this->extractData($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($memorableDay->image_url);
            $data['image_url'] = $this->storeImage($request->file('image'));
        } elseif ($request->input('remove_image') === '1') {
            $this->deleteImage($memorableDay->image_url);
            $data['image_url'] = null;
        }

        $memorableDay->update($data);

        return response()->json($memorableDay->fresh());
    }

    public function destroy(MemorableDay $memorableDay)
    {
        $this->deleteImage($memorableDay->image_url);
        $memorableDay->delete();

        return response()->json(['success' => true]);
    }

    private function extractData(Request $request): array
    {
        $tags = $request->input('tags');
        if (is_string($tags) && str_starts_with($tags, '[')) {
            $tags = json_decode($tags, true);
        } elseif (is_string($tags) && strlen($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        } else {
            $tags = null;
        }

        return [
            'title'                => $request->input('title'),
            'description'          => $request->input('description'),
            'event_date'           => $request->input('event_date'),
            'category'             => $request->input('category'),
            'location'             => $request->input('location'),
            'color'                => $request->input('color'),
            'is_private'           => $request->boolean('is_private'),
            'reminder_enabled'     => $request->boolean('reminder_enabled'),
            'reminder_days_before' => $request->input('reminder_days_before') ?: null,
            'importance_level'     => $request->input('importance_level') ?: null,
            'repeat_yearly'        => $request->boolean('repeat_yearly'),
            'tags'                 => $tags ?: null,
            'user_id'              => auth()->id(),
        ];
    }

    private function storeImage($file): string
    {
        $filename = 'mem-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('memorable-days', $filename, 'public');
        return 'memorable-days/' . $filename;
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}

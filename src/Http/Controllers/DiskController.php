<?php

namespace EmCore\Http\Controllers;

use EmCore\Models\Disk;
use ME\Models\Setting;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class DiskController extends Controller
{
    public function __construct()
    {
        // Permission middleware (MessageController style)
        $this->middleware('authorization:disk.show')->only(['index']);
        $this->middleware('authorization:disk.edit')->only(['update']);
        $this->middleware('authorization:disk.delete')->only(['destroy']);
        $this->middleware('authorization:disk.create')->only(['store']);
    }

    /**
     * Display a listing of the disks.
     */
    public function index(Request $request)
    {
        $query = Disk::query();

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }
        if ($request->filled('tag')) {
            $query->where('tag', 'like', '%' . $request->tag . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // $disks = $query->orderByDesc('id')->paginate(get_setting('pagination', 10));
        $disks = $query->orderByDesc('code')->get();
        $settings = Setting::latest()->first();

        return view('em_core::disks.index', compact('disks', 'settings'));
    }

    /**
     * Store a newly created disk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:disks,code',
            'tag' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric',
            'used' => 'nullable|numeric',
            'description' => 'nullable|string',
            'content' => 'nullable',
            'status' => 'nullable|string|max:255',
        ]);

        Disk::create($request->all());
        return redirect()->route('admin.disks.index')->with('success', __('Disk created successfully.'));
    }


    /**
     * Update disk
     */
    public function update(Request $request, $id)
    {
        $disk = Disk::findOrFail($id);

        $request->validate([
            'code' => 'required|string|unique:disks,code,' . $disk->id,
            'tag' => 'nullable|string|max:255',
            'capacity' => 'nullable|numeric',
            'used' => 'nullable|numeric',
            'description' => 'nullable|string',
            'content' => 'nullable',
            'status' => 'nullable|string|max:255',
        ]);

        $disk->update($request->all());

        return redirect()->route('admin.disks.index')->with('success', __('Disk created successfully.'));;
    }

    /**
     * Delete disk
     */
    public function destroy($id)
    {
        $disk = Disk::findOrFail($id);
        $disk->delete();

        return redirect()->route('admin.disks.index')->with('success', __('Disk deleted successfully.'));;
    }


}

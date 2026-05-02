<?php

namespace ME\EmCore\Http\Controllers;

use ME\EmCore\Models\Link;
use Illuminate\Http\Request;
use ME\Http\Controllers\Controller;
use App\Http\Middleware\AuthorizationMiddleware;

class LinkController extends Controller
{
    public function __construct()
    {
        // Permission middleware (MessageController style)
        $this->middleware('authorization:link.view')->only(['index']);
        $this->middleware('authorization:link.edit')->only(['update']);
        $this->middleware('authorization:link.delete')->only(['destroy']);
        $this->middleware('authorization:link.create')->only(['store']);
    }

    /**
     * Display a listing of the links.
     */
    public function index(Request $request)
    {
        $query = Link::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('link')) {
            $query->where('link', 'like', '%' . $request->link . '%');
        }
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        // $links = $query->orderByDesc('id')->paginate(get_setting('pagination', 10));
        $links = $query->orderByDesc('id')->get();

        return view('em_core::links.index', compact('links'));
    }

    /**
     * Store a newly created link.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:500',
            'description' => 'nullable|string',
        ]);

        Link::create($request->all());
        return redirect()->route('admin.links.index')->with('success', __('Link created successfully.'));
    }


    /**
     * Update link
     */
    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|string|max:500',
            'description' => 'nullable|string',
        ]);

        $link->update($request->all());

        return redirect()->route('admin.links.index')->with('success', __('Link updated successfully.'));
    }

    /**
     * Delete link
     */
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $link->delete();

        return redirect()->route('admin.links.index')->with('success', __('Link deleted successfully.'));
    }


}

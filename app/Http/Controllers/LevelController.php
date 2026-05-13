<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $levels = Level::orderBy('name')->paginate(20);
        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $availableLevels = Level::getAllLevels();
        return view('admin.levels.create', compact('availableLevels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:levels|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        Level::create($validated);

        return redirect()->route('admin.levels.index')->with('success', 'Level created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.levels.show', compact('level'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:levels,name,' . $level->id . '|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        $level->update($validated);

        return redirect()->route('admin.levels.index')->with('success', 'Level updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $level->delete();
        return redirect()->route('admin.levels.index')->with('success', 'Level deleted successfully');
    }

    public function details(Level $level)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'level' => [
                'id' => $level->id,
                'name' => $level->name,
                'description' => $level->description,
                'status' => $level->status,
                'created_at' => $level->created_at->format('M d, Y H:i'),
                'updated_at' => $level->updated_at->format('M d, Y H:i'),
            ]
        ]);
    }
}

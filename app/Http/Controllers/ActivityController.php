<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $activities = Activity::where('student_id', $student->id)
            ->orderBy('activity_date', 'desc')
            ->paginate(10);
        
        return view('activities.index', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'duration_hours' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        Activity::create([
            'student_id' => $student->id,
            'activity_name' => $validated['name'],
            'description' => $validated['description'],
            'activity_date' => $validated['date'],
            'category' => $validated['category'],
            'duration_hours' => $validated['duration_hours'],
            'status' => 'pending',
        ]);

        return redirect('/activities')->with('success', 'Activity created successfully!');
    }

    public function show(Activity $activity)
    {
        $student = Auth::user()->student;
        if ($activity->student_id !== $student->id) {
            abort(403);
        }
        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        $student = Auth::user()->student;
        if ($activity->student_id !== $student->id) {
            abort(403);
        }
        return view('activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $student = Auth::user()->student;
        if ($activity->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'date' => ['required', 'date'],
            'category' => ['required', 'string', 'max:100'],
            'duration_hours' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        $activity->update([
            'activity_name' => $validated['name'],
            'description' => $validated['description'],
            'activity_date' => $validated['date'],
            'category' => $validated['category'],
            'duration_hours' => $validated['duration_hours'],
        ]);

        return redirect('/activities')->with('success', 'Activity updated successfully!');
    }

    public function destroy(Activity $activity)
    {
        $student = Auth::user()->student;
        if ($activity->student_id !== $student->id) {
            abort(403);
        }

        $activity->delete();
        return redirect('/activities')->with('success', 'Activity deleted successfully!');
    }

    public function complete(Activity $activity)
    {
        $student = Auth::user()->student;
        if ($activity->student_id !== $student->id) {
            abort(403);
        }

        $activity->update(['status' => 'completed']);
        return redirect('/activities')->with('success', 'Activity marked as completed!');
    }
}

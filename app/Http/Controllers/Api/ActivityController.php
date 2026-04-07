<?php

namespace App\Http\Controllers\Api;

use App\Models\Activity;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ActivityController extends \Illuminate\Routing\Controller
{
    /**
     * Get all activities for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Activity::query();

        // Filter by student if the user is a student
        if ($user->role_id == 2) { // Student role
            $student = $user->student;
            $query->where('student_id', $student->id);
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $activities = $query->with('student.user', 'assignedBy.user')
            ->orderBy('activity_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($activities);
    }

    /**
     * Store a new activity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'activity_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'category' => 'required|string',
            'deliverables' => 'nullable|string',
        ]);

        $activity = Activity::create([
            ...$request->validated(),
            'assigned_by' => auth()->user()->instructor?->id,
        ]);

        return response()->json([
            'message' => 'Activity created successfully',
            'data' => $activity,
        ], 201);
    }

    /**
     * Get a specific activity.
     */
    public function show(Activity $activity)
    {
        return response()->json($activity->load('student.user', 'assignedBy.user'));
    }

    /**
     * Update an activity.
     */
    public function update(Request $request, Activity $activity)
    {
        $request->validate([
            'activity_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'activity_date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i:s',
            'end_time' => 'nullable|date_format:H:i:s',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'category' => 'nullable|string',
            'status' => 'nullable|in:pending,in_progress,completed',
            'deliverables' => 'nullable|string',
        ]);

        $activity->update($request->validated());

        return response()->json([
            'message' => 'Activity updated successfully',
            'data' => $activity,
        ]);
    }

    /**
     * Delete an activity.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return response()->json([
            'message' => 'Activity deleted successfully',
        ]);
    }

    /**
     * Mark an activity as completed.
     */
    public function complete(Request $request, Activity $activity)
    {
        $request->validate([
            'deliverables' => 'nullable|string',
        ]);

        $activity->update([
            'status' => 'completed',
            'deliverables' => $request->deliverables ?? $activity->deliverables,
        ]);

        // TODO: Send notification to instructor

        return response()->json([
            'message' => 'Activity marked as completed',
            'data' => $activity,
        ]);
    }
}

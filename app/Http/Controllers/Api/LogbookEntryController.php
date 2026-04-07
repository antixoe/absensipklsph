<?php

namespace App\Http\Controllers\Api;

use App\Models\LogbookEntry;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogbookEntryController extends \Illuminate\Routing\Controller
{
    /**
     * Get all logbook entries for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = LogbookEntry::query();

        // Filter by student if the user is a student
        if ($user->role_id == 2) { // Student role
            $student = $user->student;
            $query->where('student_id', $student->id);
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('month')) {
            $query->whereMonth('entry_date', $request->month);
        }

        $entries = $query->with('student.user', 'instructor.user')
            ->orderBy('entry_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($entries);
    }

    /**
     * Store a new logbook entry.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        $request->validate([
            'entry_date' => 'required|date',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'hours_worked' => 'nullable|integer|min:1|max:24',
        ]);

        $entry = LogbookEntry::create([
            'student_id' => $student->id,
            'entry_date' => $request->entry_date,
            'title' => $request->title,
            'description' => $request->description,
            'achievements' => $request->achievements,
            'challenges' => $request->challenges,
            'learning_outcomes' => $request->learning_outcomes,
            'hours_worked' => $request->hours_worked ?? 8,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Logbook entry created successfully',
            'data' => $entry,
        ], 201);
    }

    /**
     * Get a specific logbook entry.
     */
    public function show(LogbookEntry $entry)
    {
        return response()->json($entry->load('student.user', 'instructor.user'));
    }

    /**
     * Update a logbook entry.
     */
    public function update(Request $request, LogbookEntry $entry)
    {
        // Only allow updating if status is draft
        if ($entry->status !== 'draft') {
            return response()->json([
                'message' => 'Can only edit draft entries',
            ], 403);
        }

        $request->validate([
            'entry_date' => 'nullable|date',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'achievements' => 'nullable|string',
            'challenges' => 'nullable|string',
            'learning_outcomes' => 'nullable|string',
            'hours_worked' => 'nullable|integer|min:1|max:24',
        ]);

        $entry->update($request->validated());

        return response()->json([
            'message' => 'Logbook entry updated successfully',
            'data' => $entry,
        ]);
    }

    /**
     * Delete a logbook entry.
     */
    public function destroy(LogbookEntry $entry)
    {
        // Only allow deleting if status is draft
        if ($entry->status !== 'draft') {
            return response()->json([
                'message' => 'Can only delete draft entries',
            ], 403);
        }

        $entry->delete();

        return response()->json([
            'message' => 'Logbook entry deleted successfully',
        ]);
    }

    /**
     * Submit a logbook entry for review.
     */
    public function submit(Request $request, LogbookEntry $entry)
    {
        if ($entry->status !== 'draft') {
            return response()->json([
                'message' => 'Entry is not in draft status',
            ], 400);
        }

        $entry->update([
            'status' => 'submitted',
        ]);

        // TODO: Send notification to instructor

        return response()->json([
            'message' => 'Logbook entry submitted successfully',
            'data' => $entry,
        ]);
    }

    /**
     * Approve a logbook entry (instructor only).
     */
    public function approve(Request $request, LogbookEntry $entry)
    {
        $user = $request->user();
        if ($user->role_id != 3) { // Instructor role
            return response()->json([
                'message' => 'Only instructors can approve entries',
            ], 403);
        }

        $request->validate([
            'feedback' => 'nullable|string',
        ]);

        $entry->update([
            'status' => 'approved',
            'instructor_id' => $user->instructor->id,
            'instructor_feedback' => $request->feedback,
            'approved_date' => now(),
        ]);

        // TODO: Send notification to student

        return response()->json([
            'message' => 'Logbook entry approved successfully',
            'data' => $entry,
        ]);
    }

    /**
     * Reject a logbook entry (instructor only).
     */
    public function reject(Request $request, LogbookEntry $entry)
    {
        $user = $request->user();
        if ($user->role_id != 3) { // Instructor role
            return response()->json([
                'message' => 'Only instructors can reject entries',
            ], 403);
        }

        $request->validate([
            'feedback' => 'required|string',
        ]);

        $entry->update([
            'status' => 'draft',
            'instructor_id' => $user->instructor->id,
            'instructor_feedback' => $request->feedback,
        ]);

        // TODO: Send notification to student

        return response()->json([
            'message' => 'Logbook entry rejected and returned to draft',
            'data' => $entry,
        ]);
    }
}

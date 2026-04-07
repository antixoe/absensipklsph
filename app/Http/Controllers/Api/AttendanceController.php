<?php

namespace App\Http\Controllers\Api;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AttendanceController extends \Illuminate\Routing\Controller
{
    /**
     * Get all attendance records for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Attendance::query();

        // Filter by student if the user is a student
        if ($user->role_id == 2) { // Student role
            $student = $user->student;
            $query->where('student_id', $student->id);
        }

        // Apply filters
        if ($request->has('date')) {
            $query->whereDate('attendance_date', $request->date);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }

        $attendances = $query->with('student.user')
            ->orderBy('attendance_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($attendances);
    }

    /**
     * Store a new attendance record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:present,late,absent,sick,permission',
        ]);

        $attendance = Attendance::create($request->validated());

        return response()->json([
            'message' => 'Attendance record created successfully',
            'data' => $attendance,
        ], 201);
    }

    /**
     * Get a specific attendance record.
     */
    public function show(Attendance $attendance)
    {
        return response()->json($attendance->load('student.user'));
    }

    /**
     * Update an attendance record.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'attendance_date' => 'nullable|date',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'nullable|in:present,late,absent,sick,permission',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($request->validated());

        return response()->json([
            'message' => 'Attendance record updated successfully',
            'data' => $attendance,
        ]);
    }

    /**
     * Delete an attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance record deleted successfully',
        ]);
    }

    /**
     * Check-in the student with GPS and photo.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();
        $student = $user->student;

        // Check if already checked in today
        $existingAttendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->first();

        if ($existingAttendance && $existingAttendance->check_in_time) {
            return response()->json([
                'message' => 'Already checked in today',
                'data' => $existingAttendance,
            ], 400);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance/checkin', 'public');
        }

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'attendance_date' => today(),
            ],
            [
                'check_in_time' => now()->format('H:i:s'),
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
                'check_in_photo' => $photoPath,
                'status' => 'present',
            ]
        );

        return response()->json([
            'message' => 'Check-in successful',
            'data' => $attendance,
        ]);
    }

    /**
     * Check-out the student with GPS and photo.
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();
        $student = $user->student;

        // Get today's attendance
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'No check-in found for today',
            ], 400);
        }

        if ($attendance->check_out_time) {
            return response()->json([
                'message' => 'Already checked out today',
                'data' => $attendance,
            ], 400);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance/checkout', 'public');
        }

        $attendance->update([
            'check_out_time' => now()->format('H:i:s'),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_photo' => $photoPath,
        ]);

        return response()->json([
            'message' => 'Check-out successful',
            'data' => $attendance,
        ]);
    }

    /**
     * Generate attendance report for a student.
     */
    public function report(Student $student, Request $request)
    {
        $query = $student->attendances();

        // Filter by month
        if ($request->has('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }

        $attendances = $query->orderBy('attendance_date', 'desc')->get();

        $summary = [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'sick' => $attendances->where('status', 'sick')->count(),
            'permission' => $attendances->where('status', 'permission')->count(),
            'attendance_rate' => $attendances->count() > 0 
                ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100, 2) 
                : 0,
        ];

        return response()->json([
            'student' => $student->load('user'),
            'period' => $request->has('month') ? 'Month: ' . $request->month : 'All time',
            'summary' => $summary,
            'details' => $attendances,
        ]);
    }
}

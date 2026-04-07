<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }
        
        $attendances = Attendance::where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(10);
        
        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        return view('attendance.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['required', 'in:present,late,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'attendance_date' => $validated['date'],
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'],
            'check_in_latitude' => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect('/attendance')->with('success', 'Attendance record created successfully!');
    }

    public function show(Attendance $attendance)
    {
        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }
        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }
        return view('attendance.edit', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status' => ['required', 'in:present,late,absent,sick,permission'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $attendance->update([
            'attendance_date' => $validated['date'],
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'],
            'check_in_latitude' => $validated['latitude'],
            'check_in_longitude' => $validated['longitude'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect('/attendance')->with('success', 'Attendance record updated successfully!');
    }

    public function destroy(Attendance $attendance)
    {
        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }

        $attendance->delete();
        return redirect('/attendance')->with('success', 'Attendance record deleted successfully!');
    }
}

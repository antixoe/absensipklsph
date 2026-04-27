<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        $student = Auth::user()->student;

        $attendances = Attendance::where('student_id', $student->id)
            ->orderBy('attendance_date', 'desc')
            ->paginate(10);
        
        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        return view('attendance.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        $student = Auth::user()->student;

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
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }
        return view('attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }
        return view('attendance.edit', compact('attendance'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

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
        if ($redirect = $this->requireStudentAccess()) {
            return $redirect;
        }

        $student = Auth::user()->student;
        if ($attendance->student_id !== $student->id) {
            abort(403);
        }

        $attendance->delete();
        return redirect('/attendance')->with('success', 'Attendance record deleted successfully!');
    }

    private function requireStudentAccess()
    {
        $user = Auth::user();

        if (!$user->hasRole(Role::STUDENT)) {
            return redirect()->route('dashboard')->with('error', 'Only students can access attendance records.');
        }

        if (!$user->student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        return null;
    }
}

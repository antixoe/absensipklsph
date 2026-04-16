<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Absence;
use App\Models\LogbookEntry;
use App\Models\Document;
use App\Models\Activity;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role->name ?? 'Unknown';

        // Fetch absence data for the chart
        $absenceData = $this->getAbsenceData();

        // Fetch statistics for the current user
        $studentId = Auth::user()->student->id ?? 0;
        $stats = [
            'absenceCount' => Absence::where('student_id', $studentId)->count() ?? 0,
            'logbookCount' => LogbookEntry::where('student_id', $studentId)->count() ?? 0,
            'documentCount' => Document::where('student_id', $studentId)->count() ?? 0,
            'activityCount' => Activity::where('student_id', $studentId)->count() ?? 0,
        ];

        return view('dashboard.index', compact('user', 'role', 'absenceData', 'stats'));
    }

    /**
     * Get absence statistics data for chart
     */
    private function getAbsenceData()
    {
        // Get data for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        // Get all attendance records in the date range
        $attendances = Attendance::whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->attendance_date->format('Y-m-d');
            });

        // Get all students with attendance records
        $students = Student::whereHas('user', function ($query) {
            $query->whereNotNull('id');
        })->get();

        // Count absences per day
        $dates = [];
        $absenceCounts = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            // Count how many registered students didn't have attendance
            $presentCount = isset($attendances[$dateStr]) ? $attendances[$dateStr]->count() : 0;
            $absentCount = max(0, $students->count() - $presentCount);
            $absenceCounts[] = $absentCount;
        }

        return [
            'dates' => $dates,
            'absences' => $absenceCounts,
            'totalStudents' => $students->count(),
        ];
    }

    public function attendance()
    {
        return view('dashboard.attendance');
    }

    public function logbook()
    {
        return view('dashboard.logbook');
    }

    public function activities()
    {
        return view('dashboard.activities');
    }

    public function documents()
    {
        return view('dashboard.documents');
    }

    public function reports()
    {
        return view('dashboard.reports');
    }
}

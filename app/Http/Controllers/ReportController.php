<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show reports page with charts and statistics
     */
    public function index()
    {
        $dateRange = request('range', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        // Get absence statistics
        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate);
        $absenceByStatus = $this->getAbsenceByStatus($startDate, $endDate);
        $absenceByStudent = $this->getAbsenceByStudent($startDate, $endDate);
        $dailyAbsenceData = $this->getDailyAbsenceData($startDate, $endDate);
        $approvalRateData = $this->getApprovalRateData($startDate, $endDate);

        return view('reports.index', compact(
            'absenceStats',
            'absenceByStatus',
            'absenceByStudent',
            'dailyAbsenceData',
            'approvalRateData',
            'dateRange'
        ));
    }

    /**
     * Get overall absence statistics
     */
    private function getAbsenceStatistics($startDate, $endDate)
    {
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])->get();

        return [
            'total' => $absences->count(),
            'pending' => $absences->where('status', 'pending')->count(),
            'approved' => $absences->where('status', 'approved')->count(),
            'rejected' => $absences->where('status', 'rejected')->count(),
            'approvalRate' => $absences->count() > 0 
                ? round(($absences->where('status', 'approved')->count() / $absences->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Get absence count by status for pie chart
     */
    private function getAbsenceByStatus($startDate, $endDate)
    {
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])->get();

        return [
            'labels' => ['Pending', 'Approved', 'Rejected'],
            'data' => [
                $absences->where('status', 'pending')->count(),
                $absences->where('status', 'approved')->count(),
                $absences->where('status', 'rejected')->count(),
            ],
        ];
    }

    /**
     * Get top 10 students with most absences
     */
    private function getAbsenceByStudent($startDate, $endDate)
    {
        $studentAbsences = Student::with('user')
            ->withCount(['absences' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('absence_date', [$startDate, $endDate]);
            }])
            ->orderBy('absences_count', 'desc')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];

        foreach ($studentAbsences as $student) {
            if ($student->absences_count > 0) {
                $labels[] = $student->user->name;
                $data[] = $student->absences_count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Get daily absence data for line chart
     */
    private function getDailyAbsenceData($startDate, $endDate)
    {
        $dates = [];
        $absenceCounts = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            $count = Absence::whereDate('absence_date', $dateStr)->count();
            $absenceCounts[] = $count;
        }

        return [
            'dates' => $dates,
            'data' => $absenceCounts,
        ];
    }

    /**
     * Get approval rate data
     */
    private function getApprovalRateData($startDate, $endDate)
    {
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])->get();

        $approved = $absences->where('status', 'approved')->count();
        $rejected = $absences->where('status', 'rejected')->count();
        $pending = $absences->where('status', 'pending')->count();

        return [
            'labels' => ['Approved', 'Rejected', 'Pending'],
            'data' => [$approved, $rejected, $pending],
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController extends Controller
{
    /**
     * Check if the current user is a homeroom teacher.
     */
    private function isHomeroomTeacher(?User $user): bool
    {
        return $user?->hasRole(Role::WALI_KELAS) ?? false;
    }

    /**
     * Get the scope text used to identify the homeroom teacher's class.
     */
    private function getHomeroomTeacherScope(?User $user): ?string
    {
        if (!$this->isHomeroomTeacher($user)) {
            return null;
        }

        $scope = trim((string) ($user?->instructor?->department ?? $user?->instructor?->position ?? ''));

        return $scope !== '' ? $scope : null;
    }

    /**
     * Normalize scope text for flexible matching.
     */
    private function normalizeScopeText(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }

    /**
     * Build a short acronym from a scope string.
     */
    private function makeScopeAcronym(string $value): string
    {
        $stopWords = [
            'department',
            'teacher',
            'homeroom',
            'class',
            'program',
            'student',
            'school',
            'major',
        ];

        $parts = array_filter(explode(' ', $this->normalizeScopeText($value)));
        $letters = array_map(function (string $part) use ($stopWords) {
            return in_array($part, $stopWords, true) ? '' : substr($part, 0, 1);
        }, $parts);

        return implode('', array_filter($letters));
    }

    /**
     * Check whether a teacher scope matches a student class label.
     */
    private function scopeMatchesStudentClass(string $scope, ?string $studentClass): bool
    {
        $normalizedScope = $this->normalizeScopeText($scope);
        $normalizedStudentClass = $this->normalizeScopeText($studentClass);

        if ($normalizedScope === '' || $normalizedStudentClass === '') {
            return false;
        }

        if ($normalizedScope === $normalizedStudentClass) {
            return true;
        }

        if (str_contains($normalizedStudentClass, $normalizedScope) || str_contains($normalizedScope, $normalizedStudentClass)) {
            return true;
        }

        $scopeAcronym = $this->makeScopeAcronym($normalizedScope);
        $studentAcronym = $this->makeScopeAcronym($normalizedStudentClass);

        return $scopeAcronym !== '' && $scopeAcronym === $studentAcronym;
    }

    /**
     * Get students allowed for the current homeroom teacher.
     */
    private function getHomeroomTeacherStudentIds(?User $user): array
    {
        $scope = $this->getHomeroomTeacherScope($user);

        if (!$scope) {
            return [];
        }

        return Student::with('user')
            ->get()
            ->filter(fn (Student $student) => $this->scopeMatchesStudentClass($scope, $student->major))
            ->pluck('id')
            ->values()
            ->all();
    }

    /**
     * Apply homeroom teacher restrictions to an absence query.
     */
    private function scopeAbsencesForUser($query, ?User $user)
    {
        if (!$this->isHomeroomTeacher($user)) {
            return $query;
        }

        $studentIds = $this->getHomeroomTeacherStudentIds($user);

        if (empty($studentIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('student_id', $studentIds);
    }

    /**
     * Show reports page with charts and statistics
     */
    public function index()
    {
        $currentUser = auth()->user();
        $dateRange = request('range', '30'); // Default 30 days
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        $isHomeroomTeacher = $this->isHomeroomTeacher($currentUser);
        $classScope = $this->getHomeroomTeacherScope($currentUser);
        $classStudentIds = $isHomeroomTeacher ? $this->getHomeroomTeacherStudentIds($currentUser) : [];

        // Get absence statistics
        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate, $currentUser);
        $absenceByStatus = $this->getAbsenceByStatus($startDate, $endDate, $currentUser);
        $dailyAbsenceData = $this->getDailyAbsenceData($startDate, $endDate, $currentUser);

        return view('reports.index', compact(
            'absenceStats',
            'absenceByStatus',
            'dailyAbsenceData',
            'dateRange',
            'startDate',
            'endDate',
            'classScope',
            'isHomeroomTeacher',
            'classStudentIds'
        ));
    }

    /**
     * Get overall absence statistics
     */
    private function getAbsenceStatistics($startDate, $endDate, ?User $user = null)
    {
        $absences = $this->scopeAbsencesForUser(
            Absence::whereBetween('absence_date', [$startDate, $endDate]),
            $user
        )->get();

        return [
            'total' => $absences->count(),
            'present' => $absences->where('status', 'present')->count(),
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
    private function getAbsenceByStatus($startDate, $endDate, ?User $user = null)
    {
        $absences = $this->scopeAbsencesForUser(
            Absence::whereBetween('absence_date', [$startDate, $endDate]),
            $user
        )->get();

        return [
            'labels' => ['Present', 'Pending', 'Approved', 'Rejected'],
            'data' => [
                $absences->where('status', 'present')->count(),
                $absences->where('status', 'pending')->count(),
                $absences->where('status', 'approved')->count(),
                $absences->where('status', 'rejected')->count(),
            ],
        ];
    }



    /**
     * Get daily absence data for line chart
     */
    private function getDailyAbsenceData($startDate, $endDate, ?User $user = null)
    {
        $dates = [];
        $absenceCounts = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            $count = $this->scopeAbsencesForUser(
                Absence::whereDate('absence_date', $dateStr),
                $user
            )->count();
            $absenceCounts[] = $count;
        }

        return [
            'dates' => $dates,
            'data' => $absenceCounts,
        ];
    }

    /**
     * Export report data to Excel
     */
    public function exportExcel(Request $request)
    {
        $currentUser = auth()->user();
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        $isHomeroomTeacher = $this->isHomeroomTeacher($currentUser);
        $classScope = $this->getHomeroomTeacherScope($currentUser);

        // Get data
        $absences = $this->scopeAbsencesForUser(
            Absence::whereBetween('absence_date', [$startDate, $endDate]),
            $currentUser
        )
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate, $currentUser);

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', $isHomeroomTeacher && $classScope
            ? 'Absence Report - ' . $classScope
            : 'Absence Report');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Date range
        $sheet->setCellValue('A2', 'Period: ' . optional($startDate)->format('M d, Y') . ' to ' . optional($endDate)->format('M d, Y'));
        $sheet->mergeCells('A2:F2');

        // Summary Statistics
        $sheet->setCellValue('A4', 'Summary Statistics');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);

        $sheet->setCellValue('A5', 'Total Absences:');
        $sheet->setCellValue('B5', $absenceStats['total']);

        $sheet->setCellValue('A6', 'Approved:');
        $sheet->setCellValue('B6', $absenceStats['approved']);

        $sheet->setCellValue('A7', 'Pending:');
        $sheet->setCellValue('B7', $absenceStats['pending']);

        $sheet->setCellValue('A8', 'Rejected:');
        $sheet->setCellValue('B8', $absenceStats['rejected']);

        $sheet->setCellValue('A9', 'Approval Rate:');
        $sheet->setCellValue('B9', $absenceStats['approvalRate'] . '%');

        // Detailed Absence List
        $sheet->setCellValue('A11', 'Detailed Absence Records');
        $sheet->getStyle('A11')->getFont()->setBold(true)->setSize(12);

        // Headers
        $headers = ['Date', 'Student Name', 'Email', 'Reason', 'Status', 'Notes'];
        $headerRow = 12;
        foreach ($headers as $index => $header) {
            $column = chr(65 + $index);
            $sheet->setCellValue($column . $headerRow, $header);
            $sheet->getStyle($column . $headerRow)->getFont()->setBold(true);
            $sheet->getStyle($column . $headerRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE5E5E5');
        }

        // Data rows
        $row = $headerRow + 1;
        foreach ($absences as $absence) {
            $sheet->setCellValue('A' . $row, optional($absence->absence_date)->format('M d, Y') ?? 'N/A');
            $sheet->setCellValue('B' . $row, $absence->student->user->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $absence->student->user->email ?? 'N/A');
            $sheet->setCellValue('D' . $row, $absence->reason ?? '-');
            $sheet->setCellValue('E' . $row, ucfirst($absence->status));
            $sheet->setCellValue('F' . $row, $absence->notes ?? '-');
            $row++;
        }

        // Auto-fit columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Write to file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'absence_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $writer->save('php://output');
        exit;
    }

    /**
     * Export report as printable PDF view
     */
    public function exportPdf(Request $request)
    {
        $currentUser = auth()->user();
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        $classScope = $this->getHomeroomTeacherScope($currentUser);

        // Get data
        $absences = $this->scopeAbsencesForUser(
            Absence::whereBetween('absence_date', [$startDate, $endDate]),
            $currentUser
        )
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate, $currentUser);

        return view('reports.pdf', compact(
            'absenceStats',
            'absences',
            'startDate',
            'endDate',
            'classScope'
        ));
    }

    /**
     * Show printable report view
     */
    public function printReport(Request $request)
    {
        $currentUser = auth()->user();
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();
        $classScope = $this->getHomeroomTeacherScope($currentUser);

        // Get data
        $absences = $this->scopeAbsencesForUser(
            Absence::whereBetween('absence_date', [$startDate, $endDate]),
            $currentUser
        )
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate, $currentUser);

        return view('reports.print', compact(
            'absenceStats',
            'absences',
            'startDate',
            'endDate',
            'classScope'
        ));
    }
}

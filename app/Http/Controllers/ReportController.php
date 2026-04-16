<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
        $dailyAbsenceData = $this->getDailyAbsenceData($startDate, $endDate);
        $approvalRateData = $this->getApprovalRateData($startDate, $endDate);

        return view('reports.index', compact(
            'absenceStats',
            'absenceByStatus',
            'dailyAbsenceData',
            'approvalRateData',
            'dateRange',
            'startDate',
            'endDate'
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

    /**
     * Export report data to Excel
     */
    public function exportExcel(Request $request)
    {
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        // Get data
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate);

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'Absence Report');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Date range
        $sheet->setCellValue('A2', 'Period: ' . $startDate->format('M d, Y') . ' to ' . $endDate->format('M d, Y'));
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
            $sheet->setCellValue('A' . $row, $absence->absence_date->format('M d, Y'));
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
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        // Get data
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate);

        return view('reports.pdf', compact(
            'absenceStats',
            'absences',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show printable report view
     */
    public function printReport(Request $request)
    {
        $dateRange = $request->get('range', '30');
        $startDate = Carbon::now()->subDays($dateRange);
        $endDate = Carbon::now();

        // Get data
        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])
            ->with('student')
            ->orderBy('absence_date', 'desc')
            ->get();

        $absenceStats = $this->getAbsenceStatistics($startDate, $endDate);

        return view('reports.print', compact(
            'absenceStats',
            'absences',
            'startDate',
            'endDate'
        ));
    }
}


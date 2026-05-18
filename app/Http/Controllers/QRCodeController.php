<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Models\Absence;
use App\Models\Student;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QRCodeController extends Controller
{
    /**
     * Show QR code management page (admin only).
     */
    public function index()
    {
        // Only Kesiswaan can manage QR codes
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $qrCodes = QRCode::with('creator')
            ->orderBy('qr_date', 'desc')
            ->paginate(20);

        return view('qrcode.index', compact('qrCodes'));
    }

    /**
     * Show form to generate QR codes.
     */
    public function create()
    {
        // Only Kesiswaan can create QR codes
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        return view('qrcode.create');
    }

    /**
     * Generate QR codes in bulk.
     */
    public function store(Request $request)
    {
        // Only Kesiswaan can create QR codes
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'qr_date' => 'required|date',
            'qr_time' => 'required|date_format:H:i',
            'quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',
            'expires_at' => 'nullable|date',
        ]);

        try {
            $createdCount = 0;

            // Combine date and time into datetime
            $dateTime = Carbon::createFromFormat('Y-m-d H:i', $validated['qr_date'] . ' ' . $validated['qr_time']);

            // Generate the requested amount of QR codes
            for ($i = 0; $i < $validated['quantity']; $i++) {
                QRCode::create([
                    'code' => QRCode::generateCode(),
                    'qr_date' => $dateTime,
                    'created_by' => auth()->id(),
                    'notes' => $validated['notes'] ?? null,
                    'expires_at' => $validated['expires_at'] ?? null,
                    'status' => 'active',
                ]);
                $createdCount++;
            }

            $successMsg = "Generated $createdCount QR code(s) successfully for " . $dateTime->format('M d, Y \\a\\t H:i');

            // Check if it's an AJAX request
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['success' => $successMsg]);
            }

            return redirect()->route('qrcode.index')->with('success', $successMsg);
        } catch (\Exception $e) {
            \Log::error('QR Code generation error: ' . $e->getMessage());
            
            // Check if it's an AJAX request
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['error' => 'Error generating QR codes: ' . $e->getMessage()], 422);
            }

            return back()->with('error', 'Error generating QR codes: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show QR code details and scan history.
     */
    public function show(QRCode $qrCode)
    {
        // Only Kesiswaan, Guru, and Wali Kelas can view details
        if (!auth()->user()->hasAnyRole([Role::KESISWAAN, Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $scanHistory = $qrCode->absences()
            ->with('student.user')
            ->orderBy('scanned_qr_at', 'desc')
            ->limit(10)
            ->get();

        // If AJAX request, return JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'code' => $qrCode->code,
                'date' => $qrCode->qr_date->format('M d, Y \\a\\t H:i'),
                'scans' => $qrCode->absences()->count(),
                'qr_image' => "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCode->code),
                'scan_history' => $scanHistory->map(function($scan) {
                    return [
                        'student_name' => $scan->student->user->name,
                        'nim' => $scan->student->nim,
                        'scanned_at' => $scan->scanned_qr_at->format('M d, Y H:i:s'),
                        'location' => $scan->location_name ?? '—'
                    ];
                })
            ]);
        }

        return view('qrcode.show', compact('qrCode', 'scanHistory'));
    }

    /**
     * Export a QR code as a printable PDF card.
     */
    public function exportPdf(QRCode $qrCode)
    {
        // Only Kesiswaan can export QR cards.
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $pdf = new \TCPDF('P', 'mm', [105, 148], true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('QR Code - ' . $qrCode->code);
        $pdf->SetSubject('Printable QR Code Card');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        // Header band
        $pdf->SetFillColor(249, 115, 22);
        $pdf->Rect(0, 0, 105, 22, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetY(7);
        $pdf->Cell(0, 7, 'Absensi Sekolah', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 4, 'Printable QR Code Card', 0, 1, 'C');

        $pdf->SetTextColor(17, 24, 39);

        // QR Code block
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetY(30);
        $pdf->Cell(0, 6, 'QR Code', 0, 1, 'C');

        $qrStyle = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1,
        ];

        $pdf->write2DBarcode($qrCode->code, 'QRCODE,H', 27, 38, 51, 51, $qrStyle, 'N');

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetY(92);
        $pdf->Cell(0, 6, $qrCode->code, 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Generated: ' . optional($qrCode->qr_date)->format('M d, Y H:i'), 0, 1, 'C');
        $pdf->Cell(0, 5, 'Status: ' . ucfirst($qrCode->status), 0, 1, 'C');

        if (!empty($qrCode->notes)) {
            $pdf->Ln(2);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->MultiCell(0, 4, 'Notes: ' . $qrCode->notes, 0, 'C', false, 1);
        }

        $pdf->SetY(132);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 4, 'Scan this code with the teacher QR scanner to record attendance.', 0, 1, 'C');

        $fileName = 'qr-code-' . $qrCode->code . '.pdf';
        $content = $pdf->Output($fileName, 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Show QR code scanner page for teachers to scan student QR codes.
     */
    public function teacherScanner()
    {
        // Only Guru (teachers) and Wali Kelas (homeroom teachers) can scan QR codes
        if (!auth()->user()->hasAnyRole([Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Only teachers can scan QR codes.');
        }

        // Show active student QR codes first, then other active codes created today.
        $todayQRCodes = QRCode::where('status', 'active')
            ->where(function ($query) {
                $query->whereHas('student')
                    ->orWhereDate('qr_date', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('qrcode.teacher-scanner', compact('todayQRCodes'));
    }

    /**
     * Process QR code scan (teachers only - students cannot scan).
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string'],
            'qr_code' => ['nullable', 'string'],
            'selfie' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ip_address' => 'nullable|string',
            'mode' => 'nullable|in:checkin,checkout',
        ]);

        $currentUser = Auth::user();

        // Only teachers (Guru) and homeroom teachers (Wali Kelas) can scan QR codes
        if (!$currentUser->hasAnyRole([Role::GURU, Role::WALI_KELAS])) {
            return response()->json([
                'success' => false,
                'message' => 'Only teachers can scan student QR codes. Students cannot mark their own attendance.'
            ], 403);
        }

        $code = $validated['code'] ?? $validated['qr_code'] ?? null;

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'No QR code provided.'
            ], 422);
        }

        // Teacher scanning student QR code - create attendance for that student
        return $this->teacherScanQRCode($request, array_merge($validated, ['code' => $code]));
    }

    /**
     * Teacher scans student QR code to record attendance.
     */
    private function teacherScanQRCode(Request $request, array $validated)
    {
        $currentUser = Auth::user();

        if (!$currentUser->hasAnyRole([Role::GURU, Role::WALI_KELAS])) {
            return response()->json([
                'success' => false,
                'message' => 'Only teachers can scan student QR codes.'
            ], 403);
        }

        // Find the QR code
        $qrCode = QRCode::where('code', $validated['code'])->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. This code does not exist in the system.'
            ], 404);
        }

        // Check if QR code is still active
        if (!$qrCode->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This QR code has expired or is disabled.'
            ], 400);
        }

        try {
            // Find student by QR code ID first, then fall back to the stored QR code text.
            $student = Student::where('qr_code_id', $qrCode->id)->first();

            if (!$student) {
                $student = Student::where('student_qr_code', $qrCode->code)->first();
            }

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'No student found for this QR code. The QR code may not be assigned to a student.'
                ], 404);
            }

            if (!$student->qr_code_id || $student->qr_code_id !== $qrCode->id) {
                $student->update([
                    'qr_code_id' => $qrCode->id,
                    'student_qr_code' => $qrCode->code,
                ]);
            }

            // Check if attendance already recorded for today
            $todayAbsence = Absence::where('student_id', $student->id)
                ->whereDate('absence_date', Carbon::today())
                ->first();

            if ($todayAbsence) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already recorded for ' . $student->user->name . ' today.'
                ], 400);
            }

            // Create attendance record
            $absence = Absence::create([
                'student_id' => $student->id,
                'absence_date' => Carbon::today(),
                'scanned_qr_at' => Carbon::now(),
                'status' => 'present',
                'recorded_by' => $currentUser->id,
                'notes' => 'Scanned by ' . $currentUser->name . ' using QR code',
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance recorded for ' . $student->user->name,
                'student_name' => $student->user->name,
                'student_id' => $student->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Teacher QR Code scan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate a QR code.
     */
    public function deactivate(QRCode $qrCode)
    {
        // Only Kesiswaan can deactivate
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $qrCode->update(['status' => 'disabled']);

        // Check if it's an AJAX request
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json(['success' => 'QR code disabled successfully.']);
        }

        return redirect()->back()->with('success', 'QR code disabled successfully.');
    }

    /**
     * Generate QR code image for download.
     */
    public function downloadQRImage(QRCode $qrCode)
    {
        // Only Kesiswaan can download
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $barcode = new \TCPDF2DBarcode($qrCode->code, 'QRCODE,H');
        $pngData = $barcode->getBarcodePngData(6, 6, [0, 0, 0]);
        $fileName = 'qr-code-' . $qrCode->code . '.png';

        return response($pngData, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}

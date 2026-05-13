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
     * Show QR code scanner page for teachers to scan student QR codes.
     */
    public function teacherScanner()
    {
        // Only Guru (teachers) and Wali Kelas (homeroom teachers) can scan QR codes
        if (!auth()->user()->hasAnyRole([Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Only teachers can scan QR codes.');
        }

        // Get available QR codes for today
        $todayQRCodes = QRCode::whereDate('qr_date', Carbon::today())
            ->where('status', 'active')
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
            'code' => 'required|string',
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

        // Teacher scanning student QR code - create attendance for that student
        return $this->teacherScanQRCode($request, $validated);
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
            // Find student by QR code ID - student QR codes are linked by qr_code_id
            $student = Student::where('qr_code_id', $qrCode->id)->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'No student found for this QR code. The QR code may not be assigned to a student.'
                ], 404);
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

        // Use an online QR code API service
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCode->code);

        return redirect($qrImageUrl);
    }
}

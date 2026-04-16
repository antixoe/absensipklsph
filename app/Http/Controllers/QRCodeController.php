<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Models\Absence;
use App\Models\Student;
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
        // Only admins/supervisors can manage QR codes
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
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
        // Only admins/supervisors can create QR codes
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        return view('qrcode.create');
    }

    /**
     * Generate QR codes in bulk.
     */
    public function store(Request $request)
    {
        // Only admins/supervisors can create QR codes
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
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
        // Only admins/supervisors can view details
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
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
     * Show QR code scanner page for students.
     */
    public function scanner()
    {
        $currentUser = Auth::user();
        
        // Check if current user is a student
        $student = Student::where('user_id', $currentUser->id)->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'You must be registered as a student to scan QR codes.');
        }

        // Get available QR codes for today
        $todayQRCodes = QRCode::where('qr_date', Carbon::today())
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        $alreadyScanned = null;
        if ($todayQRCodes) {
            $alreadyScanned = Absence::where('student_id', $student->id)
                ->whereDate('absence_date', Carbon::today())
                ->where('qr_code_id', $todayQRCodes->id)
                ->first();
        }

        return view('qrcode.scanner', compact('student', 'todayQRCodes', 'alreadyScanned'));
    }

    /**
     * Process QR code scan.
     */
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $currentUser = Auth::user();
        $student = Student::where('user_id', $currentUser->id)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student record not found.'
            ], 400);
        }

        // Find the QR code
        $qrCode = QRCode::where('code', $validated['code'])->first();

        if (!$qrCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Please try again.'
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
            // Check if QR code is for today
            if ($qrCode->qr_date->toDateString() !== Carbon::today()->toDateString()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This QR code is for ' . $qrCode->qr_date->format('M d, Y \\a\\t H:i') . ', not today.'
                ], 400);
            }

            // Check if student already marked attendance via this QR code today
            $existingAbsence = Absence::where('student_id', $student->id)
                ->where('qr_code_id', $qrCode->id)
                ->whereDate('absence_date', Carbon::today())
                ->first();

            if ($existingAbsence) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already scanned this QR code today at ' . $existingAbsence->scanned_qr_at->format('H:i:s')
                ], 400);
            }

            // Create absence record
            $absence = Absence::create([
                'student_id' => $student->id,
                'absence_date' => Carbon::now(),
                'qr_code_id' => $qrCode->id,
                'scanned_qr_at' => Carbon::now(),
                'status' => 'approved', // QR code scans are auto-approved
                'approved_at' => Carbon::now(),
                'approved_by' => $qrCode->created_by,
                'location_name' => 'Scanned via QR Code',
            ]);

            // Try to get geolocation if provided
            if ($request->has('latitude') && $request->has('longitude')) {
                $absence->update([
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'location_name' => $request->input('location_name') ?? 'Location',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '✓ Attendance marked successfully!',
                'data' => [
                    'student_name' => $currentUser->name,
                    'time' => $absence->scanned_qr_at->format('H:i:s'),
                    'date' => $absence->scanned_qr_at->format('M d, Y'),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('QR Code scan error: ' . $e->getMessage());
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
        // Only admins/supervisors can deactivate
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
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
        // Only admins/supervisors can download
        if (!auth()->user()->hasAnyRole(['admin', 'homeroom_teacher', 'head_of_department', 'industry_supervisor'])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Use an online QR code API service
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCode->code);

        return redirect($qrImageUrl);
    }
}

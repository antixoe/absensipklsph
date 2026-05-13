<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\QRCode;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentController extends Controller
{
    /**
     * Show all students with QR code management.
     */
    public function index()
    {
        // Only Kesiswaan, Kurikulum, and staff can view student list
        if (!auth()->user()->hasAnyRole([Role::KESISWAAN, Role::KURIKULUM, Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $students = Student::with(['user', 'qrCode'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.index', compact('students'));
    }

    /**
     * Show QR code details for a student.
     */
    public function showQRCode(Student $student)
    {
        // Authorization
        if (!auth()->user()->hasAnyRole([Role::KESISWAAN, Role::KURIKULUM, Role::GURU, Role::WALI_KELAS])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$student->qrCode) {
            return response()->json([
                'error' => 'No QR code found for this student',
                'student_name' => $student->user->name
            ], 404);
        }

        $qrCode = $student->qrCode;
        
        return response()->json([
            'success' => true,
            'student_id' => $student->id,
            'student_name' => $student->user->name,
            'student_nim' => $student->nim,
            'qr_code' => $qrCode->code,
            'qr_image_url' => $this->getQRImageUrl($qrCode->code),
            'created_at' => $qrCode->created_at->format('M d, Y H:i:s'),
            'status' => $qrCode->status,
        ]);
    }

    /**
     * Download QR code as image.
     */
    public function downloadQRCode(Student $student)
    {
        // Authorization
        if (!auth()->user()->hasAnyRole([Role::KESISWAAN, Role::KURIKULUM, Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        if (!$student->qrCode) {
            return redirect()->back()->with('error', 'No QR code found for this student.');
        }

        $qrCode = $student->qrCode;
        $imageUrl = $this->getQRImageUrl($qrCode->code);

        // Download the QR code image
        return redirect($imageUrl);
    }

    /**
     * Regenerate QR code for a student.
     */
    public function regenerateQRCode(Student $student)
    {
        // Only Kesiswaan can regenerate
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['error' => 'Only Kesiswaan can regenerate QR codes.'], 403);
        }

        try {
            // Delete old QR code
            if ($student->qrCode) {
                $student->qrCode->delete();
            }

            // Create new QR code
            $newQRCode = QRCode::createStudentQRCode($student->id);

            // Update student with new QR code
            $student->update([
                'qr_code_id' => $newQRCode->id,
                'student_qr_code' => $newQRCode->code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QR code regenerated successfully for ' . $student->user->name,
                'qr_code' => $newQRCode->code,
                'qr_image_url' => $this->getQRImageUrl($newQRCode->code),
                'created_at' => $newQRCode->created_at->format('M d, Y H:i:s'),
            ]);
        } catch (\Exception $e) {
            \Log::error('QR Code regeneration error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error regenerating QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR image URL using external API.
     */
    private function getQRImageUrl($code, $size = 300)
    {
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data=" . urlencode($code);
    }
}

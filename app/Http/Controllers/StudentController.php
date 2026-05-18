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

        $student->ensureQrCode();

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

        $student->ensureQrCode();

        if (!$student->qrCode) {
            return redirect()->back()->with('error', 'No QR code found for this student.');
        }

        $qrCode = $student->qrCode;
        $barcode = new \TCPDF2DBarcode($qrCode->code, 'QRCODE,H');
        $pngData = $barcode->getBarcodePngData(6, 6, [0, 0, 0]);
        $fileName = 'student-qr-' . ($student->nim ?? $student->id) . '.png';

        return response($pngData, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Export the student's QR code as a printable PDF card.
     */
    public function exportQRCode(Student $student)
    {
        if (!auth()->user()->hasAnyRole([Role::KESISWAAN, Role::KURIKULUM, Role::GURU, Role::WALI_KELAS])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $student->loadMissing(['user', 'qrCode']);
        $student->ensureQrCode();

        if (!$student->qrCode) {
            return redirect()->back()->with('error', 'No QR code found for this student.');
        }

        $qrCode = $student->qrCode;
        $studentName = $student->user->name ?? 'Student #' . $student->id;
        $pdf = new \TCPDF('P', 'mm', [105, 148], true, 'UTF-8', false);
        $pdf->SetCreator(config('app.name'));
        $pdf->SetAuthor(config('app.name'));
        $pdf->SetTitle('Student QR Code - ' . $studentName);
        $pdf->SetSubject('Printable Student QR Code Card');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 10);
        $pdf->AddPage();

        $pdf->SetFillColor(249, 115, 22);
        $pdf->Rect(0, 0, 105, 22, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetY(7);
        $pdf->Cell(0, 7, 'Student QR Card', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 4, config('app.name'), 0, 1, 'C');

        $pdf->SetTextColor(17, 24, 39);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetY(30);
        $pdf->Cell(0, 6, $studentName, 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'NIM/ID: ' . ($student->nim ?? '-'), 0, 1, 'C');
        $pdf->Cell(0, 5, 'School: ' . ($student->school ?? '-'), 0, 1, 'C');

        $qrStyle = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1,
        ];

        $pdf->write2DBarcode($qrCode->code, 'QRCODE,H', 27, 45, 51, 51, $qrStyle, 'N');

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetY(100);
        $pdf->Cell(0, 6, $qrCode->code, 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, 'Generated: ' . optional($qrCode->created_at)->format('M d, Y H:i'), 0, 1, 'C');
        $pdf->Cell(0, 5, 'Status: ' . ucfirst($qrCode->status), 0, 1, 'C');

        $pdf->SetY(132);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 4, 'Scan this code with the teacher QR scanner to record attendance.', 0, 1, 'C');

        $fileName = 'student-qr-' . ($student->nim ?? $student->id) . '.pdf';
        $content = $pdf->Output($fileName, 'S');

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
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
            $newQRCode = QRCode::createStudentQRCode($student->id, $student->user->name ?? null);

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

<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Show the absence page with list of students.
     */
    public function index()
    {
        $today = Carbon::today();
        
        // Get all students
        $students = Student::with('user')->get();
        
        // Get absences for today
        $absences = Absence::whereDate('absence_date', $today)->get()->keyBy('student_id');
        
        return view('absence.index', compact('students', 'absences', 'today'));
    }

    /**
     * Store absence records.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'ip_address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $today = Carbon::today();
        $selfieFilename = null;

        // Store the selfie image
        if ($request->hasFile('selfie')) {
            $file = $request->file('selfie');
            $selfieFilename = 'selfie_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('absences', $selfieFilename, 'public');
        }

        // Create absence records for each selected student
        foreach ($validated['student_ids'] as $studentId) {
            Absence::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'absence_date' => $today,
                ],
                [
                    'selfie_path' => $selfieFilename ? 'absences/' . $selfieFilename : null,
                    'ip_address' => $validated['ip_address'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'location_name' => $validated['location_name'],
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'pending',
                ]
            );
        }

        return redirect()->route('absence.index')->with('success', 'Absence(s) recorded successfully with selfie and location.');
    }

    /**
     * Show pending absences for approval.
     */
    public function pending()
    {
        // Get pending absences with student and user data
        $absences = Absence::with('student.user')
            ->where('status', 'pending')
            ->orderBy('absence_date', 'desc')
            ->get();

        return view('absence.pending', compact('absences'));
    }

    /**
     * Show the individual absence details for approval/rejection.
     */
    public function show($studentId)
    {
        $student = Student::with('user')->findOrFail($studentId);
        $absences = Absence::where('student_id', $studentId)->orderBy('absence_date', 'desc')->get();

        return view('absence.show', compact('student', 'absences'));
    }

    /**
     * Bulk approve or reject absences
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'absence_ids' => 'required|array',
            'absence_ids.*' => 'exists:absences,id',
            'action' => 'required|in:approve,reject',
            'signature' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $signatureFilename = null;

        // Store signature if provided
        if ($request->has('signature') && !empty($request->input('signature'))) {
            // Convert canvas signature to image
            $signatureData = $request->input('signature');
            
            // Remove data:image/png;base64, prefix
            if (strpos($signatureData, 'data:image') !== false) {
                $signatureData = preg_replace('#^data:image/\w+;base64,#i', '', $signatureData);
            }

            if (!empty($signatureData)) {
                $signatureData = base64_decode($signatureData);
                $signatureFilename = 'signature_' . time() . '.png';
                Storage::disk('public')->put('signatures/' . $signatureFilename, $signatureData);
            }
        }

        // Update all selected absences
        $action = $validated['action'];
        $status = $action === 'approve' ? 'approved' : 'rejected';

        foreach ($validated['absence_ids'] as $absenceId) {
            $absence = Absence::findOrFail($absenceId);
            $absence->update([
                'status' => $status,
                'approved_signature' => $signatureFilename ? 'signatures/' . $signatureFilename : null,
                'approved_notes' => $validated['notes'] ?? null,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);
        }

        $message = ucfirst($action) . 'd ' . count($validated['absence_ids']) . ' absence(s) successfully.';
        return redirect()->route('absence.pending')->with('success', $message);
    }

    /**
     * Approve an absence record.
     */
    public function approve(Absence $absence)
    {
        $absence->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Absence approved successfully.');
    }

    /**
     * Reject an absence record.
     */
    public function reject(Absence $absence)
    {
        $absence->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Absence rejected.');
    }
}

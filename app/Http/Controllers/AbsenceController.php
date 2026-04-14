<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Show the absence page with list of students.
     */
    public function index()
    {
        $today = Carbon::today();
        $currentUser = Auth::user();
        
        // Get all students
        $students = Student::with('user')->get();
        
        // Check if current user is a student
        $currentUserStudent = Student::where('user_id', $currentUser->id)->first();
        
        $loggedInStudents = [];
        
        // If logged-in user is a student, auto-select only their own record
        if ($currentUserStudent) {
            $loggedInStudents = [$currentUserStudent->id];
        } else {
            // If instructor/admin, get other students with active sessions
            $thirtyMinutesAgo = now()->subMinutes(30)->timestamp;
            $loggedInUserIds = DB::table('sessions')
                ->where('last_activity', '>=', $thirtyMinutesAgo)
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            
            // Get student IDs that are logged in
            $loggedInStudents = Student::whereIn('user_id', $loggedInUserIds)
                ->pluck('id')
                ->toArray();
        }
        
        // Get all absences for recent records display
        $absences = Absence::with('student')->get();
        
        // Check if current student already has an absence record for today
        $todayAbsence = null;
        if ($currentUserStudent) {
            $todayAbsence = Absence::where('student_id', $currentUserStudent->id)
                ->whereDate('absence_date', $today)
                ->first();
        }
        
        return view('absence.index', compact('students', 'absences', 'today', 'loggedInStudents', 'currentUserStudent', 'todayAbsence'));
    }

    /**
     * Store absence records.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_ids' => 'required|array',
                'selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'ip_address' => 'nullable|string',
                'location_name' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Absence validation error', $e->errors());
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            $today = Carbon::today();
            $currentUser = Auth::user();
            
            // Get or create student record for current user
            $currentUserStudent = Student::where('user_id', $currentUser->id)->first();
            if (!$currentUserStudent) {
                // Auto-create student record for user if it doesn't exist
                $currentUserStudent = Student::create([
                    'user_id' => $currentUser->id,
                    'nim' => 'AUTO_' . $currentUser->id . '_' . time(),
                    'internship_program_id' => 1, // Default program
                ]);
            }
            
            $studentIds = $validated['student_ids'];
            // If only a placeholder (0), use the current user's student ID
            if (count($studentIds) === 1 && $studentIds[0] == 0) {
                $studentIds = [$currentUserStudent->id];
            }
            
            // Validate that all student IDs exist
            $invalidIds = [];
            foreach ($studentIds as $id) {
                if ($id > 0 && !Student::where('id', $id)->exists()) {
                    $invalidIds[] = $id;
                }
            }
            
            if (!empty($invalidIds)) {
                return back()->withErrors(['student_ids' => 'One or more selected students do not exist.'])->withInput();
            }
            
            $selfieFilename = null;
            $alreadyExists = false;

            // Check if student already has a record for today
            if ($currentUserStudent) {
                $existingAbsence = Absence::where('student_id', $currentUserStudent->id)
                    ->whereDate('absence_date', $today)
                    ->first();
                
                if ($existingAbsence) {
                    $alreadyExists = true;
                }
            }

            // Store the selfie image
            if ($request->hasFile('selfie')) {
                $file = $request->file('selfie');
                $selfieFilename = 'selfie_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('absences', $selfieFilename, 'public');
            }

            // Create absence records for each selected student
            foreach ($studentIds as $studentId) {
                Absence::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'absence_date' => $today,
                    ],
                    [
                        'selfie_path' => $selfieFilename ? 'absences/' . $selfieFilename : null,
                        'ip_address' => $validated['ip_address'] ?? null,
                        'location_name' => $validated['location_name'] ?? null,
                        'notes' => $validated['notes'] ?? null,
                        'status' => 'pending',
                    ]
                );
            }

            $successMessage = $alreadyExists 
                ? 'Your absence record has been updated successfully!' 
                : 'Your absence has been recorded successfully! Pending approval.';

            return redirect()->route('absence.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Absence submission error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An error occurred while saving your absence: ' . $e->getMessage())->withInput();
        }
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

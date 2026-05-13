<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\QRCode;
use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Notifications\AbsenceSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Show the student's own attendance (read-only).
     */
    public function index()
    {
        $today = Carbon::today();
        $currentUser = Auth::user();

        if (!$currentUser->hasRole(Role::MURID)) {
            return redirect()->route('dashboard')->with('error', 'Only students can view their attendance.');
        }

        $currentUserStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentUserStudent) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        // Get all attendance records for this student (read-only view)
        $attendanceRecords = Absence::where('student_id', $currentUserStudent->id)
            ->orderBy('absence_date', 'desc')
            ->paginate(20);

        // Get today's attendance if exists
        $todayAbsence = Absence::where('student_id', $currentUserStudent->id)
            ->whereDate('absence_date', $today)
            ->first();

        return view('absence.student-view', compact('currentUserStudent', 'attendanceRecords', 'todayAbsence', 'today'));
    }

    /**
     * Store absence records (DISABLED FOR STUDENTS - Only teachers/scanners can mark attendance).
     */
    public function store(Request $request)
    {
        return response()->json([
            'message' => 'Students cannot mark their own attendance. Teachers scan QR codes to record attendance.'
        ], 403);
    }

    /**
     * Show all absences for all students.
     */
    public function all(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasAnyRole([
            Role::KESISWAAN,
            Role::WALI_KELAS,
            Role::KURIKULUM,
            Role::GURU,
        ])) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        // Get search and filter parameters
        $search = $request->query('search');
        $status = $request->query('status');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Build query with filters
        $query = Absence::with('student.user');

        // Search filter (student name or NIM)
        if ($search) {
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status && in_array($status, ['approved', 'pending', 'rejected'])) {
            $query->where('status', $status);
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('absence_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('absence_date', '<=', $dateTo);
        }

        // Get filtered and paginated absences
        $absences = $query->orderBy('absence_date', 'desc')->paginate(50);

        // Get summary statistics (unfiltered for dashboard cards)
        $totalAbsences = Absence::count();
        $approvedAbsences = Absence::where('status', 'approved')->count();
        $pendingAbsences = Absence::where('status', 'pending')->count();
        $rejectedAbsences = Absence::where('status', 'rejected')->count();

        return view('absence.all', compact(
            'absences',
            'totalAbsences',
            'approvedAbsences',
            'pendingAbsences',
            'rejectedAbsences',
            'search',
            'status',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Show the create form (redirect to index where the form is)
     */
    public function create()
    {
        return redirect()->route('absence.index');
    }

    /**
     * Send notifications to admins and teachers when a student marks absence
     */
    private function sendAbsenceNotifications(Absence $absence, Student $student, bool $isQRSubmission)
    {
        try {
            $submissionMethod = $isQRSubmission ? 'qr' : 'selfie';
            
            // Get all admins and teachers
            $notifiableRoles = [
                Role::KESISWAAN,
                Role::WALI_KELAS,
                Role::KURIKULUM,
                Role::GURU
            ];
            
            // Get users with these roles
            $notifiableUsers = User::whereIn('role_id', function ($query) use ($notifiableRoles) {
                $query->select('id')
                    ->from('roles')
                    ->whereIn('name', $notifiableRoles);
            })->get();
            
            // Send notification to each user
            foreach ($notifiableUsers as $user) {
                $user->notify(new AbsenceSubmittedNotification($absence, $student->user, $submissionMethod));
            }
            
            \Log::info('Absence notifications sent', [
                'absence_id' => $absence->id,
                'student_id' => $student->id,
                'recipients_count' => $notifiableUsers->count(),
                'method' => $submissionMethod
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send absence notifications: ' . $e->getMessage());
        }
    }

}

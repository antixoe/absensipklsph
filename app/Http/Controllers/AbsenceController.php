<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Notifications\AbsenceSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Prevent admins from submitting absences
        if ($currentUser->hasRole(Role::ADMIN)) {
            return redirect()->route('dashboard')->with('error', 'Admins cannot submit absences.');
        }
        
        // Get all non-admin students only
        $adminRoleId = Role::where('name', Role::ADMIN)->value('id');
        $students = Student::with('user')
            ->whereHas('user', function ($query) use ($adminRoleId) {
                $query->where('role_id', '!=', $adminRoleId);
            })
            ->get();
        
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
        // Prevent admins from submitting absences
        $currentUser = Auth::user();
        if ($currentUser->hasRole(Role::ADMIN)) {
            return redirect()->back()->with('error', 'Admins cannot submit absences.');
        }
        
        // Check if this is a QR code submission (method field takes priority)
        $isQRSubmission = $request->input('method') === 'qr';
        
        try {
            if ($isQRSubmission) {
                // QR code submission doesn't require selfie
                $validated = $request->validate([
                    'student_ids' => 'required|array',
                    'qr_code' => 'required|string',
                    'latitude' => 'nullable|numeric',
                    'longitude' => 'nullable|numeric',
                    'ip_address' => 'nullable|string',
                    'location_name' => 'nullable|string',
                    'notes' => 'nullable|string',
                    'absence_date' => 'nullable|date',
                    'absence_time' => 'nullable|date_format:H:i',
                ]);
            } else {
                // Selfie submission requires selfie image
                $validated = $request->validate([
                    'student_ids' => 'required|array',
                    'selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'latitude' => 'nullable|numeric',
                    'longitude' => 'nullable|numeric',
                    'ip_address' => 'nullable|string',
                    'location_name' => 'nullable|string',
                    'notes' => 'nullable|string',
                    'absence_date' => 'nullable|date',
                    'absence_time' => 'nullable|date_format:H:i',
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Absence validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->except(['selfie', '_token']),
                'method' => $request->input('method'),
                'has_qr_code' => $request->has('qr_code'),
                'has_selfie' => $request->hasFile('selfie'),
            ]);
            return back()->withErrors($e->errors())->withInput();
        }

        try {
            // Use real-time current date and time
            $absenceDatetime = Carbon::now();
            
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

            // Check if student already has a record for this date and time
            if ($currentUserStudent) {
                $existingAbsence = Absence::where('student_id', $currentUserStudent->id)
                    ->whereDate('absence_date', $absenceDatetime->toDateString())
                    ->first();
                
                if ($existingAbsence) {
                    $alreadyExists = true;
                }
            }

            // Store the selfie image (only for selfie submissions)
            if (!$isQRSubmission && $request->hasFile('selfie')) {
                $file = $request->file('selfie');
                $selfieFilename = 'selfie_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('absences', $selfieFilename, 'public');
            }

            // Create absence records for each selected student
            foreach ($studentIds as $studentId) {
                $updateData = [
                    'ip_address' => $validated['ip_address'] ?? null,
                    'location_name' => $validated['location_name'] ?? null,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'approved',
                ];
                
                // Add appropriate submission method data
                if ($isQRSubmission) {
                    $updateData['qr_code'] = $validated['qr_code'] ?? null;
                    $updateData['scanned_qr_at'] = Carbon::now();
                } else {
                    $updateData['selfie_path'] = $selfieFilename ? 'absences/' . $selfieFilename : null;
                }
                
                $absence = Absence::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'absence_date' => $absenceDatetime,
                    ],
                    $updateData
                );

                // Log the activity with detailed information
                $method = $isQRSubmission ? 'QR Code' : 'Selfie';
                $logData = [
                    'submission_method' => $method,
                    'student_id' => $studentId,
                    'student_name' => Student::find($studentId)->user->name ?? 'Unknown',
                    'qr_code_used' => $isQRSubmission ? ($validated['qr_code'] ?? 'N/A') : 'N/A',
                    'selfie_saved' => !$isQRSubmission && $selfieFilename ? true : false,
                    'location_name' => $validated['location_name'] ?? 'Not provided',
                    'ip_address' => $validated['ip_address'] ?? 'Not provided',
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                ];
                
                ActivityLoggerService::log(
                    'submitted_absence',
                    'absence',
                    $absence->id,
                    "Submitted absence via $method for {$absenceDatetime->format('Y-m-d H:i')}",
                    [],
                    $logData
                );
                
                \Log::info('Absence submitted', [
                    'user_id' => Auth::id(),
                    'student_id' => $studentId,
                    'absence_id' => $absence->id,
                    'method' => $method,
                    'data' => $logData
                ]);

                // Send notifications to admins and teachers
                $this->sendAbsenceNotifications($absence, $currentUserStudent, $isQRSubmission);
            }

            $successMessage = $alreadyExists 
                ? 'Your absence record has been updated successfully!' 
                : 'Your absence has been recorded successfully!';

            return redirect()->route('absence.index')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Absence submission error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'An error occurred while saving your absence: ' . $e->getMessage())->withInput();
        }
    }



    /**
     * Show all absences for all students.
     */
    public function all(Request $request)
    {
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
                Role::ADMIN,
                Role::HOMEROOM_TEACHER,
                Role::HEAD_OF_DEPARTMENT,
                Role::INDUSTRY_SUPERVISOR
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

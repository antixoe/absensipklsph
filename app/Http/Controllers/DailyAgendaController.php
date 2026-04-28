<?php

namespace App\Http\Controllers;

use App\Models\DailyAgenda;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\Absence;
use App\Notifications\DailyAgendaReviewedNotification;
use App\Notifications\DailyAgendaSubmittedNotification;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyAgendaController extends Controller
{
    /**
     * Check if the current user can review daily agendas.
     */
    private function isInstructorOrAdmin()
    {
        return $this->isAgendaReviewer(Auth::user());
    }

    /**
     * Check if a user is allowed to review daily agendas.
     */
    private function isAgendaReviewer(?User $user): bool
    {
        if (!$user?->role) {
            return false;
        }

        $roleName = $this->normalizeRoleName($user->role->name);

        if (in_array($roleName, array_map(fn (string $role) => $this->normalizeRoleName($role), $this->agendaReviewerRoles()), true)) {
            return true;
        }

        foreach (['pembimbing', 'mentor', 'instructor', 'supervisor', 'teacher', 'principal', 'admin'] as $keyword) {
            if (str_contains($roleName, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize role names so matching stays stable across stored variants.
     */
    private function normalizeRoleName(string $roleName): string
    {
        $normalized = strtolower(trim($roleName));
        $normalized = preg_replace('/[^a-z0-9]+/', '_', $normalized) ?? $normalized;

        return trim($normalized, '_');
    }

    /**
     * Roles that should receive agenda submission notifications.
     */
    private function agendaReviewerRoles(): array
    {
        return [
            'pembimbing',
            'pembimbing_perusahaan',
            'guru_pembimbing',
            'guru_pembimbing_sekolah',
            'instructor',
            Role::INDUSTRY_SUPERVISOR,
            Role::HEAD_OF_DEPARTMENT,
            Role::HOMEROOM_TEACHER,
            Role::SCHOOL_PRINCIPAL,
            Role::ADMIN,
        ];
    }

    /**
     * Get users who should be notified when a student submits a daily agenda.
     */
    private function agendaReviewerUsers()
    {
        return User::with('role')
            ->get()
            ->filter(fn (User $user) => $this->isAgendaReviewer($user));
    }

    /**
     * Notify mentors/teachers/admins about a new agenda submission.
     */
    private function notifyAgendaSubmitted(DailyAgenda $dailyAgenda, Student $student): void
    {
        try {
            foreach ($this->agendaReviewerUsers() as $recipient) {
                $recipient->notify(new DailyAgendaSubmittedNotification($dailyAgenda, $student->user));
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to send daily agenda submission notifications: ' . $e->getMessage(), [
                'daily_agenda_id' => $dailyAgenda->id,
            ]);
        }
    }

    /**
     * Notify the student when their agenda has been reviewed.
     */
    private function notifyAgendaReviewed(
        DailyAgenda $dailyAgenda,
        User $reviewer,
        string $reviewType,
        string $status = 'approved',
        ?string $notes = null
    ): void {
        try {
            $studentUser = $dailyAgenda->student?->user;

            if (!$studentUser) {
                return;
            }

            $studentUser->notify(new DailyAgendaReviewedNotification(
                $dailyAgenda,
                $reviewer,
                $reviewType,
                $status,
                $notes
            ));
        } catch (\Throwable $e) {
            \Log::error('Failed to send daily agenda review notification: ' . $e->getMessage(), [
                'daily_agenda_id' => $dailyAgenda->id,
                'reviewer_id' => $reviewer->id,
                'status' => $status,
            ]);
        }
    }

    /**
     * Show all daily agendas or create a new one.
     */
    public function index()
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();
        
        // If this user can review agendas, show all student agendas
        if ($canReviewAgenda) {
            return $this->indexForInstructors($canReviewAgenda);
        }

        // For students, show only their own agendas
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as a student.');
        }

        $attendanceContext = $this->getAgendaAttendanceContext($currentStudent);
        $agendas = DailyAgenda::with(['student.user', 'completedBy'])
            ->where('student_id', $currentStudent->id)
            ->orderByDesc('agenda_date')
            ->paginate(10);

        return view('daily-agenda.index', compact('agendas', 'currentStudent'))
            ->with([
                'canCreateAgenda' => $attendanceContext['canCreateAgenda'],
                'agendaBlockMessage' => $attendanceContext['blockMessage'],
                'canReviewAgenda' => $canReviewAgenda,
            ]);
    }

    /**
     * Show all daily agendas for reviewers.
     */
    public function indexForInstructors(?bool $canReviewAgenda = null)
    {
        $currentUser = Auth::user();
        $canReviewAgenda ??= $this->isInstructorOrAdmin();
        
        // Get all agendas with student information, ordered by date
        $agendasQuery = DailyAgenda::with(['student.user', 'completedBy'])
            ->orderByDesc('agenda_date');

        // Optionally filter by date range
        if (request('date_from')) {
            $agendasQuery->whereDate('agenda_date', '>=', request('date_from'));
        }
        if (request('date_to')) {
            $agendasQuery->whereDate('agenda_date', '<=', request('date_to'));
        }

        // Optionally filter by student
        if (request('student_id')) {
            $agendasQuery->where('student_id', request('student_id'));
        }

        $agendas = $agendasQuery->paginate(15);
        
        // Get list of students for filter dropdown
        $students = Student::with('user')->orderBy('user_id')->get();

        return view('daily-agenda.index-instructor', compact('agendas', 'students', 'currentUser', 'canReviewAgenda'));
    }

    /**
     * Show the form to create a new daily agenda.
     */
    public function create()
    {
        $currentUser = Auth::user();
        
        // Only students can create agendas
        if ($this->isInstructorOrAdmin()) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Only students can create agendas.');
        }

        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as a student.');
        }

        $today = Carbon::today()->toDateString();
        $existingAgenda = $this->getAgendaForDate($currentStudent->id, $today);

        if ($existingAgenda) {
            return redirect()->route('daily-agenda.show', $existingAgenda->id)
                ->with('info', 'You already have an agenda for today. Only one agenda per day is allowed.');
        }

        $attendanceContext = $this->getAgendaAttendanceContext($currentStudent);

        if (!$attendanceContext['canCreateAgenda']) {
            return redirect()->route('daily-agenda.index')
                ->with('agenda_warning', $attendanceContext['blockMessage']);
        }

        $timeIn = $attendanceContext['timeIn'];
        $timeOut = $attendanceContext['timeOut'];

        // Check if user is a pembimbing (company mentor)
        $isPembimbing = $currentUser->hasRole('pembimbing');

        return view('daily-agenda.create', compact('currentStudent', 'timeIn', 'timeOut', 'isPembimbing'));
    }

    /**
     * Store a new daily agenda.
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as a student.');
        }

        $validated = $request->validate([
            'agenda_date' => 'nullable|date',
            'work_plan' => 'nullable|array|size:5',
            'work_realization' => 'nullable|array|size:5',
            'special_assignment' => 'nullable|string',
            'problems_found' => 'nullable|string',
            'assessment_items' => 'nullable|array|size:5',
            'notes' => 'nullable|string',
        ]);

        // Prepare work plans and realizations as arrays
        $workPlan = isset($validated['work_plan']) ? array_values(array_filter($validated['work_plan'], fn($item) => !empty($item))) : [];
        $workRealization = isset($validated['work_realization']) ? array_values(array_filter($validated['work_realization'], fn($item) => !empty($item))) : [];

        $agendaDate = Carbon::today()->toDateString();
        $existingAgenda = $this->getAgendaForDate($currentStudent->id, $agendaDate);

        if ($existingAgenda) {
            return redirect()->route('daily-agenda.show', $existingAgenda->id)
                ->with('info', 'You already have an agenda for today. Only one agenda per day is allowed.');
        }

        $attendanceContext = $this->getAgendaAttendanceContext($currentStudent);

        if (!$attendanceContext['canCreateAgenda']) {
            return redirect()->route('daily-agenda.index')
                ->with('agenda_warning', $attendanceContext['blockMessage']);
        }

        $timeIn = $attendanceContext['timeIn'];
        $timeOut = $attendanceContext['timeOut'];

        // Prepare daily assessment
        $dailyAssessment = [];
        $assessmentLabels = ['Senyum', 'Keramahan', 'Penampilan', 'Komunikasi', 'Realisasi Kerja'];
        foreach ($assessmentLabels as $index => $label) {
            $dailyAssessment[] = [
                'label' => $label,
                'value' => $validated['assessment_items'][$index] ?? null,
            ];
        }

        $agenda = DailyAgenda::create([
            'student_id' => $currentStudent->id,
            'agenda_date' => $agendaDate,
            'time_in' => $timeIn,
            'time_out' => $timeOut,
            'work_plan' => $workPlan,
            'work_realization' => $workRealization,
            'special_assignment' => $validated['special_assignment'],
            'problems_found' => $validated['problems_found'],
            'daily_assessment' => $dailyAssessment,
            'notes' => $validated['notes'],
            'submitted_at' => now(),
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'created_daily_agenda',
            'daily_agenda',
            $agenda->id,
            'Created daily agenda for ' . $agenda->agenda_date?->format('Y-m-d') ?? 'date not set',
            [],
            [
                'agenda_date' => $agenda->agenda_date,
                'time_in' => $agenda->time_in,
                'time_out' => $agenda->time_out,
            ]
        );

        $this->notifyAgendaSubmitted($agenda, $currentStudent);

        return redirect()->route('daily-agenda.show', $agenda->id)
            ->with('success', 'Daily agenda created successfully.');
    }

    /**
     * Get an existing agenda for a specific date.
     */
    private function getAgendaForDate(int $studentId, string $date): ?DailyAgenda
    {
        return DailyAgenda::where('student_id', $studentId)
            ->whereDate('agenda_date', $date)
            ->first();
    }

    /**
     * Get today's attendance data and agenda creation state for a student.
     */
    private function getAgendaAttendanceContext(Student $student): array
    {
        $todayAbsence = Absence::where('student_id', $student->id)
            ->whereDate('absence_date', Carbon::today())
            ->orderBy('scanned_qr_at', 'desc')
            ->first();

        $timeIn = null;
        $timeOut = null;

        if ($todayAbsence) {
            $timeIn = $todayAbsence->scanned_qr_at
                ? $todayAbsence->scanned_qr_at->format('H:i')
                : (optional($todayAbsence->created_at)->format('H:i') ?? 'N/A');

            if ($todayAbsence->scanned_qr_out_at) {
                $timeOut = $todayAbsence->scanned_qr_out_at->format('H:i');
            }
        }

        $blockMessage = null;

        if (!$todayAbsence) {
            $blockMessage = 'Siswa perlu melakukan absensi masuk terlebih dahulu sebelum membuat agenda harian.';
        } elseif (!$timeOut) {
            $blockMessage = 'Siswa perlu melakukan absen pulang terlebih dahulu sebelum membuat agenda harian.';
        }

        return [
            'todayAbsence' => $todayAbsence,
            'timeIn' => $timeIn,
            'timeOut' => $timeOut,
            'blockMessage' => $blockMessage,
            'canCreateAgenda' => $blockMessage === null,
        ];
    }

    /**
     * Show a single daily agenda.
     */
    public function show(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();
        
        // Check if user can review agendas
        if ($canReviewAgenda) {
            // Reviewers can view any agenda
            return view('daily-agenda.show', compact('dailyAgenda', 'currentUser', 'canReviewAgenda'));
        }

        // For students, check if it's their own agenda
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        // Check authorization
        if ($currentStudent && $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('daily-agenda.show', compact('dailyAgenda', 'currentUser', 'canReviewAgenda'));
    }

    /**
     * Show the edit form for agenda status updates.
     */
    public function edit(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();

        if (!$canReviewAgenda) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa mengubah status agenda.');
        }

        $dailyAgenda->load(['student.user', 'completedBy']);

        return view('daily-agenda.edit', compact('dailyAgenda', 'currentUser', 'canReviewAgenda'));
    }

    /**
     * Update agenda approval, assessment, and verification status.
     */
    public function update(Request $request, DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();

        if (!$canReviewAgenda) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa mengubah status agenda.');
        }

        $validated = $request->validate([
            'company_mentor_approved' => 'nullable|boolean',
            'school_teacher_approved' => 'nullable|boolean',
            'completion_status' => 'required|in:pending,approved,rejected',
            'instructor_notes' => 'nullable|string|max:1000',
            'daily_assessment' => 'nullable|array|size:5',
            'daily_assessment.*' => 'nullable|in:Baik,Kurang',
        ]);

        $originalCompanyMentorApproved = (bool) $dailyAgenda->company_mentor_approved;
        $originalSchoolTeacherApproved = (bool) $dailyAgenda->school_teacher_approved;
        $originalCompletionStatus = $dailyAgenda->completion_status;
        $companyMentorApproved = $request->boolean('company_mentor_approved');
        $schoolTeacherApproved = $request->boolean('school_teacher_approved');
        $completionStatus = $validated['completion_status'];
        $isCompleted = $completionStatus !== 'pending';
        $assessmentLabels = ['Senyum', 'Keramahan', 'Penampilan', 'Komunikasi', 'Realisasi Kerja'];
        $submittedAssessments = $request->input('daily_assessment', []);
        $dailyAssessment = [];

        foreach ($assessmentLabels as $index => $label) {
            $submittedValue = $submittedAssessments[$index] ?? null;
            $currentValue = $dailyAgenda->daily_assessment[$index]['value'] ?? null;
            $normalizedValue = in_array($submittedValue, ['Baik', 'Kurang'], true)
                ? $submittedValue
                : $currentValue;

            $dailyAssessment[] = [
                'label' => $label,
                'value' => $normalizedValue,
            ];
        }

        $dailyAgenda->update([
            'company_mentor_approved' => $companyMentorApproved,
            'company_mentor_approved_at' => $companyMentorApproved
                ? ($dailyAgenda->company_mentor_approved_at ?? now())
                : null,
            'school_teacher_approved' => $schoolTeacherApproved,
            'school_teacher_approved_at' => $schoolTeacherApproved
                ? ($dailyAgenda->school_teacher_approved_at ?? now())
                : null,
            'is_completed' => $isCompleted,
            'completed_by' => $isCompleted ? $currentUser->id : null,
            'completed_at' => $isCompleted ? now() : null,
            'instructor_notes' => $validated['instructor_notes'] ?? null,
            'completion_status' => $completionStatus,
            'daily_assessment' => $dailyAssessment,
        ]);

        ActivityLoggerService::log(
            'updated_daily_agenda_status',
            'daily_agenda',
            $dailyAgenda->id,
            'Updated approval, assessment, and verification status for ' . ($dailyAgenda->student?->user?->name ?? 'Unknown'),
            [
                'previous_company_mentor_approved' => $originalCompanyMentorApproved,
                'previous_school_teacher_approved' => $originalSchoolTeacherApproved,
                'previous_completion_status' => $originalCompletionStatus,
                'previous_daily_assessment' => $dailyAgenda->daily_assessment ?? [],
            ],
            [
                'company_mentor_approved' => $companyMentorApproved,
                'school_teacher_approved' => $schoolTeacherApproved,
                'completion_status' => $completionStatus,
                'instructor_notes' => $validated['instructor_notes'] ?? null,
                'daily_assessment' => $dailyAssessment,
                'updated_by' => $currentUser->name,
            ]
        );

        if (!$originalCompanyMentorApproved && $companyMentorApproved) {
            $this->notifyAgendaReviewed(
                $dailyAgenda,
                $currentUser,
                'persetujuan pembimbing perusahaan'
            );
        }

        if (!$originalSchoolTeacherApproved && $schoolTeacherApproved) {
            $this->notifyAgendaReviewed(
                $dailyAgenda,
                $currentUser,
                'persetujuan guru pembimbing sekolah'
            );
        }

        if ($originalCompletionStatus !== $completionStatus && $isCompleted) {
            $this->notifyAgendaReviewed(
                $dailyAgenda,
                $currentUser,
                'verifikasi PKL',
                $completionStatus,
                $validated['instructor_notes'] ?? null
            );
        }

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Daily agenda status updated successfully.');
    }

    /**
     * Mark a daily agenda as completed (approved) by instructor/admin.
     */
    public function markComplete(Request $request, DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();

        if (!$canReviewAgenda) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa menyetujui atau menolak agenda.');
        }

        $validated = $request->validate([
            'completion_status' => 'required|in:approved,rejected',
            'instructor_notes' => 'nullable|string|max:1000',
        ]);

        $dailyAgenda->update([
            'is_completed' => true,
            'completed_by' => $currentUser->id,
            'completed_at' => now(),
            'instructor_notes' => $validated['instructor_notes'],
            'completion_status' => $validated['completion_status'],
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'marked_daily_agenda_complete',
            'daily_agenda',
            $dailyAgenda->id,
            'Marked daily agenda as ' . $validated['completion_status'] . ' for ' . ($dailyAgenda->student?->user?->name ?? 'Unknown'),
            [],
            [
                'status' => $validated['completion_status'],
                'instructor_notes' => $validated['instructor_notes'],
            ]
        );

        $this->notifyAgendaReviewed(
            $dailyAgenda,
            $currentUser,
            'verifikasi PKL',
            $validated['completion_status'],
            $validated['instructor_notes'] ?? null
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Daily agenda marked as ' . $validated['completion_status'] . ' successfully.');
    }

    /**
     * Unmark a daily agenda (revert completion status).
     */
    public function unmarkComplete(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $canReviewAgenda = $this->isInstructorOrAdmin();

        if (!$canReviewAgenda) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa membatalkan verifikasi agenda.');
        }

        $previousStatus = $dailyAgenda->completion_status;

        $dailyAgenda->update([
            'is_completed' => false,
            'completed_by' => null,
            'completed_at' => null,
            'instructor_notes' => null,
            'completion_status' => 'pending',
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'unmarked_daily_agenda_complete',
            'daily_agenda',
            $dailyAgenda->id,
            'Unmarked daily agenda (was ' . $previousStatus . ') for ' . ($dailyAgenda->student?->user?->name ?? 'Unknown'),
            ['previous_status' => $previousStatus],
            []
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Daily agenda completion status has been reset.');
    }

    /**
     * Student approves their own agenda.
     */
    public function approveStudent(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        // Only the student can approve their own agenda
        if (!$currentStudent || $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Only the student can approve their own agenda.');
        }

        $dailyAgenda->update([
            'student_approved' => true,
            'student_approved_at' => now(),
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'student_approved_daily_agenda',
            'daily_agenda',
            $dailyAgenda->id,
            'Student approved their daily agenda',
            [],
            ['student_name' => $currentStudent->user?->name ?? 'Unknown']
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Agenda berhasil disetujui oleh Anda.');
    }

    /**
     * Company mentor (instructor) approves the agenda.
     */
    public function approveCompanyMentor(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();

        if (!$this->isInstructorOrAdmin()) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa menyetujui agenda.');
        }

        $dailyAgenda->update([
            'company_mentor_approved' => true,
            'company_mentor_approved_at' => now(),
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'company_mentor_approved_daily_agenda',
            'daily_agenda',
            $dailyAgenda->id,
            'Company mentor approved daily agenda for ' . ($dailyAgenda->student?->user?->name ?? 'Unknown'),
            [],
            ['approved_by' => $currentUser->name]
        );

        $this->notifyAgendaReviewed(
            $dailyAgenda,
            $currentUser,
            'persetujuan pembimbing perusahaan'
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Agenda disetujui oleh Pembimbing Perusahaan.');
    }

    /**
     * School teacher (instructor) approves the agenda.
     */
    public function approveSchoolTeacher(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();

        if (!$this->isInstructorOrAdmin()) {
            return redirect()->route('daily-agenda.show', $dailyAgenda->id)
                ->with('error', 'Hanya verifikator agenda yang bisa menyetujui agenda.');
        }

        $dailyAgenda->update([
            'school_teacher_approved' => true,
            'school_teacher_approved_at' => now(),
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'school_teacher_approved_daily_agenda',
            'daily_agenda',
            $dailyAgenda->id,
            'School teacher approved daily agenda for ' . ($dailyAgenda->student?->user?->name ?? 'Unknown'),
            [],
            ['approved_by' => $currentUser->name]
        );

        $this->notifyAgendaReviewed(
            $dailyAgenda,
            $currentUser,
            'persetujuan guru pembimbing sekolah'
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Agenda disetujui oleh Guru Pembimbing Sekolah.');
    }
}

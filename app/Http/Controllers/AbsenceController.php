<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\QRCode;
use App\Models\RombonganBelajar;
use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLoggerService;
use App\Notifications\AbsenceSubmittedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    /**
     * Check if the current user is a homeroom teacher.
     */
    private function isHomeroomTeacher(?User $user): bool
    {
        return $user?->hasRole(Role::WALI_KELAS) ?? false;
    }

    /**
     * Check if the current user is Kesiswaan.
     */
    private function isKesiswaan(?User $user): bool
    {
        return $user?->hasRole(Role::KESISWAAN) ?? false;
    }

    /**
     * Resolve the homeroom teacher's assigned scope text.
     *
     * The current codebase stores class scope on the instructor profile
     * (`department` or `position`) and also supports a dedicated `rombel_id`.
     */
    private function getHomeroomTeacherScope(?User $user): ?string
    {
        if (!$this->isHomeroomTeacher($user)) {
            return null;
        }

        $scope = trim((string) data_get($user, 'rombel.name', ''));

        if ($scope !== '') {
            return $scope;
        }

        $scope = trim((string) ($user?->instructor?->department ?? $user?->instructor?->position ?? ''));

        return $scope !== '' ? $scope : null;
    }

    /**
     * Normalize scope text for flexible matching.
     */
    private function normalizeScopeText(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));
        $normalized = preg_replace('/[^a-z0-9]+/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }

    /**
     * Build a short acronym from a scope string.
     */
    private function makeScopeAcronym(string $value): string
    {
        $stopWords = [
            'department',
            'teacher',
            'homeroom',
            'class',
            'program',
            'student',
            'school',
            'major',
            'rombel',
        ];

        $parts = array_filter(explode(' ', $this->normalizeScopeText($value)));
        $letters = array_map(function (string $part) use ($stopWords) {
            return in_array($part, $stopWords, true) ? '' : substr($part, 0, 1);
        }, $parts);

        return implode('', array_filter($letters));
    }

    /**
     * Check whether a teacher scope matches a student class label.
     */
    private function scopeMatchesStudentClass(string $scope, ?string $studentClass): bool
    {
        $normalizedScope = $this->normalizeScopeText($scope);
        $normalizedStudentClass = $this->normalizeScopeText($studentClass);

        if ($normalizedScope === '' || $normalizedStudentClass === '') {
            return false;
        }

        if ($normalizedScope === $normalizedStudentClass) {
            return true;
        }

        if (str_contains($normalizedStudentClass, $normalizedScope) || str_contains($normalizedScope, $normalizedStudentClass)) {
            return true;
        }

        $scopeAcronym = $this->makeScopeAcronym($normalizedScope);
        $studentAcronym = $this->makeScopeAcronym($normalizedStudentClass);

        return $scopeAcronym !== '' && $scopeAcronym === $studentAcronym;
    }

    /**
     * Apply the current user's class scope to an absence query.
     */
    private function scopeAbsencesForUser(Builder $query, ?User $user): Builder
    {
        if (!$this->isHomeroomTeacher($user)) {
            return $query;
        }

        $rombelId = data_get($user, 'rombel_id');
        if (!empty($rombelId)) {
            return $query->whereHas('student', function (Builder $studentQuery) use ($rombelId) {
                $studentQuery->where('rombel_id', $rombelId);
            });
        }

        $scope = $this->getHomeroomTeacherScope($user);
        if (!$scope) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('student', function (Builder $studentQuery) use ($scope) {
            $studentQuery->where(function (Builder $classQuery) use ($scope) {
                $classQuery->where('major', 'like', '%' . $scope . '%')
                    ->orWhereHas('rombel', function (Builder $rombelQuery) use ($scope) {
                        $rombelQuery->where('name', 'like', '%' . $scope . '%');
                    });
            });
        });
    }

    /**
     * Apply shared search filters to the query.
     */
    private function applySearchFilters(Builder $query, Request $request): Builder
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($search !== '') {
            $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery->whereHas('student.user', function (Builder $userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('student', function (Builder $studentQuery) use ($search) {
                    $studentQuery->where('nim', 'like', '%' . $search . '%')
                        ->orWhere('major', 'like', '%' . $search . '%');
                });
            });
        }

        if ($status !== '' && in_array($status, ['approved', 'pending', 'rejected', 'present'], true)) {
            $query->where('status', $status);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('absence_date', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $query->whereDate('absence_date', '<=', $dateTo);
        }

        return $query;
    }

    /**
     * Build a stable filter key for a grouped collection row.
     */
    private function buildGroupKey(Absence $absence): string
    {
        $rombelId = data_get($absence, 'student.rombel_id');
        if (!empty($rombelId)) {
            return 'rombel:' . $rombelId;
        }

        $major = trim((string) data_get($absence, 'student.major', ''));
        if ($major !== '') {
            return 'major:' . $major;
        }

        return 'ungrouped:0';
    }

    /**
     * Build a human readable label for a grouped absence row.
     */
    private function buildGroupLabel(Absence $absence): string
    {
        $rombelName = trim((string) data_get($absence, 'student.rombel.name', ''));
        if ($rombelName !== '') {
            return $rombelName;
        }

        $major = trim((string) data_get($absence, 'student.major', ''));
        if ($major !== '') {
            return $major;
        }

        return 'Belum ditetapkan';
    }

    /**
     * Convert a grouped absence collection into view-friendly structures.
     */
    private function buildGroupedAbsences($absences)
    {
        return $absences
            ->groupBy(fn (Absence $absence) => $this->buildGroupKey($absence))
            ->map(function ($records, string $groupKey) {
                $first = $records->first();

                return [
                    'key' => $groupKey,
                    'label' => $first ? $this->buildGroupLabel($first) : 'Belum ditetapkan',
                    'count' => $records->count(),
                    'approved' => $records->where('status', 'approved')->count(),
                    'pending' => $records->where('status', 'pending')->count(),
                    'rejected' => $records->where('status', 'rejected')->count(),
                    'records' => $records,
                ];
            })
            ->sortBy('label')
            ->values();
    }

    /**
     * Compute summary statistics from a collection of absences.
     */
    private function buildSummaryStats($absences): array
    {
        return [
            'total' => $absences->count(),
            'present' => $absences->where('status', 'present')->count(),
            'approved' => $absences->where('status', 'approved')->count(),
            'pending' => $absences->where('status', 'pending')->count(),
            'rejected' => $absences->where('status', 'rejected')->count(),
            'approvalRate' => $absences->count() > 0
                ? round(($absences->where('status', 'approved')->count() / $absences->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Apply a user-selected group filter.
     */
    private function applyGroupFilter(Builder $query, ?string $groupFilter): Builder
    {
        $groupFilter = trim((string) $groupFilter);

        if ($groupFilter === '') {
            return $query;
        }

        if (Str::startsWith($groupFilter, 'rombel:')) {
            $rombelId = (int) Str::after($groupFilter, 'rombel:');

            return $query->whereHas('student', function (Builder $studentQuery) use ($rombelId) {
                $studentQuery->where('rombel_id', $rombelId);
            });
        }

        if (Str::startsWith($groupFilter, 'major:')) {
            $major = Str::after($groupFilter, 'major:');

            return $query->whereHas('student', function (Builder $studentQuery) use ($major) {
                $studentQuery->where('major', $major);
            });
        }

        return $query;
    }

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
        Gate::authorize('viewAbsenceData');

        $isKesiswaan = $this->isKesiswaan($currentUser);
        $isWaliKelas = $this->isHomeroomTeacher($currentUser);
        $rombelLabel = $this->getHomeroomTeacherScope($currentUser);
        $selectedGroup = (string) $request->query('rombel_filter', '');

        $baseQuery = Absence::query()
            ->with(['student.user', 'student.rombel'])
            ->orderByDesc('absence_date');

        $baseQuery = $this->scopeAbsencesForUser($baseQuery, $currentUser);
        $baseQuery = $this->applySearchFilters($baseQuery, $request);

        if ($isKesiswaan) {
            $matchingAbsences = (clone $baseQuery)->get();
            $groupedAbsences = $this->buildGroupedAbsences($matchingAbsences);
            $summaryStats = $this->buildSummaryStats($matchingAbsences);

            $rombelFilters = $groupedAbsences->map(function (array $group) {
                return [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'count' => $group['count'],
                ];
            })->values();

            $selectedGroupLabel = null;
            $tableAbsences = null;

            if ($selectedGroup !== '') {
                $selectedGroupItem = $groupedAbsences->firstWhere('key', $selectedGroup);
                $selectedGroupLabel = $selectedGroupItem['label'] ?? null;
                $tableAbsences = $this->applyGroupFilter((clone $baseQuery), $selectedGroup)
                    ->paginate(25)
                    ->withQueryString();
            }

            return view('absence.all', [
                'absences' => $tableAbsences,
                'groupedAbsences' => $groupedAbsences,
                'rombelFilters' => $rombelFilters,
                'selectedGroup' => $selectedGroup,
                'selectedGroupLabel' => $selectedGroupLabel,
                'isKesiswaan' => true,
                'isWaliKelas' => false,
                'rombelLabel' => null,
                'summaryStats' => $summaryStats,
                'search' => $request->query('search', ''),
                'status' => $request->query('status', ''),
                'dateFrom' => $request->query('date_from', ''),
                'dateTo' => $request->query('date_to', ''),
            ]);
        }

        $absences = (clone $baseQuery)
            ->paginate(25)
            ->withQueryString();

        $summaryStats = $this->buildSummaryStats($absences->getCollection());
        $groupedAbsences = $this->buildGroupedAbsences($absences->getCollection());

        return view('absence.all', [
            'absences' => $absences,
            'groupedAbsences' => $groupedAbsences,
            'rombelFilters' => collect(),
            'selectedGroup' => null,
            'selectedGroupLabel' => null,
            'isKesiswaan' => false,
            'isWaliKelas' => $isWaliKelas,
            'rombelLabel' => $rombelLabel,
            'summaryStats' => $summaryStats,
            'search' => $request->query('search', ''),
            'status' => $request->query('status', ''),
            'dateFrom' => $request->query('date_from', ''),
            'dateTo' => $request->query('date_to', ''),
        ]);
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

<?php

namespace App\Http\Controllers;

use App\Models\DailyAgenda;
use App\Models\Student;
use App\Models\Absence;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DailyAgendaController extends Controller
{
    /**
     * Show all daily agendas or create a new one.
     */
    public function index()
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as a student.');
        }

        $agendas = DailyAgenda::where('student_id', $currentStudent->id)
            ->orderByDesc('agenda_date')
            ->paginate(10);

        return view('daily-agenda.index', compact('agendas', 'currentStudent'));
    }

    /**
     * Show the form to create a new daily agenda.
     */
    public function create()
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent) {
            return redirect()->route('dashboard')->with('error', 'You are not registered as a student.');
        }

        $today = Carbon::today()->toDateString();
        $existingAgenda = DailyAgenda::where('student_id', $currentStudent->id)
            ->whereDate('agenda_date', $today)
            ->first();

        if ($existingAgenda) {
            return redirect()->route('daily-agenda.edit', $existingAgenda->id)
                ->with('info', 'You already have an agenda for today. Edit it below.');
        }

        // Fetch today's absence to get check-in and check-out times
        // Try to get scanned_qr_at first (from QR code), then fall back to created_at (from selfie)
        $todayAbsence = Absence::where('student_id', $currentStudent->id)
            ->whereDate('absence_date', Carbon::today())
            ->orderBy('scanned_qr_at', 'desc')
            ->first();

        $timeIn = null;
        $timeOut = null;
        
        if ($todayAbsence) {
            // Prefer scanned_qr_at (QR code check-in time)
            if ($todayAbsence->scanned_qr_at) {
                $timeIn = $todayAbsence->scanned_qr_at->format('H:i');
            } else {
                // Fallback to created_at (time of submission, for selfie-based absences)
                $timeIn = $todayAbsence->created_at->format('H:i');
            }
            
            // Check if checkout time exists
            if ($todayAbsence->scanned_qr_out_at) {
                $timeOut = $todayAbsence->scanned_qr_out_at->format('H:i');
            }
        }

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
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
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
            'agenda_date' => $validated['agenda_date'],
            'time_in' => $validated['time_in'],
            'time_out' => $validated['time_out'],
            'work_plan' => $workPlan,
            'work_realization' => $workRealization,
            'special_assignment' => $validated['special_assignment'],
            'problems_found' => $validated['problems_found'],
            'daily_assessment' => $dailyAssessment,
            'notes' => $validated['notes'],
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

        return redirect()->route('daily-agenda.show', $agenda->id)
            ->with('success', 'Daily agenda created successfully.');
    }

    /**
     * Show a single daily agenda.
     */
    public function show(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        // Check authorization
        if ($currentStudent && $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Unauthorized access.');
        }

        return view('daily-agenda.show', compact('dailyAgenda'));
    }

    /**
     * Show the form to edit a daily agenda.
     */
    public function edit(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent || $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Unauthorized access.');
        }

        // Fetch absence record for the same date to get check-in and check-out times
        $absence = Absence::where('student_id', $currentStudent->id)
            ->whereDate('absence_date', $dailyAgenda->agenda_date)
            ->orderBy('scanned_qr_at', 'desc')
            ->first();

        $timeIn = null;
        $timeOut = null;
        
        if ($absence) {
            if ($absence->scanned_qr_at) {
                $timeIn = $absence->scanned_qr_at->format('H:i');
            } else {
                $timeIn = $dailyAgenda->time_in;
            }
            
            if ($absence->scanned_qr_out_at) {
                $timeOut = $absence->scanned_qr_out_at->format('H:i');
            }
        } else {
            // If no absence found, use the existing times from the agenda
            $timeIn = $dailyAgenda->time_in;
            $timeOut = $dailyAgenda->time_out;
        }

        // Check if user is a pembimbing (company mentor)
        $isPembimbing = $currentUser->hasRole('pembimbing');

        return view('daily-agenda.edit', compact('dailyAgenda', 'currentStudent', 'timeIn', 'timeOut', 'isPembimbing'));
    }

    /**
     * Update a daily agenda.
     */
    public function update(Request $request, DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent || $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'agenda_date' => 'nullable|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'work_plan' => 'nullable|array|size:5',
            'work_realization' => 'nullable|array|size:5',
            'special_assignment' => 'nullable|string',
            'problems_found' => 'nullable|string',
            'assessment_items' => 'nullable|array|size:5',
            'notes' => 'nullable|string',
        ]);

        $workPlan = isset($validated['work_plan']) ? array_values(array_filter($validated['work_plan'], fn($item) => !empty($item))) : [];
        $workRealization = isset($validated['work_realization']) ? array_values(array_filter($validated['work_realization'], fn($item) => !empty($item))) : [];

        $dailyAssessment = [];
        $assessmentLabels = ['Senyum', 'Keramahan', 'Penampilan', 'Komunikasi', 'Realisasi Kerja'];
        foreach ($assessmentLabels as $index => $label) {
            $dailyAssessment[] = [
                'label' => $label,
                'value' => $validated['assessment_items'][$index] ?? null,
            ];
        }

        $dailyAgenda->update([
            'agenda_date' => $validated['agenda_date'],
            'time_in' => $validated['time_in'],
            'time_out' => $validated['time_out'],
            'work_plan' => $workPlan,
            'work_realization' => $workRealization,
            'special_assignment' => $validated['special_assignment'],
            'problems_found' => $validated['problems_found'],
            'daily_assessment' => $dailyAssessment,
            'notes' => $validated['notes'],
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'updated_daily_agenda',
            'daily_agenda',
            $dailyAgenda->id,
            'Updated daily agenda for ' . $dailyAgenda->agenda_date?->format('Y-m-d') ?? 'date not set',
            [],
            [
                'agenda_date' => $dailyAgenda->agenda_date,
                'time_in' => $dailyAgenda->time_in,
                'time_out' => $dailyAgenda->time_out,
            ]
        );

        return redirect()->route('daily-agenda.show', $dailyAgenda->id)
            ->with('success', 'Daily agenda updated successfully.');
    }

    /**
     * Delete a daily agenda.
     */
    public function destroy(DailyAgenda $dailyAgenda)
    {
        $currentUser = Auth::user();
        $currentStudent = Student::where('user_id', $currentUser->id)->first();

        if (!$currentStudent || $dailyAgenda->student_id !== $currentStudent->id) {
            return redirect()->route('daily-agenda.index')
                ->with('error', 'Unauthorized access.');
        }

        $dailyAgenda->delete();

        // Log the activity
        ActivityLoggerService::log(
            'deleted_daily_agenda',
            'daily_agenda',
            $dailyAgenda->id,
            'Deleted daily agenda for ' . $dailyAgenda->agenda_date?->format('Y-m-d') ?? 'date not set',
            [
                'agenda_date' => $dailyAgenda->agenda_date,
                'time_in' => $dailyAgenda->time_in,
                'time_out' => $dailyAgenda->time_out,
            ],
            []
        );

        return redirect()->route('daily-agenda.index')
            ->with('success', 'Daily agenda deleted successfully.');
    }
}


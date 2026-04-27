<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyAgenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'agenda_date',
        'time_in',
        'time_out',
        'work_plan',
        'work_realization',
        'special_assignment',
        'problems_found',
        'daily_assessment',
        'notes',
        'submitted_at',
        'is_completed',
        'completed_by',
        'completed_at',
        'instructor_notes',
        'completion_status',
        'student_approved',
        'student_approved_at',
        'company_mentor_approved',
        'company_mentor_approved_at',
        'school_teacher_approved',
        'school_teacher_approved_at',
    ];

    protected $casts = [
        'work_plan' => 'array',
        'work_realization' => 'array',
        'daily_assessment' => 'array',
        'agenda_date' => 'date',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
        'student_approved' => 'boolean',
        'student_approved_at' => 'datetime',
        'company_mentor_approved' => 'boolean',
        'company_mentor_approved_at' => 'datetime',
        'school_teacher_approved' => 'boolean',
        'school_teacher_approved_at' => 'datetime',
    ];

    /**
     * Get the student associated with this daily agenda.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor who marked this agenda as completed.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}


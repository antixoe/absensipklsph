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
        'student_signature_path',
        'company_mentor_signature_path',
        'school_teacher_signature_path',
        'submitted_at',
    ];

    protected $casts = [
        'work_plan' => 'array',
        'work_realization' => 'array',
        'daily_assessment' => 'array',
        'agenda_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the student associated with this daily agenda.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}


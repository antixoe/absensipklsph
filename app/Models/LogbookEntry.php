<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'entry_date',
        'title',
        'description',
        'achievements',
        'challenges',
        'learning_outcomes',
        'hours_worked',
        'status',
        'instructor_id',
        'instructor_feedback',
        'approved_date',
    ];

    protected $dates = [
        'entry_date',
        'approved_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the student associated with this logbook entry.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor associated with this logbook entry.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Scope to get approved entries.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get pending entries.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }
}

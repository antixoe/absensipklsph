<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'activity_name',
        'description',
        'activity_date',
        'start_time',
        'end_time',
        'duration_hours',
        'category',
        'status',
        'deliverables',
        'assigned_by',
    ];

    protected $dates = [
        'activity_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the student associated with this activity.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor who assigned this activity.
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'assigned_by');
    }

    /**
     * Scope to get pending activities.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get completed activities.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

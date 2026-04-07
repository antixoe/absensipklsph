<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'attendance_date',
        'check_in_time',
        'check_out_time',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_photo',
        'check_out_photo',
        'status',
        'notes',
        'qr_code',
    ];

    protected $dates = [
        'attendance_date',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'check_in_latitude' => 'float',
        'check_in_longitude' => 'float',
        'check_out_latitude' => 'float',
        'check_out_longitude' => 'float',
    ];

    /**
     * Get the student associated with this attendance.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Scope to get today's attendance.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    /**
     * Scope to get this week's attendance.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('attendance_date', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope to get this month's attendance.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereBetween('attendance_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }
}

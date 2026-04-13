<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'absence_date',
        'selfie_path',
        'ip_address',
        'latitude',
        'longitude',
        'location_name',
        'notes',
        'status',
        'approved_signature',
        'approved_notes',
        'approved_at',
        'approved_by',
    ];

    protected $dates = [
        'absence_date',
        'approved_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'absence_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the student associated with this absence.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who approved this absence.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'document_name',
        'description',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'status',
        'upload_date',
        'reviewed_by',
        'review_notes',
        'review_date',
    ];

    protected $dates = [
        'upload_date',
        'review_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the student associated with this document.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the instructor who reviewed this document.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'reviewed_by');
    }

    /**
     * Scope to get approved documents.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get pending documents.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

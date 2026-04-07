<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'phone',
        'department',
        'position',
    ];

    /**
     * Get the user associated with this instructor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the logbook entries reviewed by this instructor.
     */
    public function logbookEntries(): HasMany
    {
        return $this->hasMany(LogbookEntry::class);
    }

    /**
     * Get the activities assigned by this instructor.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'assigned_by');
    }

    /**
     * Get the documents reviewed by this instructor.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'reviewed_by');
    }
}

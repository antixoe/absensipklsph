<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QRCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'qr_date',
        'created_by',
        'status',
        'notes',
        'expires_at',
    ];

    protected $casts = [
        'qr_date' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user who created this QR code.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who owns this personal QR code.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'user_qr_code_id');
    }

    /**
     * Get the student who owns this student QR code.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'qr_code_id');
    }

    /**
     * Get the absences that used this QR code.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'qr_code_id');
    }

    /**
     * Check if QR code is currently active.
     */
    public function isActive(): bool
    {
        if ($this->status === 'active') {
            return $this->expires_at === null || $this->expires_at->isFuture();
        }
        return false;
    }

    /**
     * Generate a unique QR code.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'QR-' . strtoupper(bin2hex(random_bytes(8))); // e.g., QR-A1B2C3D4E5F6G7H8
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Create a student-specific QR code.
     */
    public static function createStudentQRCode(int $studentId, ?string $studentName = null): self
    {
        $code = self::generateCode();

        return self::create([
            'code' => $code,
            'qr_date' => now(),
            'created_by' => auth()->id() ?? 1, // Use current user or system user
            'status' => 'active',
            'notes' => $studentName
                ? "Student ID Card QR Code - {$studentName} (#{$studentId})"
                : "Student ID Card QR Code - Student #{$studentId}",
            'expires_at' => null, // Student QR codes don't expire
        ]);
    }

    /**
     * Create a personal QR code for a user.
     */
    public static function createUserQRCode(int $userId, ?string $userName = null): self
    {
        $code = self::generateCode();

        return self::create([
            'code' => $code,
            'qr_date' => now(),
            'created_by' => auth()->id() ?? $userId,
            'status' => 'active',
            'notes' => $userName
                ? "Personal QR Code - {$userName} (#{$userId})"
                : "Personal QR Code - User #{$userId}",
            'expires_at' => null,
        ]);
    }
}

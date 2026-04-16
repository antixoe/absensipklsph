<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}

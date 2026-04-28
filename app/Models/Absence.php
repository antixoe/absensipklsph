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
        'qr_code_id',
        'scanned_qr_at',
        'scanned_qr_out_at',
        'qr_code',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'absence_date' => 'datetime',
        'scanned_qr_at' => 'datetime',
        'scanned_qr_out_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    /**
     * Get the QR code if this absence was marked via QR scan.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QRCode::class, 'qr_code_id');
    }
}

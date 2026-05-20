<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'internship_program_id',
        'nim',
        'school',
        'major',
        'phone',
        'company_placement',
        'start_date',
        'end_date',
        'status',
        'qr_code_id',
        'student_qr_code',
        'rombel_id',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Ensure every student always has a personal QR code.
     */
    protected static function booted(): void
    {
        static::saved(function (Student $student) {
            $student->ensureQrCode();
        });
    }

    /**
     * Get the user associated with this student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the internship program associated with this student.
     */
    public function internshipProgram(): BelongsTo
    {
        return $this->belongsTo(InternshipProgram::class);
    }

    /**
     * Get the learning group associated with this student.
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(RombonganBelajar::class, 'rombel_id');
    }

    /**
     * Get the QR code associated with this student.
     */
    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QRCode::class, 'qr_code_id');
    }

    /**
     * Ensure this student has a QR code record and return it.
     */
    public function ensureQrCode(): ?QRCode
    {
        if ($this->relationLoaded('qrCode') && $this->qrCode) {
            return $this->qrCode;
        }

        if ($this->qr_code_id) {
            $existingQrCode = $this->qrCode()->first();

            if ($existingQrCode) {
                $this->setRelation('qrCode', $existingQrCode);
                $this->setAttribute('student_qr_code', $existingQrCode->code);

                return $existingQrCode;
            }
        }

        $studentName = $this->relationLoaded('user') && $this->user
            ? $this->user->name
            : ('Student #' . $this->id);

        $qrCode = QRCode::createStudentQRCode($this->id, $studentName);

        self::whereKey($this->id)->update([
            'qr_code_id' => $qrCode->id,
            'student_qr_code' => $qrCode->code,
        ]);

        $this->setAttribute('qr_code_id', $qrCode->id);
        $this->setAttribute('student_qr_code', $qrCode->code);
        $this->setRelation('qrCode', $qrCode);

        return $qrCode;
    }

    /**
     * Get the attendances for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the absences for this student.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Get the logbook entries for this student.
     */
    public function logbookEntries(): HasMany
    {
        return $this->hasMany(LogbookEntry::class);
    }

    /**
     * Get the activities for this student.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Get the documents for this student.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}

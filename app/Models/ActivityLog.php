<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'action',
        'subject',
        'subject_id',
        'description',
        'ip_address',
        'user_agent',
        'latitude',
        'longitude',
        'location_name',
        'location_city',
        'location_country',
        'device_type',
        'browser',
        'operating_system',
        'method',
        'url_path',
        'old_values',
        'new_values',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity with comprehensive tracking
     */
    public static function log($action, $subject = null, $subjectId = null, $description = null, $oldValues = null, $newValues = null)
    {
        try {
            $userAgent = request()->header('User-Agent') ?? '';
            
            return self::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'subject' => $subject,
                'subject_id' => $subjectId,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => $userAgent,
                'latitude' => request()->input('latitude'),
                'longitude' => request()->input('longitude'),
                'location_name' => request()->input('location_name'),
                'location_city' => request()->input('location_city'),
                'location_country' => request()->input('location_country'),
                'device_type' => self::detectDeviceType($userAgent),
                'browser' => self::detectBrowser($userAgent),
                'operating_system' => self::detectOS($userAgent),
                'method' => request()->method(),
                'url_path' => request()->path(),
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
            ]);
        } catch (\Exception $e) {
            // Gracefully handle if table doesn't exist
            \Log::warning('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Detect device type from user agent
     */
    private static function detectDeviceType($userAgent): ?string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            if (preg_match('/iPad|Android.*Tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    private static function detectBrowser($userAgent): ?string
    {
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/Opera|OPR/i', $userAgent)) return 'Opera';
        return 'Other';
    }

    /**
     * Detect operating system from user agent
     */
    private static function detectOS($userAgent): ?string
    {
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac/i', $userAgent)) return 'macOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) return 'iOS';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        return 'Other';
    }

    /**
     * Scope: Get only active (not deleted) logs
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope: Get only deleted logs
     */
    public function scopeDeleted($query)
    {
        return $query->whereNotNull('deleted_at');
    }

    /**
     * Scope: Get logs for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope: Get logs for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get logs for specific action
     */
    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): ?string
    {
        $location = [];
        
        if ($this->location_name) {
            $location[] = $this->location_name;
        }
        if ($this->location_city) {
            $location[] = $this->location_city;
        }
        if ($this->location_country) {
            $location[] = $this->location_country;
        }
        
        return !empty($location) ? implode(', ', array_filter($location)) : null;
    }

    /**
     * Get device info string
     */
    public function getDeviceInfoAttribute(): string
    {
        $info = [];
        
        if ($this->device_type) {
            $info[] = ucfirst($this->device_type);
        }
        if ($this->browser) {
            $info[] = $this->browser;
        }
        if ($this->operating_system) {
            $info[] = $this->operating_system;
        }
        
        return !empty($info) ? implode(' • ', $info) : 'Unknown';
    }
}

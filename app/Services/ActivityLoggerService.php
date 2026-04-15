<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLoggerService
{
    /**
     * Log a user activity
     */
    public static function log(
        string $action,
        ?string $subject = null,
        ?int $subjectId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ?ActivityLog {
        try {
            return ActivityLog::log($action, $subject, $subjectId, $description, $oldValues, $newValues);
        } catch (\Exception $e) {
            \Log::warning('Failed to log activity: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Log an action with data changes
     */
    public static function logChange(
        string $action,
        string $subject,
        int $subjectId,
        array $oldData,
        array $newData,
        ?string $description = null
    ): ?ActivityLog {
        return self::log(
            $action,
            $subject,
            $subjectId,
            $description,
            $oldData,
            $newData
        );
    }

    /**
     * Log a view access
     */
    public static function logView(string $viewName, ?string $resourceId = null): ?ActivityLog
    {
        return self::log(
            'viewed_page',
            'page',
            $resourceId,
            "User accessed $viewName"
        );
    }

    /**
     * Log a create action
     */
    public static function logCreate(string $subject, int $subjectId, array $data, ?string $description = null): ?ActivityLog
    {
        return self::log(
            'created',
            $subject,
            $subjectId,
            $description ?? "Created new $subject record",
            [],
            $data
        );
    }

    /**
     * Log an update action
     */
    public static function logUpdate(string $subject, int $subjectId, array $oldData, array $newData, ?string $description = null): ?ActivityLog
    {
        return self::log(
            'updated',
            $subject,
            $subjectId,
            $description ?? "Updated $subject record",
            $oldData,
            $newData
        );
    }

    /**
     * Log a delete action
     */
    public static function logDelete(string $subject, int $subjectId, array $deletedData, ?string $description = null): ?ActivityLog
    {
        return self::log(
            'deleted',
            $subject,
            $subjectId,
            $description ?? "Deleted $subject record",
            $deletedData,
            []
        );
    }

    /**
     * Log approval action
     */
    public static function logApproved(string $subject, int $subjectId, ?string $description = null): ?ActivityLog
    {
        return self::log(
            'approved',
            $subject,
            $subjectId,
            $description ?? "Approved $subject record"
        );
    }

    /**
     * Log rejection action
     */
    public static function logRejected(string $subject, int $subjectId, ?string $description = null): ?ActivityLog
    {
        return self::log(
            'rejected',
            $subject,
            $subjectId,
            $description ?? "Rejected $subject record"
        );
    }

    /**
     * Log login action
     */
    public static function logLogin(): ?ActivityLog
    {
        return self::log(
            'login',
            'user',
            auth()->id(),
            'User logged in'
        );
    }

    /**
     * Log logout action
     */
    public static function logLogout(): ?ActivityLog
    {
        $userId = auth()->id();
        auth()->logout();
        
        return ActivityLog::create([
            'user_id' => $userId,
            'action' => 'logout',
            'subject' => 'user',
            'subject_id' => $userId,
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
            'device_type' => self::detectDeviceType(request()->header('User-Agent')),
            'browser' => self::detectBrowser(request()->header('User-Agent')),
            'operating_system' => self::detectOS(request()->header('User-Agent')),
            'method' => request()->method(),
            'url_path' => request()->path(),
        ]);
    }

    /**
     * Detect device type
     */
    private static function detectDeviceType(?string $userAgent): ?string
    {
        if (empty($userAgent)) return null;
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
            if (preg_match('/iPad|Android.*Tablet/i', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Detect browser
     */
    private static function detectBrowser(?string $userAgent): ?string
    {
        if (empty($userAgent)) return null;
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/Opera|OPR/i', $userAgent)) return 'Opera';
        return 'Other';
    }

    /**
     * Detect operating system
     */
    private static function detectOS(?string $userAgent): ?string
    {
        if (empty($userAgent)) return null;
        if (preg_match('/Windows/i', $userAgent)) return 'Windows';
        if (preg_match('/Mac/i', $userAgent)) return 'macOS';
        if (preg_match('/Linux/i', $userAgent)) return 'Linux';
        if (preg_match('/iPhone|iPad|iPod/i', $userAgent)) return 'iOS';
        if (preg_match('/Android/i', $userAgent)) return 'Android';
        return 'Other';
    }
}

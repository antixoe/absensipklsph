<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show settings page with activity logs
     */
    public function index()
    {
        $activityLogs = collect();
        $totalActions = 0;
        $todayActions = 0;
        $userActions = 0;

        try {
            // Get active activity logs - 50 per page
            $activityLogs = ActivityLog::with('user')
                ->active()
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            // Get stats
            $totalActions = ActivityLog::active()->count();
            $todayActions = ActivityLog::active()->whereDate('created_at', today())->count();
            $userActions = ActivityLog::active()->where('user_id', auth()->id())->count();
        } catch (\Exception $e) {
            // Table might not exist yet - gracefully handle
            \Log::warning('Activity logs table error: ' . $e->getMessage());
        }

        return view('settings.index', compact('activityLogs', 'totalActions', 'todayActions', 'userActions'));
    }

    /**
     * Show trash (deleted activity logs)
     */
    public function trash()
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $deletedLogs = collect();
        $totalDeleted = 0;

        try {
            // Get deleted activity logs - 50 per page
            $deletedLogs = ActivityLog::with('user')
                ->onlyTrashed()
                ->orderBy('deleted_at', 'desc')
                ->paginate(50);

            $totalDeleted = ActivityLog::onlyTrashed()->count();
        } catch (\Exception $e) {
            \Log::warning('Activity logs trash error: ' . $e->getMessage());
        }

        return view('settings.trash', compact('deletedLogs', 'totalDeleted'));
    }

    /**
     * Soft delete an activity log
     */
    public function delete($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::findOrFail($id);
            $log->delete();

            ActivityLog::log(
                'deleted_activity_log',
                'activity_log',
                $id,
                'Soft deleted activity log for user ' . ($log->user->name ?? 'Unknown')
            );

            return redirect()->back()->with('success', 'Activity log moved to trash.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete activity log: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted activity log
     */
    public function restore($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::withTrashed()->findOrFail($id);
            $log->restore();

            ActivityLog::log(
                'restored_activity_log',
                'activity_log',
                $id,
                'Restored activity log for user ' . ($log->user->name ?? 'Unknown')
            );

            return redirect()->back()->with('success', 'Activity log restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore activity log: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an activity log
     */
    public function forceDelete($id)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::withTrashed()->findOrFail($id);
            $userName = $log->user->name ?? 'Unknown';
            $log->forceDelete();

            ActivityLog::log(
                'permanently_deleted_activity_log',
                'activity_log',
                $id,
                'Permanently deleted activity log for user ' . $userName
            );

            return redirect()->back()->with('success', 'Activity log permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to permanently delete activity log: ' . $e->getMessage());
        }
    }

    /**
     * Clear activity logs (soft delete all active logs)
     */
    public function clearLogs(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $count = ActivityLog::active()->count();
            ActivityLog::active()->delete();

            ActivityLog::log('cleared_activity_logs', 'activity_log', null, 'All activity logs were soft deleted');

            return redirect()->back()->with('success', "Activity logs moved to trash ($count records).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear activity logs: ' . $e->getMessage());
        }
    }

    /**
     * Permanently clear all deleted logs from trash
     */
    public function emptyTrash(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $count = ActivityLog::onlyTrashed()->count();
            ActivityLog::onlyTrashed()->forceDelete();

            ActivityLog::log('emptied_activity_logs_trash', 'activity_log', null, "Permanently deleted $count activity logs from trash");

            return redirect()->back()->with('success', "Trash emptied permanently ($count records).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to empty trash: ' . $e->getMessage());
        }
    }

    /**
     * Export activity logs as CSV
     */
    public function exportLogs()
    {
        if (!auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $logs = ActivityLog::with('user')->active()->orderBy('created_at', 'desc')->get();

        $csvContent = "Date,Time,User,Action,Subject,Description,IP Address,Location,Device,Browser,OS\n";
        
        foreach ($logs as $log) {
            $csvContent .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('Y-m-d'),
                $log->created_at->format('H:i:s'),
                $log->user->name ?? 'Unknown',
                $log->action,
                $log->subject ?? '',
                str_replace('"', '""', $log->description ?? ''),
                $log->ip_address ?? '',
                str_replace('"', '""', $log->full_location ?? ''),
                $log->device_type ?? '',
                $log->browser ?? '',
                $log->operating_system ?? ''
            );
        }

        return response()
            ->streamDownload(fn() => print($csvContent), 'activity-logs-' . now()->format('Y-m-d-His') . '.csv');
    }
}

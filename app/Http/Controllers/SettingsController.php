<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show settings page with activity logs
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');

        // For non-admin users, show the enhanced student settings page
        if (!$isAdmin) {
            $userLogs = ActivityLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('settings.student-settings', compact('user', 'userLogs'));
        }

        // For admin users, show the admin activity logs page
        $activityLogs = collect();
        $totalActions = 0;
        $todayActions = 0;
        $userActions = 0;
        $availableActions = collect();
        $availableUsers = collect();

        try {
            // Get per page value from request, default to 50
            $perPage = (int) $request->get('per_page', 50);
            $perPage = in_array($perPage, [25, 50, 100]) ? $perPage : 50;

            // Build query with search and filters
            $query = ActivityLog::with('user')
                ->active();

            // Search by user name or email
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }

            // Filter by action type
            if ($request->filled('action_filter')) {
                $query->where('action', $request->get('action_filter'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            }

            // Filter by user
            if ($request->filled('user_filter')) {
                $query->where('user_id', $request->get('user_filter'));
            }

            // Get stats
            $totalActions = ActivityLog::active()->count();
            $todayActions = ActivityLog::active()->whereDate('created_at', today())->count();
            $userActions = ActivityLog::active()->where('user_id', auth()->id())->count();

            // Get distinct actions for filter dropdown
            $availableActions = ActivityLog::active()
                ->distinct()
                ->pluck('action')
                ->map(fn($action) => str_replace('_', ' ', ucfirst($action)))
                ->sort();

            // Get distinct users for filter dropdown
            $availableUsers = \App\Models\User::where(function ($q) {
                $q->has('activityLogs');
            })->get();

            // Get active activity logs with pagination
            $activityLogs = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        } catch (\Exception $e) {
            // Table might not exist yet - gracefully handle
            \Log::warning('Activity logs table error: ' . $e->getMessage());
        }

        return view('settings.index', compact(
            'activityLogs', 
            'totalActions', 
            'todayActions', 
            'userActions',
            'availableActions',
            'availableUsers'
        ));
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

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        auth()->user()->update($validated);

        ActivityLog::log(
            'updated_profile',
            'user',
            auth()->id(),
            'Updated profile information'
        );

        return redirect()->route('settings.index')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        ActivityLog::log(
            'changed_password',
            'user',
            auth()->id(),
            'Changed password'
        );

        return redirect()->route('settings.index')->with('success', 'Password changed successfully!');
    }

    /**
     * Get user's activity logs (personal logs only, not admin-only)
     */
    public function getUserActivityLogs()
    {
        $userLogs = ActivityLog::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('settings.user-activity', compact('userLogs'));
    }
}


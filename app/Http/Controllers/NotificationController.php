<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class NotificationController extends Controller
{
    /**
     * Show user notifications.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get notifications from the database
        $notifications = $user->notifications()->paginate(20);
        $unreadCount = $user->unreadNotifications->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function delete($id)
    {
        $notification = Auth::user()->notifications()->find($id);
        
        if ($notification) {
            $notification->delete();
        }

        return redirect()->back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all notifications.
     */
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();

        return redirect()->back()->with('success', 'All notifications deleted.');
    }
}

@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-bell" style="margin-right: 8px;"></i>My Notifications</h1>
        <p>View your absence approval status and admin messages</p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with actions -->
    @if($unreadCount > 0)
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <span style="background: #fef3c7; color: #92400e; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    <i class="bi bi-circle-fill" style="margin-right: 4px;"></i>
                    {{ $unreadCount }} {{ $unreadCount === 1 ? 'Unread' : 'Unread' }} Notification{{ $unreadCount > 1 ? 's' : '' }}
                </span>
            </div>
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn" style="padding: 8px 16px; font-size: 14px;">
                    <i class="bi bi-check-all" style="margin-right: 4px;"></i>Mark All as Read
                </button>
            </form>
        </div>
    @endif

    @if($notifications->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach($notifications as $notification)
                <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid {{ $notification->read_at ? '#ddd' : '#f59e0b' }}; 
                           box-shadow: 0 2px 8px rgba(0,0,0,0.05); {{ $notification->read_at ? '' : 'background: #fffbf0;' }}">
                    
                    <!-- Notification Header -->
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #222;">
                                {{ $notification->data['title'] }}
                                @if(!$notification->read_at)
                                    <span style="display: inline-block; background: #fbbf24; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 8px; font-weight: 500;">NEW</span>
                                @endif
                            </h3>
                            <p style="margin: 0; font-size: 13px; color: #666;">
                                {{ $notification->data['message'] }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('notifications.delete', $notification->id) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #999; cursor: pointer; font-size: 18px; padding: 0;">
                                ×
                            </button>
                        </form>
                    </div>

                    <!-- Notification Details -->
                    <div style="background: #f9fafb; padding: 12px; border-radius: 6px; margin-bottom: 12px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div>
                                <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">DATE</p>
                                <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['date'] }}</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">STATUS</p>
                                <p style="margin: 0; font-size: 14px;">
                                    @if($notification->data['action'] === 'approved')
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>Approved
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #fee2e2; color: #991b1b; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i>Rejected
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    @if($notification->data['admin_notes'])
                        <div style="background: #e0f2fe; padding: 12px; border-radius: 6px; border-left: 3px solid #0284c7; margin-bottom: 12px;">
                            <p style="margin: 0 0 5px 0; font-size: 12px; color: #0c4a6e; font-weight: 600;">
                                <i class="bi bi-chat-left-text" style="margin-right: 4px;"></i>ADMIN NOTES
                            </p>
                            <p style="margin: 0; font-size: 14px; color: #0c4a6e;">
                                {{ $notification->data['admin_notes'] }}
                            </p>
                        </div>
                    @endif

                    <!-- Timestamp -->
                    <div style="font-size: 12px; color: #999;">
                        <i class="bi bi-clock" style="margin-right: 4px;"></i>
                        {{ $notification->created_at->diffForHumans() }}
                        @if(!$notification->read_at)
                            <span style="margin-left: 8px; color: #fbbf24;">• Unread</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($notifications->hasPages())
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="bi bi-bell" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                <p style="font-size: 18px; margin: 10px 0;">No notifications yet</p>
                <p style="font-size: 14px; color: #999;">Your absence approvals and updates will appear here</p>
            </div>
        </div>
    @endif
@endsection

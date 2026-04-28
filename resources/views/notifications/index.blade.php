@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-bell" style="margin-right: 8px;"></i>Notifikasi Saya</h1>
        <p>Lihat absensi, agenda harian, dan pesan admin Anda</p>
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
                    {{ $unreadCount }} Notifikasi belum dibaca
                </span>
            </div>
            <form method="POST" action="{{ route('notifications.markAllAsRead') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn" style="padding: 8px 16px; font-size: 14px;">
                    <i class="bi bi-check-all" style="margin-right: 4px;"></i>Tandai semua sudah dibaca
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
                                Ã—
                            </button>
                        </form>
                    </div>

                    <!-- Notification Details -->
                    <div style="background: #f9fafb; padding: 12px; border-radius: 6px; margin-bottom: 12px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <!-- Date - for both types of notifications -->
                            @if(isset($notification->data['date']) || isset($notification->data['submission_date']))
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">TANGGAL</p>
                                    <p style="margin: 0; font-size: 14px; color: #222;">
                                        {{ $notification->data['date'] ?? $notification->data['submission_date'] ?? 'N/A' }}
                                    </p>
                                </div>
                            @endif

                            <!-- Status/Method -->
                            @if(isset($notification->data['review_type']))
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">JENIS REVIEW</p>
                                    <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['review_type'] }}</p>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">STATUS</p>
                                    <p style="margin: 0; font-size: 14px;">
                                        @if($notification->data['status'] === 'approved')
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>Disetujui
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #fee2e2; color: #991b1b; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i>Ditolak
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @elseif(isset($notification->data['agenda_id']) && isset($notification->data['student_name']) && !isset($notification->data['review_type']))
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">JAM MASUK</p>
                                    <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['time_in'] ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">JAM PULANG</p>
                                    <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['time_out'] ?? 'N/A' }}</p>
                                </div>
                            @elseif(isset($notification->data['action']))
                                <!-- Old Approval Notification -->
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">STATUS</p>
                                    <p style="margin: 0; font-size: 14px;">
                                        @if($notification->data['action'] === 'approved')
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>Disetujui
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #fee2e2; color: #991b1b; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i>Ditolak
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @elseif(isset($notification->data['submission_method']))
                                <!-- New Submission Notification -->
                                <div>
                                    <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">METODE</p>
                                    <p style="margin: 0; font-size: 14px;">
                                        @if($notification->data['submission_method'] === 'qr')
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #dbeafe; color: #0c4a6e; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-qr-code" style="margin-right: 4px;"></i>QR Code
                                            </span>
                                        @else
                                            <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #fce7f3; color: #831843; font-weight: 600; font-size: 12px;">
                                                <i class="bi bi-camera-fill" style="margin-right: 4px;"></i>Selfie
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Student Info for Submission Notifications -->
                        @if(isset($notification->data['student_name']))
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">SISWA</p>
                                        <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['student_name'] }}</p>
                                    </div>
                                    <div>
                                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">LOKASI</p>
                                        <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['location'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isset($notification->data['reviewer_name']))
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e7eb;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div>
                                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">VERIFIKATOR</p>
                                        <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['reviewer_name'] }}</p>
                                    </div>
                                    <div>
                                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #666; font-weight: 600;">TANGGAL AGENDA</p>
                                        <p style="margin: 0; font-size: 14px; color: #222;">{{ $notification->data['agenda_date'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Admin Notes (only for approval notifications) -->
                    @if(isset($notification->data['admin_notes']) && $notification->data['admin_notes'])
                        <div style="background: #e0f2fe; padding: 12px; border-radius: 6px; border-left: 3px solid #0284c7; margin-bottom: 12px;">
                            <p style="margin: 0 0 5px 0; font-size: 12px; color: #0c4a6e; font-weight: 600;">
                                <i class="bi bi-chat-left-text" style="margin-right: 4px;"></i>CATATAN ADMIN
                            </p>
                            <p style="margin: 0; font-size: 14px; color: #0c4a6e;">
                                {{ $notification->data['admin_notes'] }}
                            </p>
                        </div>
                    @endif

                    <!-- IP Address (for submission notifications) -->
                    @if(isset($notification->data['ip_address']))
                        <div style="background: #f3f4f6; padding: 12px; border-radius: 6px; border-left: 3px solid #6b7280; margin-bottom: 12px;">
                            <p style="margin: 0 0 5px 0; font-size: 12px; color: #374151; font-weight: 600;">
                                <i class="bi bi-globe" style="margin-right: 4px;"></i>IP ADDRESS
                            </p>
                            <p style="margin: 0; font-size: 14px; color: #374151; font-family: monospace;">
                                {{ $notification->data['ip_address'] }}
                            </p>
                        </div>
                    @endif

                    @if(isset($notification->data['notes']) && $notification->data['notes'])
                        <div style="background: #eef2ff; padding: 12px; border-radius: 6px; border-left: 3px solid #6366f1; margin-bottom: 12px;">
                            <p style="margin: 0 0 5px 0; font-size: 12px; color: #4338ca; font-weight: 600;">
                                <i class="bi bi-chat-left-text" style="margin-right: 4px;"></i>CATATAN
                            </p>
                            <p style="margin: 0; font-size: 14px; color: #4338ca;">
                                {{ $notification->data['notes'] }}
                            </p>
                        </div>
                    @endif

                    <!-- Timestamp -->
                    <div style="font-size: 12px; color: #999;">
                        <i class="bi bi-clock" style="margin-right: 4px;"></i>
                        {{ $notification->created_at->diffForHumans() }}
                        @if(!$notification->read_at)
                            <span style="margin-left: 8px; color: #fbbf24;">• Belum dibaca</span>
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
                <p style="font-size: 18px; margin: 10px 0;">Belum ada notifikasi</p>
                <p style="font-size: 14px; color: #999;">Persetujuan absensi dan agenda Anda akan muncul di sini</p>
            </div>
        </div>
    @endif
@endsection


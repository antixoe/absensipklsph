@extends('layouts.app')

@section('content')
    <style>
        * {
            box-sizing: border-box;
        }

        .page-intro {
            margin-bottom: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .page-intro-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-intro-content p {
            font-size: 1rem;
            color: #6c757d;
            margin: 0;
        }

        .page-intro-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        /* Statistics Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .stat-card.total {
            border-left-color: #f97316;
        }

        .stat-card.today {
            border-left-color: #10b981;
        }

        .stat-card.user {
            border-left-color: #0084ff;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.95rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Activity Log Card */
        .log-card {
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            padding: 0.6rem 1.2rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #f97316;
            color: white;
            border: 1px solid #f97316;
        }

        .btn-primary:hover {
            background: #ea580c;
            border-color: #ea580c;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: 1px solid #6c757d;
        }

        .btn-secondary:hover {
            background: #5c636a;
            border-color: #5c636a;
        }

        .btn-sm {
            padding: 0.5rem 0.9rem;
            font-size: 0.85rem;
        }

        /* Table */
        .log-table-wrapper {
            overflow-x: auto;
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 0.95rem;
        }

        .table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table th {
            padding: 1.2rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #495057;
            white-space: nowrap;
        }

        .table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table td {
            padding: 1.2rem;
            vertical-align: middle;
        }

        .date-cell {
            font-weight: 500;
            color: #1a1a1a;
        }

        .date-cell small {
            display: block;
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .user-cell {
            font-weight: 600;
            color: #1a1a1a;
        }

        .badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge.bg-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .badge.bg-danger {
            background: #f8d7da;
            color: #842029;
        }

        .badge.bg-info {
            background: #cfe2ff;
            color: #084298;
        }

        .badge.bg-secondary {
            background: #e2e3e5;
            color: #41464b;
        }

        .location-cell, .device-cell, .ip-cell {
            font-size: 0.9rem;
            color: #495057;
        }

        .location-cell small {
            display: block;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn {
            background: transparent;
            border: 1px solid #dee2e6;
            color: #495057;
            width: 36px;
            height: 36px;
            border-radius: 0.375rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            padding: 0;
        }

        .action-btn:hover {
            background: #e9ecef;
            border-color: #495057;
        }

        .action-btn.delete:hover {
            background: #f8d7da;
            border-color: #dc3545;
            color: #dc3545;
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 3.5rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #6c757d;
            margin: 0;
        }

        /* Pagination */
        .pagination-container {
            padding: 2rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s ease;
            padding: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close-btn:hover {
            color: #1a1a1a;
        }

        .modal-body {
            padding: 2rem;
        }

        .detail-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 700;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-value {
            color: #1a1a1a;
            word-break: break-word;
        }

        .detail-value code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .detail-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        @media (max-width: 768px) {
            .page-intro h1 {
                font-size: 1.75rem;
            }

            .log-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .log-actions {
                width: 100%;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th, .table td {
                padding: 0.75rem;
            }

            .stat-value {
                font-size: 2rem;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="page-intro">
        <div class="page-intro-content">
            <h1>
                <i class="bi bi-clock-history"></i>Activity Log
            </h1>
            <p>Monitor all user activities and system events in real-time</p>
        </div>
        <div class="page-intro-actions">
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('settings.trash') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-trash3"></i>Trash
                </a>
            @endif
            @if($activityLogs && $activityLogs->count() > 0)
                <a href="{{ route('settings.exportLogs') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-download"></i>Export CSV
                </a>
                @if(auth()->user()->hasRole('admin'))
                    <form method="POST" action="{{ route('settings.clearLogs') }}" style="display: inline;" onsubmit="return confirm('Are you sure? This will move all activity logs to trash.');">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="bi bi-trash"></i>Clear All
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card total">
            <div class="stat-value" style="color: #f97316;">{{ $totalActions }}</div>
            <div class="stat-label">Total Actions</div>
        </div>
        <div class="stat-card today">
            <div class="stat-value" style="color: #10b981;">{{ $todayActions }}</div>
            <div class="stat-label">Today's Actions</div>
        </div>
        <div class="stat-card user">
            <div class="stat-value" style="color: #0084ff;">{{ $userActions }}</div>
            <div class="stat-label">Your Actions</div>
        </div>
    </div>

    <!-- Activity Log Card -->
    <div class="log-card">
        @if($activityLogs && $activityLogs->count() > 0)
            <div class="log-table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 160px;"><i class="bi bi-calendar-event"></i> Date & Time</th>
                            <th style="width: 110px;"><i class="bi bi-person"></i> User</th>
                            <th style="width: 100px;"><i class="bi bi-lightning"></i> Action</th>
                            <th style="width: 150px;"><i class="bi bi-geo-alt"></i> Location</th>
                            <th style="width: 120px;"><i class="bi bi-laptop"></i> Device</th>
                            <th style="width: 110px;"><i class="bi bi-globe"></i> IP Address</th>
                            <th style="width: 80px; text-align: center;"><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activityLogs as $log)
                            <tr>
                                <td>
                                    <div class="date-cell">
                                        {{ $log->created_at->format('M d, Y') }}
                                        <small>{{ $log->created_at->format('H:i:s') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-cell">{{ $log->user->name ?? 'Unknown' }}</div>
                                </td>
                                <td>
                                    <span class="badge
                                        @if(str_contains($log->action, 'approved'))
                                            bg-success
                                        @elseif(str_contains($log->action, 'rejected'))
                                            bg-danger
                                        @elseif(str_contains($log->action, 'deleted'))
                                            bg-danger
                                        @elseif(str_contains($log->action, 'created'))
                                            bg-info
                                        @else
                                            bg-secondary
                                        @endif
                                    ">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="location-cell">
                                        @if($log->location_name || $log->location_city)
                                            <i class="bi bi-map-pin"></i> {{ $log->full_location ?? 'Unknown' }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="device-cell">
                                        @if($log->device_type)
                                            @if($log->device_type === 'mobile')
                                                <i class="bi bi-phone"></i> Mobile
                                            @elseif($log->device_type === 'tablet')
                                                <i class="bi bi-tablet-landscape"></i> Tablet
                                            @else
                                                <i class="bi bi-monitor"></i> Desktop
                                            @endif
                                            @if($log->browser)
                                                <small style="display: block; margin-top: 0.25rem;">{{ $log->browser }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="ip-cell">
                                        <code>{{ $log->ip_address ?? '—' }}</code>
                                        @if($log->method)
                                            <small style="display: block; margin-top: 0.25rem; background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 0.25rem; width: fit-content;">{{ $log->method }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    @if(auth()->user()->hasRole('admin'))
                                        <div class="action-buttons">
                                            <form method="POST" action="{{ route('activity-logs.delete', $log->id) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="action-btn delete" title="Delete Log">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            <button class="action-btn view-details-btn" 
                                                onclick="showLogDetails(this)" 
                                                title="View Details"
                                                data-id="{{ $log->id }}"
                                                data-date="{{ $log->created_at->format('M d, Y H:i:s') }}"
                                                data-user="{{ $log->user->name ?? 'Unknown' }}"
                                                data-action="{{ str_replace('_', ' ', ucfirst($log->action)) }}"
                                                data-location="{{ $log->full_location ?? 'Unknown' }}"
                                                data-device-type="{{ $log->device_type ?? 'Unknown' }}"
                                                data-browser="{{ $log->browser ?? 'Unknown' }}"
                                                data-os="{{ $log->os ?? 'Unknown' }}"
                                                data-ip="{{ $log->ip_address ?? '—' }}"
                                                data-method="{{ $log->method ?? 'GET' }}"
                                                data-url="{{ $log->url_path ?? '—' }}"
                                                data-user-agent="{{ $log->user_agent ?? '—' }}"
                                                data-old-values="{{ json_encode($log->old_values ?? []) }}"
                                                data-new-values="{{ json_encode($log->new_values ?? []) }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $activityLogs->links('pagination::bootstrap-4') ?? '' }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h3>No Activity Logs</h3>
                <p>Activity logs will be recorded once the system is fully initialized.</p>
            </div>
        @endif
    </div>

    <!-- Log Details Modal -->
    <div class="modal-overlay" id="logDetailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Activity Details</h2>
                <button class="modal-close-btn" onclick="closeLogDetails()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="detail-label">Date & Time</div>
                    <div class="detail-value" id="detail-date">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">User</div>
                    <div class="detail-value" id="detail-user">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Action</div>
                    <div class="detail-value" id="detail-action">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Location</div>
                    <div class="detail-value" id="detail-location">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Device</div>
                    <div class="detail-value" id="detail-device">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Browser</div>
                    <div class="detail-value" id="detail-browser">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">OS</div>
                    <div class="detail-value" id="detail-os">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">IP Address</div>
                    <div class="detail-value"><code id="detail-ip">—</code></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">HTTP Method</div>
                    <div class="detail-value" id="detail-method">—</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">URL Path</div>
                    <div class="detail-value"><code id="detail-url">—</code></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">User Agent</div>
                    <div class="detail-value" style="font-size: 0.85rem; word-wrap: break-word;"><code id="detail-user-agent">—</code></div>
                </div>
                <div class="detail-row" id="old-values-row" style="display: none;">
                    <div class="detail-label">Old Values</div>
                    <div class="detail-value"><pre id="detail-old-values" style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.375rem; overflow-x: auto; font-size: 0.85rem; margin: 0;"></pre></div>
                </div>
                <div class="detail-row" id="new-values-row" style="display: none;">
                    <div class="detail-label">New Values</div>
                    <div class="detail-value"><pre id="detail-new-values" style="background: #f8f9fa; padding: 0.75rem; border-radius: 0.375rem; overflow-x: auto; font-size: 0.85rem; margin: 0;"></pre></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showLogDetails(button) {
            const data = {
                date: button.getAttribute('data-date'),
                user: button.getAttribute('data-user'),
                action: button.getAttribute('data-action'),
                location: button.getAttribute('data-location'),
                deviceType: button.getAttribute('data-device-type'),
                browser: button.getAttribute('data-browser'),
                os: button.getAttribute('data-os'),
                ip: button.getAttribute('data-ip'),
                method: button.getAttribute('data-method'),
                url: button.getAttribute('data-url'),
                userAgent: button.getAttribute('data-user-agent'),
                oldValues: JSON.parse(button.getAttribute('data-old-values')),
                newValues: JSON.parse(button.getAttribute('data-new-values'))
            };

            // Populate modal fields
            document.getElementById('detail-date').textContent = data.date;
            document.getElementById('detail-user').textContent = data.user;
            document.getElementById('detail-action').textContent = data.action;
            document.getElementById('detail-location').textContent = data.location;
            document.getElementById('detail-device').textContent = data.deviceType;
            document.getElementById('detail-browser').textContent = data.browser;
            document.getElementById('detail-os').textContent = data.os;
            document.getElementById('detail-ip').textContent = data.ip;
            document.getElementById('detail-method').textContent = data.method;
            document.getElementById('detail-url').textContent = data.url;
            document.getElementById('detail-user-agent').textContent = data.userAgent;

            // Handle JSON data
            const oldValuesRow = document.getElementById('old-values-row');
            const newValuesRow = document.getElementById('new-values-row');

            if (Object.keys(data.oldValues).length > 0) {
                document.getElementById('detail-old-values').textContent = JSON.stringify(data.oldValues, null, 2);
                oldValuesRow.style.display = 'grid';
            } else {
                oldValuesRow.style.display = 'none';
            }

            if (Object.keys(data.newValues).length > 0) {
                document.getElementById('detail-new-values').textContent = JSON.stringify(data.newValues, null, 2);
                newValuesRow.style.display = 'grid';
            } else {
                newValuesRow.style.display = 'none';
            }

            // Show modal
            document.getElementById('logDetailsModal').classList.add('active');
        }

        function closeLogDetails() {
            document.getElementById('logDetailsModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('logDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogDetails();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogDetails();
            }
        });
    </script>
@endsection

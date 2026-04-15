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

        .stat-card.deleted {
            border-left-color: #dc3545;
        }

        .stat-card.back {
            border-left-color: #10b981;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .stat-card.back:hover {
            color: inherit;
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

        /* Trash Log Card */
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

        .btn-danger {
            background: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }

        .btn-danger:hover {
            background: #bb2d3b;
            border-color: #bb2d3b;
        }

        .btn-success {
            background: #28a745;
            color: white;
            border: 1px solid #28a745;
        }

        .btn-success:hover {
            background: #218838;
            border-color: #218838;
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
            opacity: 0.85;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            opacity: 1;
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

        .deleted-date-cell {
            color: #dc3545;
            font-weight: 600;
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

        .ip-cell {
            font-size: 0.9rem;
            color: #495057;
        }

        .ip-cell code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
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

        .action-btn.restore:hover {
            background: #d1e7dd;
            border-color: #28a745;
            color: #28a745;
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

        @media (max-width: 768px) {
            .page-intro h1 {
                font-size: 1.75rem;
            }

            .page-intro-actions {
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
                <i class="bi bi-trash3"></i>Trash
            </h1>
            <p>Manage deleted activity logs and restore if needed</p>
        </div>
        <div class="page-intro-actions">
            @if($deletedLogs && $deletedLogs->count() > 0)
                <form method="POST" action="{{ route('settings.emptyTrash') }}" style="display: inline;" onsubmit="return confirm('Are you sure? This will permanently delete all logs in trash. This cannot be undone.');">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-exclamation-triangle"></i>Empty Trash
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stat-card deleted">
            <div class="stat-value" style="color: #dc3545;">{{ $totalDeleted }}</div>
            <div class="stat-label">Deleted Logs</div>
        </div>
        <a href="{{ route('settings.index') }}" class="text-decoration-none stat-card back">
            <div class="stat-value" style="color: #10b981;">←</div>
            <div class="stat-label">Back to Activity Log</div>
        </a>
    </div>

    <!-- Trash Log Card -->
    <div class="log-card">
        @if($deletedLogs && $deletedLogs->count() > 0)
            <div class="log-table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 160px;"><i class="bi bi-calendar-event"></i> Created</th>
                            <th style="width: 110px;"><i class="bi bi-person"></i> User</th>
                            <th style="width: 100px;"><i class="bi bi-lightning"></i> Action</th>
                            <th style="width: 160px;"><i class="bi bi-trash-fill"></i> Deleted On</th>
                            <th style="width: 110px;"><i class="bi bi-globe"></i> IP Address</th>
                            <th style="width: 120px; text-align: center;"><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deletedLogs as $log)
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
                                    <div class="deleted-date-cell">
                                        {{ $log->deleted_at->format('M d, Y H:i:s') }}
                                        <br><small style="color: #6c757d; font-weight: normal;">{{ $log->deleted_at->diffForHumans() }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="ip-cell">
                                        <code>{{ $log->ip_address ?? '—' }}</code>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-buttons">
                                        <form method="POST" action="{{ route('activity-logs.restore', $log->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="action-btn restore" title="Restore">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('activity-logs.forceDelete', $log->id) }}" style="display: inline;" onsubmit="return confirm('Permanently delete this log? This cannot be undone.');">
                                            @csrf
                                            <button type="submit" class="action-btn delete" title="Delete Permanently">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $deletedLogs->links('pagination::bootstrap-4') ?? '' }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-trash"></i>
                </div>
                <h3>Trash is Empty</h3>
                <p>Deleted activity logs will appear here. You can restore them from this page.</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 0.75rem; padding: 1.5rem; margin-top: 2rem;">
        <div style="display: flex; gap: 1rem;">
            <div style="flex-shrink: 0;">
                <i class="bi bi-info-circle-fill" style="font-size: 1.5rem; color: #856404;"></i>
            </div>
            <div>
                <strong style="color: #856404;">Soft Delete vs. Hard Delete</strong>
                <p style="margin: 0.5rem 0 0 0; color: #856404; font-size: 0.9rem;">Logs are soft-deleted first, allowing recovery. You can restore them anytime. Use "Empty Trash" to permanently delete all logs at once, or delete individual logs permanently. Once permanently deleted, logs cannot be recovered.</p>
            </div>
        </div>
    </div>

    <script>
        // Any additional JavaScript functionality for trash page
    </script>
@endsection

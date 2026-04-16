@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h1 style="margin: 0; font-size: 28px; font-weight: 700; color: #333;">
            <i class="bi bi-trash" style="margin-right: 10px; color: #f97316;"></i>Deleted Users
        </h1>
        <a href="{{ route('admin.users') }}" class="btn btn-secondary" style="display: inline-block; padding: 8px 16px; background-color: #6c757d; color: white; border-radius: 6px; text-decoration: none; font-weight: 500;">
            <i class="bi bi-arrow-left" style="margin-right: 5px;"></i> Back to Users
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success" style="padding: 12px 16px; background-color: #d1fae5; border: 1px solid #6ee7b7; border-radius: 6px; color: #065f46; margin-bottom: 20px;">
            <i class="bi bi-check-circle" style="margin-right: 8px;"></i>{{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger" style="padding: 12px 16px; background-color: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; color: #991b1b; margin-bottom: 20px;">
            <i class="bi bi-exclamation-circle" style="margin-right: 8px;"></i>{{ $message }}
        </div>
    @endif

    <!-- Search & Filter Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <form method="GET" action="{{ route('admin.users.trash') }}" style="display: contents;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <input type="text" name="search" placeholder="Search by name or email..." value="{{ request('search') }}" 
                    style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 16px; background-color: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                    <i class="bi bi-search" style="margin-right: 5px;"></i>Search
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.users.trash') }}" class="btn btn-light" style="padding: 10px 16px; background-color: #f3f4f6; color: #333; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; text-decoration: none;">
                        <i class="bi bi-x-circle" style="margin-right: 5px;"></i>Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Trashed Users Table -->
    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        @if ($trashedUsers->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #374151; font-size: 14px;">Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #374151; font-size: 14px;">Email</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #374151; font-size: 14px;">Role</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #374151; font-size: 14px;">Deleted On</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: #374151; font-size: 14px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trashedUsers as $user)
                        <tr style="border-bottom: 1px solid #e5e7eb; transition: background-color 0.2s;">
                            <td style="padding: 15px; color: #333;">
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td style="padding: 15px; color: #666;">{{ $user->email }}</td>
                            <td style="padding: 15px;">
                                <span style="display: inline-block; padding: 4px 12px; background-color: #e0e7ff; color: #4c1d95; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                    {{ $user->role->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding: 15px; color: #666; font-size: 14px;">
                                {{ $user->deleted_at ? $user->deleted_at->format('M d, Y at H:i') : 'N/A' }}
                            </td>
                            <td style="padding: 15px;">
                                <div style="display: flex; gap: 8px;">
                                    <!-- Restore Button -->
                                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="Restore User" 
                                            style="padding: 6px 12px; background-color: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-arrow-counterclockwise" style="margin-right: 4px;"></i>Restore
                                        </button>
                                    </form>

                                    <!-- Permanently Delete Button -->
                                    <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" style="display: inline;" 
                                        onsubmit="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Permanently Delete" 
                                            style="padding: 6px 12px; background-color: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-trash" style="margin-right: 4px;"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; justify-content: center;">
                {{ $trashedUsers->links() }}
            </div>
        @else
            <div style="padding: 40px; text-align: center; color: #999;">
                <i class="bi bi-inbox" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
                <p style="margin: 0; font-size: 16px;">No deleted users found</p>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div style="margin-top: 25px; padding: 15px 20px; background-color: #eff6ff; border-left: 4px solid #0284c7; border-radius: 6px; color: #174ea6;">
        <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
        <strong>Note:</strong> Deleted users are kept for 30 days. After that, they will be permanently removed from the system.
    </div>
</div>

<style>
    .alert {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    tr:hover {
        background-color: #f9fafb !important;
    }

    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endsection

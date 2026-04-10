@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-people" style="margin-right: 8px;"></i>User Management</h1>
        <p>Manage system users and their roles</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>{{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-list" style="margin-right: 8px;"></i>Users List</span>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.users.import-form') }}" class="btn btn-info" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-file-earmark-excel" style="margin-right: 5px;"></i>Import from Excel
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-plus-circle" style="margin-right: 5px;"></i>Add New User
                </a>
            </div>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 12px; text-align: left;">Name</th>
                        <th style="padding: 12px; text-align: left;">Email</th>
                        <th style="padding: 12px; text-align: left;">Phone</th>
                        <th style="padding: 12px; text-align: left;">Role</th>
                        <th style="padding: 12px; text-align: center;">Status</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; font-weight: 600;">{{ $user->name }}</td>
                            <td style="padding: 12px;">{{ $user->email }}</td>
                            <td style="padding: 12px;">{{ $user->phone ?? '-' }}</td>
                            <td style="padding: 12px;">
                                <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                    {{ ucfirst(str_replace('_', ' ', $user->role->name ?? 'N/A')) }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                @if ($user->status === 'active')
                                    <span style="display: inline-block; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    </span>
                                @else
                                    <span style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                        <i class="bi bi-x-circle-fill"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                    <i class="bi bi-eye" style="margin-right: 5px;"></i>View
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                    <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash" style="margin-right: 5px;"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 20px; text-align: center; color: #999;">
                                <i class="bi bi-inbox" style="margin-right: 8px;"></i>No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $users->links() }}
        </div>
    </div>
@endsection

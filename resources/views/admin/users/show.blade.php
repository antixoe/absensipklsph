@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-person-circle" style="margin-right: 8px;"></i>{{ $user->name }}</h1>
        <p>User details and information</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="margin-bottom: 20px;">User Information</h2>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- Name -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Name</label>
                <p style="font-size: 16px; font-weight: 500;">{{ $user->name }}</p>
            </div>

            <!-- Email -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Email</label>
                <p style="font-size: 16px; font-weight: 500;">{{ $user->email }}</p>
            </div>

            <!-- Phone -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Phone</label>
                <p style="font-size: 16px; font-weight: 500;">{{ $user->phone ?? '-' }}</p>
            </div>

            <!-- Role -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Role</label>
                <p>
                    <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                        {{ ucfirst(str_replace('_', ' ', $user->role->name ?? 'N/A')) }}
                    </span>
                </p>
            </div>

            <!-- Status -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Status</label>
                <p>
                    @if ($user->status === 'active')
                        <span style="display: inline-block; background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </span>
                    @else
                        <span style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                            <i class="bi bi-x-circle-fill"></i> Inactive
                        </span>
                    @endif
                </p>
            </div>

            <!-- Created At -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Created</label>
                <p style="font-size: 16px; font-weight: 500;">{{ $user->created_at->format('M d, Y H:i') }}</p>
            </div>

            <!-- Updated At -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Last Updated</label>
                <p style="font-size: 16px; font-weight: 500;">{{ $user->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <!-- Address -->
        @if ($user->address)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Address</label>
                <p style="font-size: 16px; font-weight: 500; white-space: pre-wrap;">{{ $user->address }}</p>
            </div>
        @endif

        <!-- Buttons -->
        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary" style="flex: 1; padding: 10px; text-align: center;">
                <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit User
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary" style="flex: 1; padding: 10px; text-align: center;">
                <i class="bi bi-arrow-left" style="margin-right: 5px;"></i>Back to List
            </a>
        </div>
    </div>
@endsection

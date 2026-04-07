@extends('layouts.app')

@section('title', 'Manage Roles & Features')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-shield-lock" style="margin-right: 8px;"></i>Manage Roles & Features</h1>
        <p>Configure role access permissions</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-people" style="margin-right: 8px;"></i>Roles & Permissions</span>
            <a href="{{ route('admin.features') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">
                <i class="bi bi-sliders" style="margin-right: 5px;"></i>Manage Features
            </a>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 12px; text-align: left;">Role</th>
                        <th style="padding: 12px; text-align: left;">Description</th>
                        <th style="padding: 12px; text-align: left;">Features Assigned</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</td>
                            <td style="padding: 12px; color: #666;">{{ $role->description ?? 'N/A' }}</td>
                            <td style="padding: 12px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @forelse ($role->features as $feature)
                                        <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                            {{ $feature->name }}
                                        </span>
                                    @empty
                                        <span style="color: #999;">No features assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">
                                    <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Role Details Section -->
    <div style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px;"><i class="bi bi-info-circle" style="margin-right: 8px;"></i>Role Definitions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Student -->
            <div class="card" style="background: #f0fdf4;">
                <h3 style="color: #166534; margin-bottom: 10px;">
                    <i class="bi bi-mortarboard" style="margin-right: 5px;"></i>Student
                </h3>
                <p style="color: #166534; font-size: 14px; line-height: 1.6;">
                    • Check-in/Check-out functionality<br>
                    • Fill daily logbook entries<br>
                    • View guidance notes from supervisors
                </p>
            </div>

            <!-- Industry Supervisor -->
            <div class="card" style="background: #fef3c7;">
                <h3 style="color: #92400e; margin-bottom: 10px;">
                    <i class="bi bi-person-check" style="margin-right: 5px;"></i>Industry Supervisor
                </h3>
                <p style="color: #92400e; font-size: 14px; line-height: 1.6;">
                    • Validate student attendance<br>
                    • Review and approve logbooks<br>
                    • Provide guidance notes
                </p>
            </div>

            <!-- Head of Department -->
            <div class="card" style="background: #ddd6fe;">
                <h3 style="color: #5b21b6; margin-bottom: 10px;">
                    <i class="bi bi-briefcase" style="margin-right: 5px;"></i>Head of Department
                </h3>
                <p style="color: #5b21b6; font-size: 14px; line-height: 1.6;">
                    • Weekly logbook review<br>
                    • Filter by department<br>
                    • View department reports
                </p>
            </div>

            <!-- Homeroom Teacher -->
            <div class="card" style="background: #fee2e2;">
                <h3 style="color: #991b1b; margin-bottom: 10px;">
                    <i class="bi bi-book" style="margin-right: 5px;"></i>Homeroom Teacher
                </h3>
                <p style="color: #991b1b; font-size: 14px; line-height: 1.6;">
                    • View student class data<br>
                    • Filter by class only<br>
                    • Monitor class attendance
                </p>
            </div>

            <!-- Principal -->
            <div class="card" style="background: #f0f9ff;">
                <h3 style="color: #0c4a6e; margin-bottom: 10px;">
                    <i class="bi bi-building" style="margin-right: 5px;"></i>School Principal
                </h3>
                <p style="color: #0c4a6e; font-size: 14px; line-height: 1.6;">
                    • View all school data<br>
                    • Access all reports<br>
                    • Full system visibility
                </p>
            </div>

            <!-- Admin -->
            <div class="card" style="background: #f3e8ff;">
                <h3 style="color: #6b21a8; margin-bottom: 10px;">
                    <i class="bi bi-shield-lock" style="margin-right: 5px;"></i>Admin
                </h3>
                <p style="color: #6b21a8; font-size: 14px; line-height: 1.6;">
                    • Manage all roles<br>
                    • Manage users<br>
                    • Hierarchical filtering
                </p>
            </div>
        </div>
    </div>
@endsection

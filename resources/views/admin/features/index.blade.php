@extends('layouts.app')

@section('title', 'Manage Features')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-sliders" style="margin-right: 8px;"></i>Manage Features</h1>
        <p>Enable or disable features in the system</p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-gear" style="margin-right: 8px;"></i>System Features</span>
            <a href="{{ route('admin.roles') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">
                <i class="bi bi-people" style="margin-right: 5px;"></i>Back to Roles
            </a>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 12px; text-align: left;">Feature</th>
                        <th style="padding: 12px; text-align: left;">Slug</th>
                        <th style="padding: 12px; text-align: left;">Description</th>
                        <th style="padding: 12px; text-align: center;">Status</th>
                        <th style="padding: 12px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($features as $feature)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; font-weight: 600;">{{ $feature->name }}</td>
                            <td style="padding: 12px; font-family: monospace; color: #666;">{{ $feature->slug }}</td>
                            <td style="padding: 12px; color: #666;">{{ $feature->description ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                @if ($feature->is_active)
                                    <span style="display: inline-block; background: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>Active
                                    </span>
                                @else
                                    <span style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                        <i class="bi bi-x-circle-fill" style="margin-right: 5px;"></i>Inactive
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <form method="POST" action="{{ route('admin.features.toggle', $feature) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="padding: 6px 12px; font-size: 12px;">
                                        @if ($feature->is_active)
                                            <i class="bi bi-toggle-on" style="margin-right: 5px;"></i>Disable
                                        @else
                                            <i class="bi bi-toggle-off" style="margin-right: 5px;"></i>Enable
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Feature Categories Info -->
    <div style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px;"><i class="bi bi-info-circle" style="margin-right: 8px;"></i>Feature Categories</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Student Features -->
            <div style="border-left: 4px solid #166534; padding: 20px; background: #f0fdf4; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #166534;">
                    <i class="bi bi-mortarboard" style="margin-right: 8px;"></i>Student Features
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #166534;">
                    <li>Check-in/Check-out</li>
                    <li>Fill Daily Logbook</li>
                    <li>View Guidance Notes</li>
                </ul>
            </div>

            <!-- Supervisor Features -->
            <div style="border-left: 4px solid #92400e; padding: 20px; background: #fef3c7; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #92400e;">
                    <i class="bi bi-person-check" style="margin-right: 8px;"></i>Supervisor Features
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #92400e;">
                    <li>Validate Attendance</li>
                    <li>Validate Logbook</li>
                    <li>Provide Guidance</li>
                </ul>
            </div>

            <!-- Management Features -->
            <div style="border-left: 4px solid #4338ca; padding: 20px; background: #e0e7ff; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #4338ca;">
                    <i class="bi bi-shield-lock" style="margin-right: 8px;"></i>Management Features
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #4338ca;">
                    <li>Manage Roles</li>
                    <li>Manage Users</li>
                    <li>Manage Activities</li>
                </ul>
            </div>

            <!-- Administrative Features -->
            <div style="border-left: 4px solid #5b21b6; padding: 20px; background: #ddd6fe; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #5b21b6;">
                    <i class="bi bi-graph-up" style="margin-right: 8px;"></i>Administrative Features
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #5b21b6;">
                    <li>View All Data</li>
                    <li>View Reports</li>
                    <li>Weekly Review</li>
                </ul>
            </div>

            <!-- Filtering Features -->
            <div style="border-left: 4px solid #991b1b; padding: 20px; background: #fee2e2; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #991b1b;">
                    <i class="bi bi-funnel" style="margin-right: 8px;"></i>Filtering Features
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #991b1b;">
                    <li>Department Filter</li>
                    <li>Class Filter</li>
                </ul>
            </div>

            <!-- Reporting Features -->
            <div style="border-left: 4px solid #0369a1; padding: 20px; background: #e0f2fe; border-radius: 8px;">
                <h3 style="margin-top: 0; color: #0369a1;">
                    <i class="bi bi-bar-chart" style="margin-right: 8px;"></i>Reporting
                </h3>
                <ul style="margin: 0; padding-left: 20px; color: #0369a1;">
                    <li>View and generate reports</li>
                    <li>Assigned to multiple roles</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Usage Instructions -->
    <div class="card" style="margin-top: 30px; background: #f0f9ff;">
        <h3 style="margin-top: 0; color: #0c4a6e;">
            <i class="bi bi-question-circle" style="margin-right: 8px;"></i>How to Use
        </h3>
        <ol style="color: #0c4a6e; line-height: 1.8;">
            <li><strong>Toggle Feature Status:</strong> Use the toggle buttons above to enable or disable features system-wide.</li>
            <li><strong>Manage Role Assignments:</strong> Go to "Back to Roles" to assign specific features to roles.</li>
            <li><strong>Check User Access:</strong> Users will only see features and actions that are both:
                <ul>
                    <li>Assigned to their role, AND</li>
                    <li>Marked as Active in this list</li>
                </ul>
            </li>
            <li><strong>No Code Changes Needed:</strong> All changes take effect immediately without code modifications.</li>
        </ol>
    </div>
@endsection

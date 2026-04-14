@extends('layouts.app')

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
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-plus-circle" style="margin-right: 5px;"></i>Create Role
                </a>
                <a href="{{ route('admin.features') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-sliders" style="margin-right: 5px;"></i>Manage Features
                </a>
            </div>
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
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                    <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                                </a>
                                @unless(in_array($role->name, ['student', 'industry_supervisor', 'head_of_department', 'homeroom_teacher', 'school_principal', 'admin']))
                                    <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="confirmDelete('{{ $role->name }}', '{{ route('admin.roles.destroy', $role) }}')">
                                        <i class="bi bi-trash" style="margin-right: 5px;"></i>Delete
                                    </button>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
            <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Delete Role</h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;" onclick="closeDeleteModal()">×</button>
            </div>

            <div style="padding: 25px;">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-exclamation-circle" style="font-size: 48px; color: #991b1b;"></i>
                </div>
                <p style="margin: 0 0 10px 0; font-size: 16px; color: #1f2937; text-align: center;">
                    Are you sure you want to delete the <strong id="deleteRoleName"></strong> role?
                </p>
                <p style="font-size: 13px; color: #6b7280; margin: 10px 0; text-align: center;">
                    This action cannot be undone.
                </p>
            </div>

            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" id="deleteConfirmBtn" style="padding: 10px 16px; background: #991b1b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="submitDelete()">Delete Role</button>
            </div>
        </div>
    </div>

    <script>
        let deleteFormAction = null;

        function confirmDelete(roleName, deleteUrl) {
            document.getElementById('deleteRoleName').textContent = roleName;
            deleteFormAction = deleteUrl;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteFormAction = null;
        }

        function submitDelete() {
            if (!deleteFormAction) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteFormAction;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken.getAttribute('content');
                form.appendChild(token);
            }

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
@endsection
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

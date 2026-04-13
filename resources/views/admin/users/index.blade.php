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
                <button type="button" onclick="openAddUserModal()" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-plus-circle" style="margin-right: 5px;"></i>Add New User
                </button>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <form method="GET" action="{{ route('admin.users') }}" style="margin-top: 20px; margin-bottom: 20px; padding: 15px; background: #f9fafb; border-radius: 6px; border: 1px solid #e5e7eb;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 12px; align-items: end;">
                <!-- Search Bar -->
                <div>
                    <label style="margin-bottom: 6px; display: block; font-size: 13px; font-weight: 500;">Search</label>
                    <input type="text" name="search" placeholder="Name or Email" value="{{ request('search') }}" 
                           style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                </div>

                <!-- Role Filter -->
                <div>
                    <label style="margin-bottom: 6px; display: block; font-size: 13px; font-weight: 500;">Role</label>
                    <select name="role" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label style="margin-bottom: 6px; display: block; font-size: 13px; font-weight: 500;">Status</label>
                    <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px; width: 100%;">
                        <i class="bi bi-search" style="margin-right: 5px;"></i>Search
                    </button>
                </div>

                <!-- Clear Button -->
                <div>
                    <a href="{{ route('admin.users') }}" class="btn" style="padding: 8px 16px; font-size: 12px; width: 100%; text-align: center; display: inline-block; border: 1px solid #d1d5db; border-radius: 6px; background: white; color: #374151; text-decoration: none;">
                        <i class="bi bi-arrow-clockwise" style="margin-right: 5px;"></i>Clear
                    </a>
                </div>
            </div>
        </form>

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
                                <button type="button" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px; background: #e0f2fe; color: #0369a1; border: 1px solid #0369a1; border-radius: 4px; cursor: pointer;" onclick="openActionModal('view', {{ $user->id }}, '{{ $user->name }}')">
                                    <i class="bi bi-eye" style="margin-right: 5px;"></i>View
                                </button>
                                <button type="button" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px; background: #fef3c7; color: #b45309; border: 1px solid #b45309; border-radius: 4px; cursor: pointer;" onclick="openActionModal('edit', {{ $user->id }}, '{{ $user->name }}')">
                                    <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                                </button>
                                <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px; background: #fee2e2; color: #991b1b; border: 1px solid #991b1b; border-radius: 4px; cursor: pointer;" onclick="openActionModal('delete', {{ $user->id }}, '{{ $user->name }}')">
                                    <i class="bi bi-trash" style="margin-right: 5px;"></i>Delete
                                </button>
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
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div style="font-size: 13px; color: #666;">
                    Showing <strong>{{ $users->firstItem() ?? 0 }}</strong> to <strong>{{ $users->lastItem() ?? 0 }}</strong> 
                    of <strong>{{ $users->total() }}</strong> users
                </div>
                
                <div>
                    {{ $users->onEachSide(1)->links(view: 'pagination.custom-pagination') }}
                </div>
            </div>
        </div>

        <style>
            .pagination {
                display: flex !important;
                gap: 8px;
                flex-wrap: wrap;
                justify-content: center;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .pagination li {
                list-style: none;
            }

            .pagination a, 
            .pagination span {
                padding: 10px 14px;
                border: 1px solid #fed7aa;
                border-radius: 6px;
                font-size: 13px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 36px;
                transition: all 0.3s ease;
            }

            .pagination a {
                background: white;
                color: #f97316;
                cursor: pointer;
            }

            .pagination a:hover {
                background: #fed7aa;
                border-color: #f97316;
                color: #f97316;
            }

            .pagination .active span {
                background: #f97316;
                color: white;
                border-color: #f97316;
                font-weight: 600;
            }

            .pagination .disabled span {
                color: #d1d5db;
                background: #f9fafb;
                border-color: #e5e7eb;
                cursor: not-allowed;
            }

            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        </style>
    </div>

    <!-- Action Modal -->
    <div id="actionModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <!-- Modal Header -->
            <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalTitle" style="margin: 0; font-size: 18px; color: #1f2937;">Action</h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;" onclick="closeActionModal()">×</button>
            </div>

            <!-- Modal Body -->
            <div id="modalBody" style="padding: 25px;">
                <!-- Content will be loaded here -->
            </div>

            <!-- Modal Footer -->
            <div id="modalFooter" style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeActionModal()">Cancel</button>
                <button type="button" id="modalActionBtn" style="padding: 10px 16px; background: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        let currentAction = null;
        let currentUserId = null;

        function openActionModal(action, userId, userName) {
            currentAction = action;
            currentUserId = userId;
            const modal = document.getElementById('actionModal');
            const modalBody = document.getElementById('modalBody');
            const modalTitle = document.getElementById('modalTitle');
            const modalFooter = document.getElementById('modalFooter');
            const actionBtn = document.getElementById('modalActionBtn');

            // Clear previous content
            modalBody.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="bi bi-spinner" style="font-size: 30px; animation: spin 1s linear infinite;"></i></div>';
            
            if (action === 'view') {
                modalTitle.textContent = 'View User Details';
                modalFooter.style.display = 'none';
                
                // Fetch user details
                fetch(`{{ url('admin/users') }}/${userId}/details`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            const statusBg = user.status === 'active' ? '#d1fae5' : '#fee2e2';
                            const statusColor = user.status === 'active' ? '#065f46' : '#991b1b';
                            const statusText = user.status === 'active' ? 'Active' : 'Inactive';
                            
                            modalBody.innerHTML = `
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding: 5px 0;">
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Name</label>
                                        <p style="font-size: 14px; font-weight: 500;">${user.name}</p>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Email</label>
                                        <p style="font-size: 14px; font-weight: 500;">${user.email}</p>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Phone</label>
                                        <p style="font-size: 14px; font-weight: 500;">${user.phone}</p>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Role</label>
                                        <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                            ${user.role}
                                        </span>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Status</label>
                                        <span style="display: inline-block; background: ${statusBg}; color: ${statusColor}; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                            ${statusText}
                                        </span>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Created</label>
                                        <p style="font-size: 14px; font-weight: 500;">${user.created_at}</p>
                                    </div>
                                    ${user.address ? `<div style="grid-column: 1 / -1;">
                                        <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Address</label>
                                        <p style="font-size: 14px; font-weight: 500; white-space: pre-wrap;">${user.address}</p>
                                    </div>` : ''}
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        modalBody.innerHTML = '<div style="color: #991b1b; text-align: center; padding: 20px;">Error loading user details</div>';
                        console.error('Error:', error);
                    });
            } else if (action === 'edit') {
                modalTitle.textContent = 'Edit User';
                modalFooter.style.display = 'flex';
                actionBtn.textContent = 'Update User';
                actionBtn.style.background = '#b45309';
                
                // Fetch edit data
                fetch(`{{ url('admin/users') }}/${userId}/edit-data`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const user = data.user;
                            const roles = data.roles;
                            
                            let rolesOptions = '<option value="">Select a role</option>';
                            roles.forEach(role => {
                                const selected = role.id == user.role_id ? 'selected' : '';
                                rolesOptions += `<option value="${role.id}" ${selected}>${role.name}</option>`;
                            });
                            
                            modalBody.innerHTML = `
                                <form id="editUserForm" style="display: flex; flex-direction: column; gap: 15px;">
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Name <span style="color: red;">*</span></label>
                                        <input type="text" name="name" value="${user.name}" placeholder="Enter full name" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Email <span style="color: red;">*</span></label>
                                        <input type="email" name="email" value="${user.email}" placeholder="Enter email" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Password <span style="color: #999; font-size: 11px;">(leave blank to keep current)</span></label>
                                        <input type="password" name="password" placeholder="Enter new password (min 8 characters)" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Confirm Password</label>
                                        <input type="password" name="password_confirmation" placeholder="Confirm password" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Phone <span style="color: #999; font-size: 11px;">(optional)</span></label>
                                        <input type="text" name="phone" value="${user.phone || ''}" placeholder="Enter phone number" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Address <span style="color: #999; font-size: 11px;">(optional)</span></label>
                                        <textarea name="address" rows="3" placeholder="Enter address" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">${user.address || ''}</textarea>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Role <span style="color: red;">*</span></label>
                                        <select name="role_id" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                            ${rolesOptions}
                                        </select>
                                    </div>
                                    <div>
                                        <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Status <span style="color: red;">*</span></label>
                                        <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                            <option value="active" ${user.status === 'active' ? 'selected' : ''}>Active</option>
                                            <option value="inactive" ${user.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                        </select>
                                    </div>
                                </form>
                            `;
                        }
                    })
                    .catch(error => {
                        modalBody.innerHTML = '<div style="color: #991b1b; text-align: center; padding: 20px;">Error loading edit form</div>';
                        console.error('Error:', error);
                    });
                
                actionBtn.onclick = function() {
                    updateUserViaModal(userId);
                };
            } else if (action === 'delete') {
                modalTitle.textContent = 'Delete User';
                modalFooter.style.display = 'flex';
                modalBody.innerHTML = `
                    <div style="text-align: center; padding: 20px;">
                        <i class="bi bi-exclamation-circle" style="font-size: 48px; color: #991b1b;"></i>
                        <p style="margin-top: 15px; font-size: 16px; color: #1f2937;">
                            Are you sure you want to delete <strong>${userName}</strong>?
                        </p>
                        <p style="font-size: 13px; color: #6b7280; margin-top: 10px;">
                            This action cannot be undone.
                        </p>
                    </div>
                `;
                actionBtn.textContent = 'Delete User';
                actionBtn.style.background = '#991b1b';
                actionBtn.onclick = function() {
                    deleteUser(userId);
                };
            }

            // Show modal
            modal.style.display = 'flex';
        }

        function closeActionModal() {
            document.getElementById('actionModal').style.display = 'none';
            currentAction = null;
            currentUserId = null;
        }

        function updateUserViaModal(userId) {
            const form = document.getElementById('editUserForm');
            const formData = new FormData(form);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            fetch(`{{ url('admin/users') }}/${userId}/update-modal`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully!');
                    closeActionModal();
                    location.reload(); // Reload to show updated data
                } else {
                    alert('Error: ' + (data.message || 'Update failed'));
                }
            })
            .catch(error => {
                alert('Error updating user: ' + error);
                console.error('Error:', error);
            });
        }

        function deleteUser(userId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/users') }}/${userId}`;
            
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

        function openAddUserModal() {
            currentAction = 'add';
            const modal = document.getElementById('actionModal');
            const modalBody = document.getElementById('modalBody');
            const modalTitle = document.getElementById('modalTitle');
            const modalFooter = document.getElementById('modalFooter');
            const actionBtn = document.getElementById('modalActionBtn');

            modalTitle.textContent = 'Add New User';
            modalFooter.style.display = 'flex';
            actionBtn.textContent = 'Create User';
            actionBtn.style.background = '#10b981';

            // Fetch roles on demand
            fetch(`{{ route('admin.users.get-roles') }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const roles = data.roles;
                        let rolesOptions = '<option value="">Select a role</option>';
                        roles.forEach(role => {
                            rolesOptions += `<option value="${role.id}">${role.name}</option>`;
                        });

                        modalBody.innerHTML = `
                            <form id="addUserForm" style="display: flex; flex-direction: column; gap: 15px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Name <span style="color: red;">*</span></label>
                                    <input type="text" name="name" placeholder="Enter full name" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Email <span style="color: red;">*</span></label>
                                    <input type="email" name="email" placeholder="Enter email" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Password <span style="color: red;">*</span></label>
                                    <input type="password" name="password" placeholder="Enter password (min 8 characters)" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Confirm Password <span style="color: red;">*</span></label>
                                    <input type="password" name="password_confirmation" placeholder="Confirm password" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Phone <span style="color: #999; font-size: 11px;">(optional)</span></label>
                                    <input type="text" name="phone" placeholder="Enter phone number" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Address <span style="color: #999; font-size: 11px;">(optional)</span></label>
                                    <textarea name="address" rows="3" placeholder="Enter address" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;"></textarea>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Role <span style="color: red;">*</span></label>
                                    <select name="role_id" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                        ${rolesOptions}
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">Status <span style="color: red;">*</span></label>
                                    <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div id="formErrors" style="display: none; background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; border: 1px solid #fecaca; font-size: 13px;"></div>
                            </form>
                        `;
                    }
                })
                .catch(error => {
                    modalBody.innerHTML = '<div style="color: #991b1b; text-align: center; padding: 20px;">Error loading form</div>';
                    console.error('Error:', error);
                });

            actionBtn.onclick = function() {
                storeUserViaModal();
            };

            modal.style.display = 'flex';
        }

        function storeUserViaModal() {
            const form = document.getElementById('addUserForm');
            
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                formData.append('_token', csrfToken.getAttribute('content'));
            }

            // Disable button during submission
            const actionBtn = document.getElementById('modalActionBtn');
            const originalText = actionBtn.textContent;
            actionBtn.disabled = true;
            actionBtn.textContent = 'Creating...';

            fetch(`{{ route('admin.users.store') }}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User created successfully!');
                    closeActionModal();
                    location.reload();
                } else {
                    // Handle validation errors
                    const errorDiv = document.getElementById('formErrors');
                    if (data.errors) {
                        let errorHtml = '<strong>Please fix the following errors:</strong><ul style="margin: 10px 0 0 20px; padding: 0;">';
                        for (const [field, messages] of Object.entries(data.errors)) {
                            messages.forEach(message => {
                                errorHtml += `<li style="margin-bottom: 5px;">${message}</li>`;
                            });
                        }
                        errorHtml += '</ul>';
                        errorDiv.innerHTML = errorHtml;
                        errorDiv.style.display = 'block';
                    } else {
                        errorDiv.innerHTML = data.message || 'An error occurred while creating the user';
                        errorDiv.style.display = 'block';
                    }
                    
                    actionBtn.disabled = false;
                    actionBtn.textContent = originalText;
                }
            })
            .catch(error => {
                const errorDiv = document.getElementById('formErrors');
                errorDiv.innerHTML = 'Error creating user: ' + error;
                errorDiv.style.display = 'block';
                console.error('Error:', error);
                
                actionBtn.disabled = false;
                actionBtn.textContent = originalText;
            });
        }

        // Close modal when clicking outside
        document.getElementById('actionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeActionModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeActionModal();
            }
        });
    </script>
@endsection

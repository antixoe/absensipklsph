@extends('layouts.app')

@section('content')
<div style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="margin: 0; color: #1f2937; font-size: 28px;">
                <i class="bi bi-layers" style="margin-right: 10px; color: #f97316;"></i>Level Management
            </h1>
            <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Manage user levels and designations</p>
        </div>
        <a href="{{ route('admin.levels.create') }}" class="btn" style="background: #f97316; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
            <i class="bi bi-plus-circle"></i>Add New Level
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <i class="bi bi-check-circle" style="margin-right: 8px;"></i>{{ $message }}
            </div>
            <button onclick="this.parentElement.style.display='none'" style="background: none; border: none; cursor: pointer; color: #166534; font-size: 18px;">×</button>
        </div>
    @endif

    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Name</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Description</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; color: #1f2937; font-size: 13px;">Status</th>
                    <th style="padding: 12px 16px; text-align: center; font-weight: 600; color: #1f2937; font-size: 13px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($levels as $level)
                    <tr style="border-bottom: 1px solid #e5e7eb; hover: background: #f9fafb;">
                        <td style="padding: 12px 16px; color: #1f2937; font-weight: 500;">{{ $level->name }}</td>
                        <td style="padding: 12px 16px; color: #6b7280; font-size: 13px;">{{ $level->description ?? '-' }}</td>
                        <td style="padding: 12px 16px;">
                            <span style="display: inline-block; background: {{ $level->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $level->status === 'active' ? '#166534' : '#991b1b' }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                                {{ ucfirst($level->status) }}
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <button type="button" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px; background: #e0f2fe; color: #0369a1; border: 1px solid #0369a1; border-radius: 4px; cursor: pointer;" onclick="openActionModal('view', {{ $level->id }}, '{{ $level->name }}')">
                                <i class="bi bi-eye" style="margin-right: 5px;"></i>View
                            </button>
                            <button type="button" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px; background: #fef3c7; color: #b45309; border: 1px solid #b45309; border-radius: 4px; cursor: pointer;" onclick="openActionModal('edit', {{ $level->id }}, '{{ $level->name }}')">
                                <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                            </button>
                            <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px; background: #fee2e2; color: #991b1b; border: 1px solid #991b1b; border-radius: 4px; cursor: pointer;" onclick="openActionModal('delete', {{ $level->id }}, '{{ $level->name }}')">
                                <i class="bi bi-trash" style="margin-right: 5px;"></i>Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 30px; text-align: center; color: #6b7280;">
                            No levels found. <a href="{{ route('admin.levels.create') }}" style="color: #f97316; text-decoration: underline;">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($levels->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $levels->links('pagination::bootstrap-4') }}
        </div>
    @endif
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
            <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeActionModal()">Batal</button>
            <button type="button" id="modalActionBtn" style="padding: 10px 16px; background: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">Confirm</button>
        </div>
    </div>
</div>

<script>
    let currentAction = null;
    let currentLevelId = null;

    function openActionModal(action, levelId, levelName) {
        currentAction = action;
        currentLevelId = levelId;
        const modal = document.getElementById('actionModal');
        const modalBody = document.getElementById('modalBody');
        const modalTitle = document.getElementById('modalTitle');
        const modalFooter = document.getElementById('modalFooter');
        const actionBtn = document.getElementById('modalActionBtn');

        // Clear previous content
        modalBody.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="bi bi-spinner" style="font-size: 30px; animation: spin 1s linear infinite;"></i></div>';
        
        if (action === 'view') {
            modalTitle.textContent = 'View Level Details';
            modalFooter.style.display = 'none';
            
            // Fetch level details
            fetch(`{{ url('admin/levels') }}/${levelId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const level = data.level;
                        const statusBg = level.status === 'active' ? '#d1fae5' : '#fee2e2';
                        const statusColor = level.status === 'active' ? '#065f46' : '#991b1b';
                        const statusText = level.status === 'active' ? 'Active' : 'Inactive';
                        
                        modalBody.innerHTML = `
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding: 5px 0;">
                                <div>
                                    <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Name</label>
                                    <p style="font-size: 14px; font-weight: 500; text-transform: capitalize;">${level.name}</p>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Status</label>
                                    <span style="display: inline-block; background: ${statusBg}; color: ${statusColor}; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                        ${statusText}
                                    </span>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Description</label>
                                    <p style="font-size: 14px; font-weight: 500; white-space: pre-wrap;">${level.description || 'N/A'}</p>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Created</label>
                                    <p style="font-size: 14px; font-weight: 500;">${level.created_at}</p>
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Updated</label>
                                    <p style="font-size: 14px; font-weight: 500;">${level.updated_at}</p>
                                </div>
                            </div>
                        `;
                    }
                });
        } else if (action === 'edit') {
            modalTitle.textContent = 'Edit Level';
            modalFooter.style.display = 'flex';
            actionBtn.textContent = 'Update';
            actionBtn.style.background = '#f97316';
            
            // Fetch level details for editing
            fetch(`{{ url('admin/levels') }}/${levelId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const level = data.level;
                        modalBody.innerHTML = `
                            <form id="editForm" style="display: flex; flex-direction: column; gap: 15px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Name</label>
                                    <input type="text" id="levelName" value="${level.name}" readonly style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: #f3f4f6;" disabled>
                                    <small style="color: #6b7280; margin-top: 4px; display: block;">Level names cannot be changed</small>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                                    <textarea id="levelDescription" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; min-height: 80px; font-family: Arial, sans-serif;">${level.description || ''}</textarea>
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Status</label>
                                    <select id="levelStatus" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                                        <option value="active" ${level.status === 'active' ? 'selected' : ''}>Active</option>
                                        <option value="inactive" ${level.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                                    </select>
                                </div>
                            </form>
                        `;
                    }
                });
        } else if (action === 'delete') {
            modalTitle.textContent = 'Delete Level';
            modalFooter.style.display = 'flex';
            actionBtn.textContent = 'Delete';
            actionBtn.style.background = '#ef4444';
            
            modalBody.innerHTML = `
                <div style="padding: 20px 0;">
                    <p style="font-size: 14px; margin-bottom: 12px;">Are you sure you want to delete this level?</p>
                    <p style="font-size: 13px; color: #6b7280; font-weight: 600;">Level Name: <span style="color: #1f2937; text-transform: capitalize;">${levelName}</span></p>
                    <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px; border-radius: 4px; margin-top: 12px;">
                        <p style="margin: 0; font-size: 12px; color: #7c2d12;">
                            <i class="bi bi-exclamation-triangle" style="margin-right: 8px;"></i>This action cannot be undone.
                        </p>
                    </div>
                </div>
            `;
        }

        modal.style.display = 'flex';
    }

    function closeActionModal() {
        document.getElementById('actionModal').style.display = 'none';
        currentAction = null;
        currentLevelId = null;
    }

    document.getElementById('modalActionBtn').addEventListener('click', function() {
        if (currentAction === 'edit') {
            const name = document.getElementById('levelName').value;
            const description = document.getElementById('levelDescription').value;
            const status = document.getElementById('levelStatus').value;

            fetch(`{{ url('admin/levels') }}/${currentLevelId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    description: description,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error updating level: ' + (data.message || 'Unknown error'));
                }
            });
        } else if (currentAction === 'delete') {
            fetch(`{{ url('admin/levels') }}/${currentLevelId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting level: ' + (data.message || 'Unknown error'));
                }
            });
        }
    });

    // Close modal when clicking outside
    document.getElementById('actionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeActionModal();
        }
    });
</script>

@endsection

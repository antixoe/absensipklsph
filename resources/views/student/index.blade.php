@extends('layouts.app')

@section('styles')
<style>
    .student-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .student-hero {
        position: relative;
        overflow: hidden;
        padding: 32px;
        border-radius: 20px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 32%),
            linear-gradient(135deg, #7c2d12 0%, #ea580c 52%, #f97316 100%);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        margin-bottom: 32px;
    }

    .student-hero h1 {
        position: relative;
        z-index: 1;
        margin: 0 0 12px;
        font-size: 32px;
        line-height: 1.05;
        color: #fff;
    }

    .student-hero p {
        position: relative;
        z-index: 1;
        max-width: 600px;
        margin: 0;
        font-size: 16px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.88);
    }

    .student-controls {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 1;
        min-width: 200px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 12px 16px 12px 40px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        background: #fff;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 16px;
    }

    .student-table-wrapper {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .student-table {
        width: 100%;
        border-collapse: collapse;
    }

    .student-table thead {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .student-table thead th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #334155;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .student-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }

    .student-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .student-table tbody td {
        padding: 16px;
        font-size: 14px;
    }

    .student-name {
        font-weight: 600;
        color: #0f172a;
    }

    .student-nim {
        color: #64748b;
        font-size: 13px;
    }

    .student-school {
        color: #64748b;
        font-size: 13px;
    }

    .qr-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .qr-status.active {
        background: #d1fae5;
        color: #065f46;
    }

    .qr-status.inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .qr-status.none {
        background: #fef3c7;
        color: #92400e;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-action {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-action:hover {
        border-color: #f97316;
        color: #f97316;
        background: #fff7ed;
    }

    .btn-action.view-qr {
        color: #0369a1;
        border-color: #0369a1;
    }

    .btn-action.view-qr:hover {
        background: #f0f9ff;
    }

    .btn-action.download-qr {
        color: #059669;
        border-color: #059669;
    }

    .btn-action.download-qr:hover {
        background: #f0fdf4;
    }

    .btn-action.regenerate-qr {
        color: #dc2626;
        border-color: #dc2626;
    }

    .btn-action.regenerate-qr:hover {
        background: #fef2f2;
    }

    .btn-action i {
        font-size: 14px;
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }

    .empty-state h3 {
        margin: 0 0 8px;
        font-size: 18px;
        color: #64748b;
    }

    .pagination-wrapper {
        padding: 20px 16px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        justify-content: center;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-content {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .modal-body {
        padding: 24px;
    }

    .qr-display {
        text-align: center;
    }

    .qr-image-container {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .qr-image-container img {
        max-width: 300px;
        width: 100%;
        height: auto;
    }

    .qr-info {
        background: #f0fdf4;
        border: 1px solid #10b981;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        color: #166534;
        font-size: 14px;
        line-height: 1.6;
    }

    .qr-info strong {
        display: block;
        margin-bottom: 8px;
    }

    .qr-code-text {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px;
        font-family: monospace;
        font-size: 13px;
        color: #0f172a;
        word-break: break-all;
        margin-bottom: 16px;
    }

    .modal-footer {
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-modal {
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
    }

    .btn-modal.primary {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
    }

    .btn-modal.primary:hover {
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
    }

    .btn-modal.secondary {
        background: #f1f5f9;
        color: #334155;
    }

    .btn-modal.secondary:hover {
        background: #e2e8f0;
    }

    @media (max-width: 768px) {
        .student-controls {
            flex-direction: column;
        }

        .search-box {
            min-width: auto;
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            justify-content: center;
        }

        .student-table {
            font-size: 12px;
        }

        .student-table thead th,
        .student-table tbody td {
            padding: 12px 8px;
        }

        .modal-content {
            max-width: 95%;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-people-fill" style="margin-right: 8px;"></i>Student Management</h1>
    <p>Manage students and their QR codes</p>
</div>

<div class="student-list-container">
    <!-- Hero Section -->
    <div class="student-hero">
        <h1><i class="bi bi-list-check"></i> Student Directory</h1>
        <p>View all registered students, manage their QR codes, and generate new ones when needed.</p>
    </div>

    <!-- Search & Filter -->
    <div class="student-controls">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="search-input" placeholder="Search by name, NIM, or school...">
        </div>
    </div>

    <!-- Students Table -->
    <div class="student-table-wrapper">
        @if($students->count() > 0)
            <table class="student-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Student Name</th>
                        <th style="width: 15%;">NIM / ID</th>
                        <th style="width: 20%;">School / Class</th>
                        <th style="width: 15%;">QR Status</th>
                        <th style="width: 25%;">Actions</th>
                    </tr>
                </thead>
                <tbody id="students-tbody">
                    @foreach($students as $student)
                        <tr class="student-row" data-name="{{ $student->user->name }}" data-nim="{{ $student->nim ?? '' }}" data-school="{{ $student->school ?? '' }}">
                            <td>
                                <div class="student-name">{{ $student->user->name }}</div>
                                <div class="student-nim">{{ $student->user->email }}</div>
                            </td>
                            <td class="student-nim">{{ $student->nim ?? 'N/A' }}</td>
                            <td class="student-school">{{ $student->school ?? 'N/A' }}</td>
                            <td>
                                @if($student->qrCode)
                                    <span class="qr-status active">
                                        <i class="bi bi-check-circle-fill"></i> Active
                                    </span>
                                @else
                                    <span class="qr-status none">
                                        <i class="bi bi-exclamation-circle-fill"></i> No QR
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action view-qr" onclick="viewQRCode({{ $student->id }})">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    @if($student->qrCode)
                                        <button class="btn-action download-qr" onclick="downloadQRCode({{ $student->id }})">
                                            <i class="bi bi-download"></i> Download
                                        </button>
                                        @if(auth()->user()->hasRole(\App\Models\Role::KESISWAAN))
                                            <button class="btn-action regenerate-qr" onclick="regenerateQRCode({{ $student->id }})">
                                                <i class="bi bi-arrow-clockwise"></i> Regenerate
                                            </button>
                                        @endif
                                    @else
                                        @if(auth()->user()->hasRole(\App\Models\Role::KESISWAAN))
                                            <button class="btn-action regenerate-qr" onclick="regenerateQRCode({{ $student->id }})">
                                                <i class="bi bi-plus-circle"></i> Generate
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($students->hasPages())
                <div class="pagination-wrapper">
                    {{ $students->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h3>No Students Found</h3>
                <p>There are no registered students yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- QR Code Modal -->
<div id="qr-modal" class="modal-backdrop">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Student QR Code</h2>
            <button class="modal-close" onclick="closeQRModal()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body" id="qr-modal-body">
            <!-- Loaded dynamically -->
        </div>
        <div class="modal-footer">
            <button class="btn-modal secondary" onclick="closeQRModal()">Close</button>
            <button class="btn-modal primary" id="modal-download-btn" onclick="downloadFromModal()">
                <i class="bi bi-download"></i> Download
            </button>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('search-input').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');

        rows.forEach(row => {
            const name = row.dataset.name.toLowerCase();
            const nim = row.dataset.nim.toLowerCase();
            const school = row.dataset.school.toLowerCase();

            if (name.includes(searchTerm) || nim.includes(searchTerm) || school.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // View QR Code
    function viewQRCode(studentId) {
        fetch(`/students/${studentId}/qrcode`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.status === 404) {
                showNotification('No QR code found for this student', 'warning');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;

            if (data.success) {
                const modalBody = `
                    <div class="qr-display">
                        <div style="margin-bottom: 16px;">
                            <h3 style="margin: 0 0 4px; color: #0f172a;">${data.student_name}</h3>
                            <p style="margin: 0; color: #64748b; font-size: 14px;">NIM: ${data.student_nim}</p>
                        </div>
                        <div class="qr-image-container">
                            <img src="${data.qr_image_url}" alt="QR Code" id="qr-image">
                        </div>
                        <div class="qr-info">
                            <strong>QR Code Information</strong>
                            <p style="margin: 0;">
                                Status: <strong>${data.status}</strong><br>
                                Generated: ${data.created_at}
                            </p>
                        </div>
                        <div class="qr-code-text">${data.qr_code}</div>
                    </div>
                `;

                document.getElementById('qr-modal-body').innerHTML = modalBody;
                document.getElementById('qr-modal').classList.add('show');
                document.getElementById('modal-download-btn').dataset.studentId = studentId;
            } else {
                showNotification(data.error || 'Failed to load QR code', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error loading QR code', 'error');
        });
    }

    // Close QR Modal
    function closeQRModal() {
        document.getElementById('qr-modal').classList.remove('show');
    }

    // Download from Modal
    function downloadFromModal() {
        const studentId = document.getElementById('modal-download-btn').dataset.studentId;
        downloadQRCode(studentId);
    }

    // Download QR Code
    function downloadQRCode(studentId) {
        window.location.href = `/students/${studentId}/qrcode/download`;
    }

    // Regenerate QR Code
    function regenerateQRCode(studentId) {
        if (!confirm('Are you sure you want to regenerate the QR code for this student? The old QR code will no longer work.')) {
            return;
        }

        fetch(`/students/${studentId}/qrcode/regenerate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showNotification(data.message || 'Failed to regenerate QR code', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error regenerating QR code', 'error');
        });
    }

    // Notification helper
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 400px;
            padding: 16px 20px;
            border-radius: 12px;
            z-index: 9999;
            animation: slideInRight 0.3s ease;
        `;

        if (type === 'success') {
            notification.style.background = 'linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%)';
            notification.style.border = '1px solid #10b981';
            notification.style.color = '#166534';
        } else if (type === 'error') {
            notification.style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
            notification.style.border = '1px solid #ef4444';
            notification.style.color = '#991b1b';
        } else {
            notification.style.background = 'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)';
            notification.style.border = '1px solid #f59e0b';
            notification.style.color = '#92400e';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Close modal on backdrop click
    document.getElementById('qr-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQRModal();
        }
    });

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection

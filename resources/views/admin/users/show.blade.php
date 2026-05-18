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
                        {{ \App\Models\Role::displayName($user->role->name ?? 'N/A') }}
                    </span>
                </p>
            </div>

            <!-- Level -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Level</label>
                <p>
                    <span style="display: inline-block; background: #f3e8ff; color: #7c3aed; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                        {{ $user->level ? \App\Models\Level::displayName($user->level) : '-' }}
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
                <p style="font-size: 16px; font-weight: 500;">{{ optional($user->created_at)->format('M d, Y H:i') ?? 'N/A' }}</p>
            </div>

            <!-- Updated At -->
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Last Updated</label>
                <p style="font-size: 16px; font-weight: 500;">{{ optional($user->updated_at)->format('M d, Y H:i') ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Address -->
        @if ($user->address)
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Address</label>
                <p style="font-size: 16px; font-weight: 500; white-space: pre-wrap;">{{ $user->address }}</p>
            </div>
        @endif

        <!-- QR Code Section for Students -->
        @php
            $student = $user->student;
        @endphp

        @if($student)
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <h2 style="margin-bottom: 20px;">
                    <i class="bi bi-qr-code-scan" style="margin-right: 8px; color: #2563eb;"></i>Student QR Code Information
                </h2>

                @if($student->qrCode)
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start;">
                        <!-- QR Code Display -->
                        <div style="text-align: center; background: #f8fafc; padding: 20px; border-radius: 12px; border: 2px dashed #cbd5e1;">
                            <img id="qr-code-display" src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($student->qrCode->code) }}" 
                                 alt="QR Code" style="max-width: 250px; width: 100%; height: auto;">
                            <p style="margin: 12px 0 0; color: #64748b; font-size: 13px;">
                                Click to view full size
                            </p>
                        </div>

                        <!-- QR Code Info -->
                        <div>
                            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                    <strong>NIM/ID:</strong> {{ $student->nim ?? 'N/A' }}<br>
                                    <strong>School:</strong> {{ $student->school ?? 'N/A' }}<br>
                                    <strong>Status:</strong> 
                                    <span style="display: inline-block; background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 6px; font-size: 12px; margin-left: 4px;">
                                        {{ $student->qrCode->status ?? 'active' }}
                                    </span><br>
                                    <strong>Generated:</strong> {{ optional($student->qrCode->created_at)->format('M d, Y H:i') }}
                                </p>
                            </div>

                            <div style="background: #f0fdf4; border: 1px solid #16a34a; border-radius: 12px; padding: 12px; margin-bottom: 16px;">
                                <p style="margin: 0; color: #166534; font-size: 13px; word-break: break-all; font-family: monospace;">
                                    <strong style="display: block; margin-bottom: 4px;">QR Code:</strong>
                                    {{ $student->qrCode->code }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap;">
                        <button onclick="viewQRCode()" class="btn" style="background: #0369a1; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                            <i class="bi bi-eye"></i> View Full Size
                        </button>
                        <button onclick="downloadQRCode()" class="btn" style="background: #059669; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                            <i class="bi bi-download"></i> Download PNG
                        </button>
                        <button onclick="exportQRCode()" class="btn" style="background: #2563eb; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </button>
                        @if(auth()->user()->hasRole(\App\Models\Role::KESISWAAN))
                            <button onclick="regenerateQRCode({{ $student->id }})" class="btn" style="background: #dc2626; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                                <i class="bi bi-arrow-clockwise"></i> Regenerate
                            </button>
                        @endif
                    </div>
                @else
                    <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 12px; padding: 16px;">
                        <p style="margin: 0; color: #92400e;">
                            <i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>
                            <strong>No QR code found for this student.</strong>
                            @if(auth()->user()->hasRole(\App\Models\Role::KESISWAAN))
                                <button onclick="regenerateQRCode({{ $student->id }})" class="btn" style="background: #f59e0b; padding: 6px 12px; border-radius: 6px; cursor: pointer; border: none; color: white; font-weight: 600; margin-left: 8px;">
                                    <i class="bi bi-plus-circle"></i> Generate QR Code
                                </button>
                            @endif
                        </p>
                    </div>
                @endif
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

    <!-- QR Code Modal -->
    <div id="qr-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); max-width: 500px; width: 90%; padding: 24px; text-align: center; animation: slideIn 0.3s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 20px;">Student QR Code</h2>
                <button onclick="closeQRModal()" style="background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer;">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 24px; margin-bottom: 20px;">
                <img id="qr-modal-image" src="" alt="QR Code" style="max-width: 100%; width: 100%; height: auto;">
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; margin-bottom: 20px;">
                <button onclick="downloadQRCode()" class="btn" style="background: #059669; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                    <i class="bi bi-download"></i> Download PNG
                </button>
                <button onclick="exportQRCode()" class="btn" style="background: #2563eb; padding: 10px 16px; border-radius: 8px; cursor: pointer; border: none; color: white; font-weight: 600;">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </button>
            </div>
            <button onclick="closeQRModal()" class="btn" style="width: 100%; background: #f1f5f9; color: #334155; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Close
            </button>
        </div>
    </div>

    <style>
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
    </style>

    <script>
        function viewQRCode() {
            document.getElementById('qr-modal-image').src = document.getElementById('qr-code-display').src;
            document.getElementById('qr-modal').style.display = 'flex';
        }

        function closeQRModal() {
            document.getElementById('qr-modal').style.display = 'none';
        }

        function downloadQRCode() {
            const qrCode = document.getElementById('qr-code-display').src;
            const link = document.createElement('a');
            link.href = qrCode;
            link.download = 'qr-code-{{ $student->nim ?? $user->id }}.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function exportQRCode() {
            window.location.href = '{{ route('students.qrcode.export', $student) }}';
        }

        function regenerateQRCode(studentId) {
            if (!confirm('Are you sure you want to regenerate the QR code? The old QR code will no longer work.')) {
                return;
            }

            fetch(`/api/students/${studentId}/qrcode/regenerate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to regenerate QR code'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error regenerating QR code');
            });
        }

        // Close modal on backdrop click
        document.getElementById('qr-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });
    </script>
@endsection

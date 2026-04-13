@extends('layouts.app')

@section('title', 'Approve Student Absences')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-check-square" style="margin-right: 8px;"></i>Approve Student Absences</h1>
        <p>Review and approve pending student absences</p>
    </div>

    @if($absences->count() > 0)
        <div class="card">
            <form id="approvalForm" method="POST" action="{{ route('absence.bulkAction') }}">
                @csrf

                <!-- Select All Checkbox -->
                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" id="selectAll" style="margin-right: 10px; cursor: pointer;" 
                               onchange="toggleSelectAll(this)">
                        <strong>Select All Absences</strong>
                    </label>
                </div>

                <!-- Absences Table -->
                <div style="overflow-x: auto; margin-bottom: 30px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                                <th style="padding: 12px; text-align: center; font-weight: 600; width: 50px;">
                                    <input type="checkbox" id="tableSelectAll" style="cursor: pointer;"
                                           onchange="toggleTableCheckboxes(this)">
                                </th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Student Name</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Date</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($absences as $absence)
                                <tr style="border-bottom: 1px solid #eee; transition: background 0.3s;">
                                    <td style="padding: 12px; text-align: center;">
                                        <input type="checkbox" name="absence_ids[]" value="{{ $absence->id }}"
                                               class="absence-checkbox" style="cursor: pointer;"
                                               onchange="updateSelectAllCheckbox()">
                                    </td>
                                    <td style="padding: 12px;">
                                        <strong>{{ $absence->student->user->name }}</strong><br>
                                        <small style="color: #666;">NIM: {{ $absence->student->nim }}</small>
                                    </td>
                                    <td style="padding: 12px;">{{ $absence->absence_date->format('M d, Y') }}</td>
                                    <td style="padding: 12px;">
                                        <small style="word-break: break-word;">{{ $absence->location_name }}</small>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        @if($absence->selfie_path)
                                            <button type="button" onclick="viewPhoto('{{ asset('storage/' . $absence->selfie_path) }}')"
                                                    class="btn" style="padding: 6px 12px; font-size: 12px;">
                                                <i class="bi bi-image"></i>
                                            </button>
                                        @else
                                            <span style="color: #999;">—</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px;">
                                        <small style="color: #666;">{{ $absence->notes ?? '—' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Signature Section -->
                <div class="card-title" style="font-size: 18px; margin-bottom: 20px;">
                    <i class="bi bi-pen" style="margin-right: 8px;"></i>Teacher/Mentor Signature
                </div>

                <div style="margin-bottom: 30px;">
                    <div style="border: 2px solid #ddd; border-radius: 6px; background: #fafafa; padding: 10px;">
                        <p style="margin: 0 0 10px 0; color: #666; font-size: 14px;">
                            <strong>Sign below to approve or reject the selected absences:</strong>
                        </p>
                        <canvas id="signatureCanvas" 
                                style="border: 1px solid #ddd; border-radius: 4px; display: block; 
                                       cursor: crosshair; background: white; width: 100%; height: 200px;">
                        </canvas>
                        <input type="hidden" id="signatureInput" name="signature">
                        <button type="button" onclick="clearSignature()" class="btn btn-secondary" 
                                style="margin-top: 10px; font-size: 12px;">
                            <i class="bi bi-arrow-clockwise" style="margin-right: 4px;"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Action Selection -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Action</label>
                        <div style="display: flex; gap: 10px;">
                            <label style="display: flex; align-items: center; flex: 1; padding: 10px; 
                                         border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="action" value="approve" checked style="margin-right: 8px;">
                                <span><strong><i class="bi bi-check-lg"></i> Approve</strong></span>
                            </label>
                            <label style="display: flex; align-items: center; flex: 1; padding: 10px; 
                                         border: 1px solid #ddd; border-radius: 6px; cursor: pointer;">
                                <input type="radio" name="action" value="reject" style="margin-right: 8px;">
                                <span><strong><i class="bi bi-x-lg"></i> Reject</strong></span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Notes</label>
                        <textarea name="notes" placeholder="Optional notes for approval/rejection..."
                                  style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; 
                                         font-family: inherit; min-height: 40px; resize: vertical;"></textarea>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn" style="padding: 12px 30px;">
                        <i class="bi bi-check-lg" style="margin-right: 8px;"></i>Submit Approval
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 12px 30px;">Cancel</a>
                </div>
            </form>
        </div>
    @else
        <div class="card">
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                <p style="font-size: 18px; margin: 10px 0;">No pending absences to approve</p>
                <a href="{{ route('dashboard') }}" class="btn" style="margin-top: 20px;">Back to Dashboard</a>
            </div>
        </div>
    @endif

    <!-- Photo Modal -->
    <div id="photoModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
                                background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; padding: 20px; max-width: 600px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Student Selfie</h2>
                <button type="button" onclick="closePhotoModal()" style="background: none; border: none; 
                                                                         font-size: 24px; cursor: pointer;">×</button>
            </div>
            <img id="modalPhoto" style="width: 100%; border-radius: 6px;" src="" alt="Selfie">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <script>
        // Initialize signature pad
        const canvas = document.getElementById('signatureCanvas');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: '#f97316'
        });

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                signaturePad.clear();
            }, 250);
        });

        function clearSignature() {
            signaturePad.clear();
        }

        function toggleSelectAll(checkbox) {
            document.querySelectorAll('.absence-checkbox').forEach(cb => {
                cb.checked = checkbox.checked;
            });
            updateSelectAllCheckbox();
        }

        function toggleTableCheckboxes(checkbox) {
            document.querySelectorAll('.absence-checkbox').forEach(cb => {
                cb.checked = checkbox.checked;
            });
        }

        function updateSelectAllCheckbox() {
            const allCheckboxes = document.querySelectorAll('.absence-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.absence-checkbox:checked');
            document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
            document.getElementById('tableSelectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
        }

        function viewPhoto(photoUrl) {
            document.getElementById('modalPhoto').src = photoUrl;
            document.getElementById('photoModal').style.display = 'flex';
        }

        function closePhotoModal() {
            document.getElementById('photoModal').style.display = 'none';
        }

        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoModal();
            }
        });

        document.getElementById('approvalForm').addEventListener('submit', function(e) {
            const selectedAbsences = document.querySelectorAll('.absence-checkbox:checked');
            
            if (selectedAbsences.length === 0) {
                e.preventDefault();
                alert('Please select at least one absence to approve/reject.');
                return;
            }

            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Please sign before submitting.');
                return;
            }

            // Store signature
            document.getElementById('signatureInput').value = signaturePad.toDataURL();
        });

        // Close modal on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-qr-code" style="margin-right: 8px;"></i>QR Code Management</h1>
        <p>Generate and manage attendance QR codes</p>
        <div style="margin-top: 12px;">
            <button type="button" onclick="openGenerateModal()" class="btn" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-plus-circle"></i>Generate New QR Codes
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    @php
        // Separate active and inactive codes
        $activeQRCodes = $qrCodes->where('status', 'active')->sortByDesc('created_at');
        $inactiveQRCodes = $qrCodes->where('status', '!=', 'active')->sortByDesc('created_at');
        $allSorted = $activeQRCodes->merge($inactiveQRCodes);
    @endphp
    
    @if($qrCodes->count() > 0)
        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">QR Code</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Date</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Created By</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Scans</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allSorted as $qr)
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.3s; @if($qr->status === 'active' && $qr->isActive()) background: #f0ffe7; @endif">
                                <td style="padding: 12px;">
                                    <code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-family: monospace;">{{ $qr->code }}</code>
                                </td>
                                <td style="padding: 12px;">{{ $qr->qr_date->format('M d, Y \\a\\t H:i') }}</td>
                                <td style="padding: 12px;">
                                    <small style="color: #666;">{{ $qr->creator->name }}</small>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <strong>{{ $qr->absences->count() }}</strong>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($qr->status === 'active' && $qr->isActive())
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #dcfce7; color: #166534; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>Active
                                        </span>
                                    @elseif($qr->status === 'expired')
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #fee2e2; color: #991b1b; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-clock-history" style="margin-right: 4px;"></i>Expired
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: #f5f5f5; color: #666; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i>Disabled
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <button type="button" onclick="showQRModal('{{ $qr->id }}')" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" onclick="downloadQRImage('{{ $qr->code }}')" class="btn" style="padding: 6px 12px; font-size: 12px; margin-right: 5px;">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    @if($qr->status === 'active')
                                        <button type="button" onclick="deactivateQRCode('{{ $qr->id }}')" class="btn" style="padding: 6px 12px; font-size: 12px; background: #fed7aa; color: #ea580c;">
                                            <i class="bi bi-ban"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($qrCodes->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $qrCodes->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="card">
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="bi bi-qr-code" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                <p style="font-size: 18px; margin: 10px 0;">No QR codes generated yet</p>
                <button type="button" onclick="openGenerateModal()" class="btn" style="margin-top: 20px;">
                    <i class="bi bi-plus-circle" style="margin-right: 4px;"></i>Generate First QR Code
                </button>
            </div>
        </div>
    @endif

    <!-- QR Details Modal -->
    <div id="qr-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; padding: 30px; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 600;">QR Code Details</h2>
                <button type="button" onclick="closeQRModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">×</button>
            </div>

            <div id="qr-modal-content" style="text-align: center;">
                <div style="padding: 30px; background: #f5f5f5; border-radius: 6px;">
                    <p style="color: #666;">Loading...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showQRModal(qrCodeId) {
            const modal = document.getElementById('qr-modal');
            const content = document.getElementById('qr-modal-content');
            
            // Show loading state
            content.innerHTML = `
                <div style="padding: 30px; background: #f5f5f5; border-radius: 6px; text-align: center;">
                    <p style="color: #666;">Loading QR code details...</p>
                </div>
            `;
            modal.style.display = 'flex';
            
            // Fetch QR code details via AJAX
            fetch(`/qrcode/${qrCodeId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch QR code details');
                }
                return response.json();
            })
            .then(data => {
                // Build scans history HTML
                let scansHtml = '';
                if (data.scan_history && data.scan_history.length > 0) {
                    scansHtml = data.scan_history.map(scan => `
                        <div style="padding: 12px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; margin-bottom: 4px;">${scan.student_name}</div>
                                <div style="font-size: 12px; color: #666;">NIM: ${scan.nim} | ${scan.scanned_at}</div>
                                <div style="font-size: 12px; color: #666; margin-top: 2px;">📍 ${scan.location}</div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    scansHtml = '<div style="padding: 12px; color: #999; text-align: center;">No scans yet</div>';
                }
                
                content.innerHTML = `
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <div style="padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">QR CODE</div>
                            <code style="font-family: monospace; font-size: 12px; font-weight: 600; word-break: break-all;">${data.code}</code>
                        </div>
                        <div style="padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">DATE</div>
                            <div style="font-weight: 600;">${data.date}</div>
                        </div>
                        <div style="padding: 15px; background: #f9fafb; border-radius: 6px;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">TOTAL SCANS</div>
                            <div style="font-size: 20px; font-weight: 700; color: #f97316;">${data.scans}</div>
                        </div>
                    </div>

                    <div style="padding: 20px; background: #f9fafb; border-radius: 6px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600;">QR Code Image</h3>
                        <img src="${data.qr_image}" style="max-width: 250px; height: auto; border: 1px solid #ddd; padding: 10px; background: white; border-radius: 4px;" />
                    </div>

                    <div id="modal-scan-history" style="margin-top: 20px;">
                        <h3 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 600;">Recent Scans</h3>
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #eee; border-radius: 6px; background: white;">
                            ${scansHtml}
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = '<div style="color: #ef4444; padding: 20px; text-align: center;"><strong>Error loading QR code details</strong><p style="margin-top: 10px; font-size: 14px;">' + error.message + '</p></div>';
            });
        }

        function closeQRModal() {
            document.getElementById('qr-modal').style.display = 'none';
        }

        function downloadQRImage(code) {
            const qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data=" + encodeURIComponent(code);
            
            // Create a temporary link and click it
            const link = document.createElement('a');
            link.href = qrImageUrl;
            link.download = code + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            showToast('QR code image downloaded!', 'success');
        }

        function deactivateQRCode(qrCodeId) {
            if (!confirm('Are you sure you want to disable this QR code?')) {
                return;
            }

            fetch(`/qrcode/${qrCodeId}/deactivate`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    showToast('QR code disabled successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    return response.json().then(data => {
                        throw new Error(data.error || 'Error disabling QR code');
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Error disabling QR code', 'error');
            });
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';
            const icon = type === 'success' ? 'check-circle-fill' : type === 'error' ? 'x-circle-fill' : 'info-circle-fill';
            
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 24px;
                border-radius: 8px;
                background: ${bgColor};
                color: white;
                font-weight: 600;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                z-index: 2000;
            `;
            toast.innerHTML = `<i class="bi bi-${icon}" style="margin-right: 8px;"></i>${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }

        // Close modal when clicking outside
        document.getElementById('qr-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });

        // Open Generate Modal
        function openGenerateModal() {
            const modal = document.getElementById('generate-modal');
            if (modal) {
                modal.style.display = 'flex';
                // Reset form when opening
                const form = document.getElementById('generate-form');
                if (form) form.reset();
                // Focus on first input
                document.querySelector('input[name="qr_date"]')?.focus();
            }
        }

        function closeGenerateModal() {
            const modal = document.getElementById('generate-modal');
            if (modal) {
                modal.style.display = 'none';
            }
            const form = document.getElementById('generate-form');
            if (form) form.reset();
        }

        // Close loading modal
        function closeLoadingModal() {
            const loadingModal = document.getElementById('loading-modal');
            if (loadingModal) {
                loadingModal.style.display = 'none';
            }
        }

        // Handle Generate Form Submit
        function handleGenerateFormSubmit() {
            try {
                const qrDate = document.querySelector('input[name="qr_date"]')?.value;
                const quantity = document.querySelector('input[name="quantity"]')?.value;
                const expiresAt = document.querySelector('input[name="expires_at"]')?.value;

                // Validate form inputs
                if (!qrDate || !quantity) {
                    showToast('Please fill in all required fields', 'warning');
                    return;
                }

                if (quantity < 1 || quantity > 100) {
                    showToast('Quantity must be between 1 and 100', 'warning');
                    return;
                }

                if (expiresAt && qrDate > expiresAt) {
                    showToast('Expiration date must be after the attendance date', 'warning');
                    return;
                }

                // Create FormData
                const form = document.getElementById('generate-form');
                if (!form) {
                    showToast('Form not found', 'error');
                    return;
                }

                const formData = new FormData(form);

                // Show loading modal
                const loadingModal = document.getElementById('loading-modal');
                if (loadingModal) {
                    loadingModal.style.display = 'flex';
                    const statusEl = document.getElementById('loading-status');
                    if (statusEl) statusEl.textContent = `Generating ${quantity} QR code(s)...`;
                }

                // Submit via AJAX
                fetch('{{ route('qrcode.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        closeLoadingModal();
                        return response.json().then(data => {
                            throw new Error(data.error || `Error: ${response.status}`);
                        }).catch(err => {
                            throw new Error('Error response from server. Please check console.');
                        });
                    }
                    
                    return response.json().then(data => {
                        // Keep loading modal visible for 3 seconds to show the complete process
                        const statusEl = document.getElementById('loading-status');
                        if (statusEl) statusEl.textContent = `✓ QR code(s) generated successfully!`;
                        
                        showToast('✓ ' + (data.success || 'QR codes generated successfully!'), 'success');
                        
                        // Close modals and reload after 3 seconds
                        setTimeout(() => {
                            closeLoadingModal();
                            closeGenerateModal();
                            location.reload();
                        }, 3000);
                    });
                })
                .catch(error => {
                    closeLoadingModal();
                    console.error('Submission Error:', error);
                    showToast(error.message || 'Error generating QR codes', 'error');
                });
            } catch (error) {
                closeLoadingModal();
                console.error('Exception:', error);
                showToast('An unexpected error occurred', 'error');
            }
        }

        // Close modal when clicking outside
        document.getElementById('generate-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGenerateModal();
            }
        });
    </script>

    <!-- Generate QR Codes Modal -->
    <div id="generate-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <!-- Modal Header -->
            <div style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 18px; font-weight: 600;">
                    <i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Generate New QR Codes
                </h2>
                <button type="button" onclick="closeGenerateModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">×</button>
            </div>

            <!-- Modal Body -->
            <div style="padding: 20px;">
                <form id="generate-form">
                    @csrf

                    <!-- Date & Time Selection -->
                    <div style="margin-bottom: 20px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Attendance Date <span style="color: #ef4444;">*</span></label>
                                <input type="date" name="qr_date" required
                                       value="{{ now()->format('Y-m-d') }}"
                                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Attendance Time <span style="color: #ef4444;">*</span></label>
                                <input type="time" name="qr_time" required
                                       value="{{ now()->format('H:i') }}"
                                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            </div>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Number of QR Codes <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="quantity" required min="1" max="100"
                               value="1"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <small style="color: #666; display: block; margin-top: 5px;">Create 1-100 codes at once</small>
                    </div>

                    <!-- Expiration Date -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Expiration Date (Optional)</label>
                        <input type="date" name="expires_at"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        <small style="color: #666; display: block; margin-top: 5px;">Leave empty if no expiration</small>
                    </div>

                    <!-- Notes -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Notes (Optional)</label>
                        <textarea name="notes" placeholder="e.g., For Class A, Period 1"
                                  style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; min-height: 70px; resize: vertical;"></textarea>
                    </div>

                    <!-- Info Box -->
                    <div style="padding: 12px; background: #fed7aa; border: 2px solid #ea580c; border-radius: 6px; margin-bottom: 20px; color: #92400e; font-size: 13px;">
                        <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
                        <strong>Tip:</strong> QR codes are unique and reusable. Multiple students can scan the same code on the same day.
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 15px 20px; background: #f5f5f5; border-top: 1px solid #ddd; display: flex; gap: 10px;">
                <button type="button" onclick="handleGenerateFormSubmit()" class="btn" style="flex: 1; padding: 10px; background: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="bi bi-check-lg" style="margin-right: 8px;"></i>Generate
                </button>
                <button type="button" onclick="closeGenerateModal()" class="btn btn-secondary" style="flex: 1; padding: 10px;">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loading-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 40px; text-align: center; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
            <!-- Spinner -->
            <div style="display: inline-block; margin-bottom: 20px;">
                <div style="
                    width: 50px;
                    height: 50px;
                    border: 5px solid #f0f0f0;
                    border-top: 5px solid #f97316;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                "></div>
            </div>

            <!-- Message -->
            <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600; color: #333;">
                Generating QR Codes
            </h3>
            <p style="margin: 0; color: #666; font-size: 14px;">
                Please wait while we create your QR codes...
            </p>

            <!-- Progress Details -->
            <div style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 6px;">
                <p style="margin: 0; color: #666; font-size: 12px;">
                    <i class="bi bi-hourglass-split" style="margin-right: 8px; color: #f97316;"></i>
                    <span id="loading-status">Processing...</span>
                </p>
            </div>
        </div>

        <style>
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
        </style>
    </div>
@endsection

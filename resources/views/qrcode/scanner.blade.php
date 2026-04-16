@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan" style="margin-right: 8px;"></i>Mark Attendance - QR Code Scanner</h1>
        <p>Scan the QR code provided by your instructor to mark your attendance</p>
    </div>

    @if($alreadyScanned)
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>✓ You have already marked your attendance today!</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">Scanned at: {{ $alreadyScanned->scanned_qr_at->format('H:i:s') }}</p>
        </div>
    @elseif(!$todayQRCodes)
        <div style="padding: 15px 20px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; margin-bottom: 20px; color: #92400e;">
            <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
            <strong>No QR codes available today</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">Wait for your instructor to generate codes for today.</p>
        </div>
    @endif

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <!-- Scanner Input -->
        <div style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-camera" style="margin-right: 8px;"></i>Scan QR Code
            </h3>
            <div style="padding: 15px; background: #f5f5f5; border-radius: 6px; text-align: center;">
                <video id="scanner-video" style="width: 100%; max-width: 300px; margin: 0 auto; border-radius: 6px; display: none;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                
                <input type="text" id="qr-input" placeholder="🎯 Tap here and scan QR code or paste manually"
                       style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; text-align: center;">
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-lightbulb"></i> Point your device camera at the QR code
            </small>
        </div>

        <!-- Status Messages -->
        <div id="status-message" style="display: none; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px;"></div>

        <!-- Manual Code Entry -->
        <div style="margin-bottom: 20px; padding-top: 20px; border-top: 1px solid #eee;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-keyboard" style="margin-right: 8px;"></i>Manual Entry
            </h3>
            <form id="manual-form" style="display: flex; gap: 10px;">
                <input type="text" id="manual-code" placeholder="Enter QR code manually (e.g., QR-A1B2C3D4)"
                       style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <button type="submit" class="btn" style="padding: 12px 20px;">
                    <i class="bi bi-check-lg"></i>Submit
                </button>
            </form>
        </div>

        <!-- Tips -->
        <div style="padding: 15px; background: #e0f2fe; border: 2px solid #0284c7; border-radius: 6px; color: #0c4a6e; font-size: 13px;">
            <i class="bi bi-lightbulb" style="margin-right: 8px;"></i>
            <strong>Tips:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>Make sure the QR code is well-lit and clearly visible</li>
                <li>You can only scan once per day</li>
                <li>QR code must be for today's date</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
        const qrInput = document.getElementById('qr-input');
        const manualForm = document.getElementById('manual-form');
        const manualCode = document.getElementById('manual-code');
        const statusMessage = document.getElementById('status-message');

        // Auto-focus on input
        qrInput.focus();

        // Handle QR input
        qrInput.addEventListener('change', function() {
            if (this.value.trim()) {
                submitCode(this.value.trim());
            }
        });

        // Handle manual form submission
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const code = manualCode.value.trim();
            if (code) {
                submitCode(code);
            }
        });

        function submitCode(code) {
            showStatus('Processing...', 'info');

            // Get geolocation if available
            let latitude = null, longitude = null, locationName = null;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        latitude = position.coords.latitude;
                        longitude = position.coords.longitude;
                        sendScanRequest(code, latitude, longitude);
                    },
                    error => {
                        // Send without location if permission denied
                        sendScanRequest(code, null, null);
                    }
                );
            } else {
                sendScanRequest(code, null, null);
            }
        }

        function sendScanRequest(code, latitude, longitude) {
            fetch('{{ route("qrcode.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    code: code,
                    latitude: latitude,
                    longitude: longitude,
                    location_name: 'Attendance Location'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus(data.message, 'success');
                    qrInput.value = '';
                    manualCode.value = '';
                    
                    // Reload page after 2 seconds
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showStatus(data.message, 'error');
                    qrInput.value = '';
                    manualCode.value = '';
                    qrInput.focus();
                }
            })
            .catch(error => {
                showStatus('Error: ' + error.message, 'error');
                qrInput.value = '';
                manualCode.value = '';
                qrInput.focus();
            });
        }

        function showStatus(message, type) {
            const bgColor = type === 'success' ? '#dcfce7' : type === 'error' ? '#fee2e2' : '#fef3c7';
            const textColor = type === 'success' ? '#166534' : type === 'error' ? '#991b1b' : '#92400e';
            const icon = type === 'success' ? 'check-circle-fill' : type === 'error' ? 'x-circle-fill' : 'info-circle';

            statusMessage.innerHTML = `<i class="bi bi-${icon}" style="margin-right: 8px;"></i>${message}`;
            statusMessage.style.background = bgColor;
            statusMessage.style.color = textColor;
            statusMessage.style.display = 'block';
        }
    </script>
@endsection

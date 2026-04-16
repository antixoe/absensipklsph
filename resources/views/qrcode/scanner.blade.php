@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan" style="margin-right: 8px;"></i>Mark Attendance - QR & Selfie</h1>
        <p>Scan QR code, take a selfie, and share your location to mark attendance</p>
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

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <!-- Progress Indicators -->
        <div style="display: flex; gap: 15px; margin-bottom: 30px; padding: 15px; background: #f5f5f5; border-radius: 6px;">
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 5px;" id="step1-icon">⭕</div>
                <div style="font-weight: 600; font-size: 14px;">Step 1: QR Scan</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 5px;" id="step2-icon">⭕</div>
                <div style="font-weight: 600; font-size: 14px;">Step 2: Selfie</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="font-size: 24px; margin-bottom: 5px;" id="step3-icon">⭕</div>
                <div style="font-weight: 600; font-size: 14px;">Step 3: Location</div>
            </div>
        </div>

        <!-- Step 1: QR Code Scanner -->
        <div id="step1" style="margin-bottom: 30px; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; background: #fafafa;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-qr-code" style="margin-right: 8px;"></i>Step 1: Scan QR Code
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px; text-align: center;">
                <video id="scanner-video" style="width: 100%; max-width: 300px; margin: 0 auto; border-radius: 6px; display: none;"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                
                <input type="text" id="qr-input" placeholder="🎯 Tap here and scan QR code"
                       style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; text-align: center;">
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-lightbulb"></i> Point your device camera at the QR code
            </small>
            
            <!-- Manual Code Entry -->
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <small style="color: #666; display: block; margin-bottom: 10px;">Or enter manually:</small>
                <form id="manual-form" style="display: flex; gap: 10px;">
                    <input type="text" id="manual-code" placeholder="e.g., QR-A1B2C3D4"
                           style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    <button type="submit" class="btn" style="padding: 10px 15px;">Submit</button>
                </form>
            </div>
        </div>

        <!-- Step 2: Selfie Capture -->
        <div id="step2" style="margin-bottom: 30px; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; background: #fafafa; opacity: 0.5; pointer-events: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Step 2: Take Selfie
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div style="text-align: center;">
                    <video id="selfie-video" style="width: 100%; max-width: 300px; margin: 0 auto; border-radius: 6px; display: none; background: #000;"></video>
                    <canvas id="selfie-canvas" style="display: none;"></canvas>
                    <img id="selfie-preview" style="width: 100%; max-width: 300px; margin: 0 auto; border-radius: 6px; display: none;">
                    
                    <div id="selfie-placeholder" style="width: 100%; max-width: 300px; margin: 0 auto; aspect-ratio: 4/3; background: #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #999;">
                        <i class="bi bi-camera" style="font-size: 48px;"></i>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="button" id="start-selfie-btn" class="btn" style="flex: 1; padding: 10px 15px;">
                        <i class="bi bi-camera" style="margin-right: 8px;"></i>Start Camera
                    </button>
                    <button type="button" id="take-selfie-btn" class="btn" style="flex: 1; padding: 10px 15px; display: none;">
                        <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Take Photo
                    </button>
                    <button type="button" id="retake-selfie-btn" class="btn btn-secondary" style="flex: 1; padding: 10px 15px; display: none;">
                        <i class="bi bi-arrow-counterclockwise" style="margin-right: 8px;"></i>Retake
                    </button>
                </div>
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-lightbulb"></i> Ensure your face is clearly visible
            </small>
        </div>

        <!-- Step 3: Location Confirmation -->
        <div id="step3" style="margin-bottom: 30px; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; background: #fafafa; opacity: 0.5; pointer-events: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-geo-alt-fill" style="margin-right: 8px;"></i>Step 3: Share Location
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div id="location-status" style="padding: 12px; background: #e0f2fe; border: 1px solid #0284c7; border-radius: 6px; color: #0c4a6e; text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-hourglass-split" style="margin-right: 8px;"></i>Waiting for QR scan...
                </div>
                
                <div id="location-info" style="padding: 12px; background: #f0fdf4; border: 1px solid #16a34a; border-radius: 6px; color: #166534; display: none;">
                    <strong style="display: block; margin-bottom: 8px;">📍 Your Location:</strong>
                    <div style="font-size: 13px;">
                        <div>Latitude: <span id="location-lat">—</span></div>
                        <div>Longitude: <span id="location-lng">—</span></div>
                    </div>
                </div>
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-info-circle"></i> Your actual location will be used for verification
            </small>
        </div>

        <!-- Status Messages -->
        <div id="status-message" style="display: none; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px;"></div>

        <!-- Submit Button -->
        <div style="display: flex; gap: 10px;">
            <button type="button" id="submit-attendance-btn" class="btn" style="flex: 1; padding: 12px 20px; opacity: 0.5; pointer-events: none;">
                <i class="bi bi-check-circle" style="margin-right: 8px;"></i>Complete Attendance
            </button>
        </div>

        <!-- Tips -->
        <div style="margin-top: 25px; padding: 15px; background: #e0f2fe; border: 2px solid #0284c7; border-radius: 6px; color: #0c4a6e; font-size: 13px;">
            <i class="bi bi-lightbulb" style="margin-right: 8px;"></i>
            <strong>Requirements:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>✓ Valid QR code for today's date</li>
                <li>✓ Clear selfie photo</li>
                <li>✓ Location permission granted</li>
                <li>You can only submit once per day</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
        // State Management
        const state = {
            qrCode: null,
            selfieBlob: null,
            latitude: null,
            longitude: null
        };

        // DOM Elements
        const qrInput = document.getElementById('qr-input');
        const manualForm = document.getElementById('manual-form');
        const manualCode = document.getElementById('manual-code');
        const statusMessage = document.getElementById('status-message');
        
        // Selfie Elements
        const startSelfieBtnEl = document.getElementById('start-selfie-btn');
        const takeSelfieBtnEl = document.getElementById('take-selfie-btn');
        const retakeSelfieBtnEl = document.getElementById('retake-selfie-btn');
        const selfieVideoEl = document.getElementById('selfie-video');
        const selfieCanvasEl = document.getElementById('selfie-canvas');
        const selfiePreviewEl = document.getElementById('selfie-preview');
        const selfiePlaceholderEl = document.getElementById('selfie-placeholder');
        
        // Submit Button
        const submitBtnEl = document.getElementById('submit-attendance-btn');

        // Initialize
        qrInput.focus();

        // ==================== QR SCANNING ====================
        qrInput.addEventListener('change', function() {
            if (this.value.trim()) {
                validateAndSetQRCode(this.value.trim());
            }
        });

        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const code = manualCode.value.trim();
            if (code) {
                validateAndSetQRCode(code);
            }
        });

        function validateAndSetQRCode(code) {
            showStatus('Validating QR code...', 'info');
            
            // For now, we'll just accept the code
            // In a real scenario, you'd validate it with the server first
            state.qrCode = code;
            
            // Update UI
            qrInput.value = code;
            qrInput.disabled = true;
            manualCode.disabled = true;
            manualForm.querySelector('button').disabled = true;
            
            // Update step 1 indicator
            document.getElementById('step1-icon').textContent = '✅';
            
            // Enable step 2
            document.getElementById('step2').style.opacity = '1';
            document.getElementById('step2').style.pointerEvents = 'auto';
            
            showStatus('✓ QR code validated! Now take a selfie.', 'success');
        }

        // ==================== SELFIE CAPTURE ====================
        let selfieStream = null;

        startSelfieBtnEl.addEventListener('click', async function() {
            try {
                selfieStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                });
                
                selfieVideoEl.srcObject = selfieStream;
                selfieVideoEl.style.display = 'block';
                selfiePlaceholderEl.style.display = 'none';
                
                startSelfieBtnEl.style.display = 'none';
                takeSelfieBtnEl.style.display = 'block';
            } catch (error) {
                showStatus('Camera access denied. Please enable camera permissions.', 'error');
            }
        });

        takeSelfieBtnEl.addEventListener('click', function() {
            const context = selfieCanvasEl.getContext('2d');
            selfieCanvasEl.width = selfieVideoEl.videoWidth;
            selfieCanvasEl.height = selfieVideoEl.videoHeight;
            context.drawImage(selfieVideoEl, 0, 0);
            
            // Convert canvas to blob
            selfieCanvasEl.toBlob(blob => {
                state.selfieBlob = blob;
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    selfiePreviewEl.src = e.target.result;
                    selfiePreviewEl.style.display = 'block';
                    selfieVideoEl.style.display = 'none';
                    selfiePlaceholderEl.style.display = 'none';
                    
                    // Update buttons
                    takeSelfieBtnEl.style.display = 'none';
                    retakeSelfieBtnEl.style.display = 'block';
                    
                    // Stop stream
                    if (selfieStream) {
                        selfieStream.getTracks().forEach(track => track.stop());
                    }
                    
                    // Update step 2 indicator
                    document.getElementById('step2-icon').textContent = '✅';
                    
                    // Enable step 3
                    document.getElementById('step3').style.opacity = '1';
                    document.getElementById('step3').style.pointerEvents = 'auto';
                    
                    // Request location
                    requestLocation();
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.95);
        });

        retakeSelfieBtnEl.addEventListener('click', async function() {
            try {
                selfieStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                });
                
                selfieVideoEl.srcObject = selfieStream;
                selfieVideoEl.style.display = 'block';
                selfiePreviewEl.style.display = 'none';
                selfiePlaceholderEl.style.display = 'none';
                
                takeSelfieBtnEl.style.display = 'block';
                retakeSelfieBtnEl.style.display = 'none';
            } catch (error) {
                showStatus('Camera access error.', 'error');
            }
        });

        // ==================== LOCATION ACCESS ====================
        function requestLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        state.latitude = position.coords.latitude;
                        state.longitude = position.coords.longitude;
                        
                        // Update location display
                        document.getElementById('location-lat').textContent = state.latitude.toFixed(6);
                        document.getElementById('location-lng').textContent = state.longitude.toFixed(6);
                        document.getElementById('location-info').style.display = 'block';
                        document.getElementById('location-status').style.display = 'none';
                        
                        // Update step 3 indicator
                        document.getElementById('step3-icon').textContent = '✅';
                        
                        // Enable submit button
                        enableSubmitButton();
                    },
                    error => {
                        // Try without location
                        showStatus('Location permission denied. You can still submit without location.', 'warning');
                        state.latitude = null;
                        state.longitude = null;
                        
                        // Still enable submit
                        document.getElementById('step3-icon').textContent = '⚠️';
                        enableSubmitButton();
                    }
                );
            } else {
                showStatus('Geolocation is not supported by your browser.', 'error');
            }
        }

        function enableSubmitButton() {
            submitBtnEl.style.opacity = '1';
            submitBtnEl.style.pointerEvents = 'auto';
        }

        // ==================== SUBMIT ====================
        submitBtnEl.addEventListener('click', function() {
            if (!state.qrCode) {
                showStatus('Please scan a QR code first.', 'error');
                return;
            }
            
            if (!state.selfieBlob) {
                showStatus('Please take a selfie first.', 'error');
                return;
            }
            
            submitAttendance();
        });

        function submitAttendance() {
            showStatus('Processing attendance...', 'info');
            submitBtnEl.disabled = true;
            
            const formData = new FormData();
            formData.append('code', state.qrCode);
            formData.append('selfie', state.selfieBlob, 'selfie.jpg');
            formData.append('latitude', state.latitude);
            formData.append('longitude', state.longitude);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("qrcode.scan") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus(data.message, 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showStatus(data.message, 'error');
                    submitBtnEl.disabled = false;
                }
            })
            .catch(error => {
                showStatus('Error: ' + error.message, 'error');
                submitBtnEl.disabled = false;
            });
        }

        // ==================== UTILITIES ====================
        function showStatus(message, type) {
            const bgColor = type === 'success' ? '#dcfce7' : type === 'error' ? '#fee2e2' : type === 'warning' ? '#fef3c7' : '#dbeafe';
            const textColor = type === 'success' ? '#166534' : type === 'error' ? '#991b1b' : type === 'warning' ? '#92400e' : '#0c4a6e';
            const icon = type === 'success' ? 'check-circle-fill' : type === 'error' ? 'x-circle-fill' : type === 'warning' ? 'exclamation-circle-fill' : 'info-circle';

            statusMessage.innerHTML = `<i class="bi bi-${icon}" style="margin-right: 8px;"></i>${message}`;
            statusMessage.style.background = bgColor;
            statusMessage.style.color = textColor;
            statusMessage.style.display = 'block';
            
            // Scroll to message
            statusMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    </script>

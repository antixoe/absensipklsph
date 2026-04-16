@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan" style="margin-right: 8px;"></i>Mark Attendance</h1>
        <p>Scan QR code, Take Selfie, Share Location</p>
    </div>

    @if($currentUserStudent && $todayAbsence)
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>You have already marked your attendance today</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">Submitted at: {{ $todayAbsence->created_at->format('H:i:s') }}</p>
        </div>
    @endif

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <!-- Progress Indicators -->
        <div style="display: flex; gap: 15px; margin-bottom: 30px; padding: 15px; background: #f5f5f5; border-radius: 6px;">
            <div style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #f97316; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step1-icon">1</div>
                <div style="font-weight: 600; font-size: 13px;">Scan QR</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #d1d5db; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step2-icon">2</div>
                <div style="font-weight: 600; font-size: 13px;">Selfie</div>
            </div>
            <div style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #d1d5db; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step3-icon">3</div>
                <div style="font-weight: 600; font-size: 13px;">Location</div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="status-message" style="display: none; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;"></div>

        <!-- Step 1: QR Code Scanner -->
        <div id="step1-container" style="margin-bottom: 30px; padding: 20px; border: 3px solid #f97316; border-radius: 8px; background: #fafafa;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-qr-code" style="margin-right: 8px;"></i>Step 1: Scan QR Code with Camera
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px; text-align: center;">
                <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; overflow: hidden; border-radius: 6px;">
                    <video id="qr-video" autoplay playsinline muted style="width: 100%; height: 100%; aspect-ratio: 1; display: none; background: #000; object-fit: cover;"></video>
                    <canvas id="qr-canvas" style="display: none;"></canvas>
                    
                    <!-- Scanning Animation Overlay -->
                    <div id="qr-scan-overlay" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; aspect-ratio: 1; border: 3px solid #f97316; border-radius: 6px; animation: scanPulse 1.5s ease-in-out infinite; pointer-events: none;">
                        <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #f97316, transparent); animation: scanLine 1s ease-in-out infinite;"></div>
                    </div>
                    
                    <div id="qr-placeholder" style="width: 100%; max-width: 400px; aspect-ratio: 1; background: #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #999;">
                        <i class="bi bi-qr-code" style="font-size: 80px;"></i>
                    </div>
                </div>
                <button type="button" id="start-qr-scanner-btn" class="btn" style="width: 100%; margin-top: 15px; padding: 10px 15px;">
                    <i class="bi bi-camera" style="margin-right: 8px;"></i>Start QR Scanner
                </button>
                <button type="button" id="stop-qr-scanner-btn" class="btn btn-secondary" style="width: 100%; margin-top: 10px; padding: 10px 15px; display: none;">
                    <i class="bi bi-stop-circle" style="margin-right: 8px;"></i>Stop Scanner
                </button>
            </div>
            <small style="color: #666; display: block; margin-top: 10px; text-align: center;">
                <i class="bi bi-info-circle"></i> Point your camera at the QR code to scan it automatically
            </small>
        </div>

        <!-- Step 2: Selfie Capture -->
        <div id="step2-container" style="margin-bottom: 30px; padding: 20px; border: 2px solid #ddd; border-radius: 8px; background: #fafafa; display: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Step 2: Take Selfie
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div style="text-align: center;">
                    <video id="selfie-video" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 6px; display: none; background: #000;"></video>
                    <img id="selfie-preview" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 6px; display: none;">
                    
                    <div id="selfie-placeholder" style="width: 100%; max-width: 400px; margin: 0 auto; aspect-ratio: 3/4; background: #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #999;">
                        <i class="bi bi-camera" style="font-size: 64px;"></i>
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
                <i class="bi bi-lightbulb"></i> Make sure your face is clearly visible in the camera
            </small>
        </div>

        <!-- Step 3: Location & IP Detection -->
        <div id="step3-container" style="margin-bottom: 30px; padding: 20px; border: 2px solid #ddd; border-radius: 8px; background: #fafafa; display: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-geo-alt-fill" style="margin-right: 8px;"></i>Step 3: Location Detected
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div id="location-info" style="padding: 15px; background: #f0fdf4; border: 2px solid #16a34a; border-radius: 6px; color: #166534;">
                    <strong style="display: block; margin-bottom: 12px;">Location Information</strong>
                    <div style="font-size: 13px; line-height: 2;">
                        <div><i class="bi bi-geo-alt" style="margin-right: 5px;"></i><strong>Latitude:</strong> <span id="location-lat">Detecting...</span></div>
                        <div><i class="bi bi-geo-alt" style="margin-right: 5px;"></i><strong>Longitude:</strong> <span id="location-lng">Detecting...</span></div>
                        <div><i class="bi bi-globe" style="margin-right: 5px;"></i><strong>IP Address:</strong> <span id="location-ip">Detecting...</span></div>
                        <div><i class="bi bi-clock" style="margin-right: 5px;"></i><strong>Time:</strong> <span id="location-time">—</span></div>
                    </div>
                </div>
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-info-circle"></i> Your location and IP have been automatically detected
            </small>
        </div>

        <!-- Submit Button -->
        <div style="display: flex; gap: 10px;">
            <button type="button" id="submit-attendance-btn" class="btn" style="flex: 1; padding: 12px 20px; display: none;">
                <i class="bi bi-check-circle" style="margin-right: 8px;"></i>Complete Attendance
            </button>
        </div>

        <!-- Tips -->
        <div style="margin-top: 25px; padding: 15px; background: #fef3c7; border: 2px solid #ea580c; border-radius: 6px; color: #92400e; font-size: 13px;">
            <i class="bi bi-lightbulb" style="margin-right: 8px;"></i>
            <strong>Tips for QR Code Scanning:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>Make sure the QR code is well-lit and clearly visible</li>
                <li>Keep the QR code within 10-30 cm from your camera</li>
                <li>Hold the device steady at a slight angle (avoid glare)</li>
                <li>The QR code will automatically detect - no manual input needed</li>
                <li>After QR detected → take selfie → location detected → submit</li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <style>
        @keyframes scanPulse {
            0%, 100% { box-shadow: inset 0 0 20px rgba(249, 115, 22, 0.3); }
            50% { box-shadow: inset 0 0 40px rgba(249, 115, 22, 0.6); }
        }
        
        @keyframes scanLine {
            0% { top: 0; opacity: 0; }
            50% { opacity: 1; }
            100% { top: calc(100% - 2px); opacity: 0; }
        }
    </style>
    <script>
        // Wait for jsQR to load
        let jsQRReady = false;
        const checkJsQR = setInterval(() => {
            if (typeof jsQR !== 'undefined') {
                jsQRReady = true;
                console.log('✓ jsQR library loaded');
                clearInterval(checkJsQR);
            }
        }, 100);
        
        setTimeout(() => {
            if (!jsQRReady) {
                console.warn('⚠ jsQR library not loaded after 5 seconds, proceeding anyway');
                jsQRReady = true;
            }
        }, 5000);
        
        // State Management
        const state = {
            qrCode: null,
            selfieBlob: null,
            latitude: null,
            longitude: null,
            ipAddress: null
        };

        // DOM Elements
        const statusMessage = document.getElementById('status-message');
        
        // Step Containers
        const step1Container = document.getElementById('step1-container');
        const step2Container = document.getElementById('step2-container');
        const step3Container = document.getElementById('step3-container');
        
        // Step Icons
        const step1Icon = document.getElementById('step1-icon');
        const step2Icon = document.getElementById('step2-icon');
        const step3Icon = document.getElementById('step3-icon');
        
        // Selfie Elements
        const startSelfieBtnEl = document.getElementById('start-selfie-btn');
        const takeSelfieBtnEl = document.getElementById('take-selfie-btn');
        const retakeSelfieBtnEl = document.getElementById('retake-selfie-btn');
        const selfieVideoEl = document.getElementById('selfie-video');
        const selfiePreviewEl = document.getElementById('selfie-preview');
        const selfiePlaceholderEl = document.getElementById('selfie-placeholder');
        
        // QR Scanner Elements
        let qrStream = null;
        let qrScannerActive = false;
        let qrScanInterval = null;
        const qrVideoEl = document.getElementById('qr-video');
        const qrCanvasEl = document.getElementById('qr-canvas');
        const qrPlaceholderEl = document.getElementById('qr-placeholder');
        const qrScanOverlay = document.getElementById('qr-scan-overlay');
        const startQRScannerBtn = document.getElementById('start-qr-scanner-btn');
        const stopQRScannerBtn = document.getElementById('stop-qr-scanner-btn');
        
        // Submit Button
        const submitBtnEl = document.getElementById('submit-attendance-btn');

        // Initialize
        @if($currentUserStudent && $todayAbsence)
            // User already submitted, disable form
            step1Container.style.opacity = '0.5';
            startQRScannerBtn.disabled = true;
            startQRScannerBtn.style.opacity = '0.5';
            startQRScannerBtn.style.cursor = 'not-allowed';
            submitBtnEl.style.display = 'none';
        @endif

        // ==================== QR SCANNING ====================
        // Verify button element exists
        if (!startQRScannerBtn) {
            console.error('❌ QR Scanner button not found!');
        } else {
            console.log('✓ QR Scanner button found, attaching listener');
            
            startQRScannerBtn.addEventListener('click', async function() {
                try {
                    showStatus('Requesting camera access...', 'info');
                    
                    qrStream = await navigator.mediaDevices.getUserMedia({
                        video: { 
                            facingMode: 'environment',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });
                    
                    qrVideoEl.srcObject = qrStream;
                    
                    // Ensure video starts playing
                    qrVideoEl.onloadedmetadata = () => {
                        qrVideoEl.play().then(() => {
                            console.log('Video playing');
                            startScanning();
                        }).catch(err => {
                            console.error('Play error:', err);
                            showStatus('Video playback error', 'error');
                        });
                    };
                    
                    // Timeout for video loading
                    setTimeout(() => {
                        if (!qrScannerActive) {
                            showStatus('Camera not responding, please try again', 'warning');
                        }
                    }, 3000);
                    
                    startQRScannerBtn.style.display = 'none';
                    stopQRScannerBtn.style.display = 'block';
                    qrPlaceholderEl.style.display = 'none';
                    qrVideoEl.style.display = 'block';
                    qrScanOverlay.style.display = 'block';
                    
                } catch (error) {
                    showStatus('Camera access denied. Please enable permissions.', 'error');
                    console.error('Camera error:', error);
                    startQRScannerBtn.style.display = 'block';
                    stopQRScannerBtn.style.display = 'none';
                }
            });
        }

        function startScanning() {
            qrScannerActive = true;
            showStatus('🎯 Scanning... Point at QR code', 'info');
            console.log('QR Scanning started');
            
            // Scan every 200ms for better detection
            qrScanInterval = setInterval(() => {
                if (!qrScannerActive) {
                    clearInterval(qrScanInterval);
                    return;
                }
                
                try {
                    const canvas = qrCanvasEl;
                    const video = qrVideoEl;
                    
                    if (video.videoWidth > 0 && video.videoHeight > 0) {
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        
                        const ctx = canvas.getContext('2d', { willReadFrequently: true });
                        ctx.drawImage(video, 0, 0);
                        
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        
                        if (typeof jsQR !== 'undefined' && imageData.data.length > 0) {
                            const code = jsQR(imageData.data, imageData.width, imageData.height);
                            
                            if (code && code.data) {
                                console.log('✓ QR Code detected:', code.data);
                                qrScannerActive = false;
                                clearInterval(qrScanInterval);
                                validateAndSetQRCode(code.data);
                                return;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Scan error:', error);
                }
            }, 200);
            });
        }

        stopQRScannerBtn.addEventListener('click', function() {
            qrScannerActive = false;
            clearInterval(qrScanInterval);
            
            if (qrStream) {
                qrStream.getTracks().forEach(track => track.stop());
            }
            
            qrVideoEl.style.display = 'none';
            qrScanOverlay.style.display = 'none';
            qrPlaceholderEl.style.display = 'flex';
            startQRScannerBtn.style.display = 'block';
            stopQRScannerBtn.style.display = 'none';
            
            showStatus('Scanner stopped', 'info');
        });

        function validateAndSetQRCode(code) {
            qrScannerActive = false;
            if (qrStream) {
                qrStream.getTracks().forEach(track => track.stop());
            }
            
            // Add detection animation
            qrScanOverlay.style.boxShadow = 'inset 0 0 30px rgba(16, 185, 129, 0.8)';
            qrScanOverlay.style.borderColor = '#10b981';
            
            showStatus('✅ QR Code Detected! Processing...', 'success');
            
            state.qrCode = code;
            console.log('QR Code stored:', state.qrCode);
            
            // Update step 1 icon with animation
            step1Icon.textContent = '✓';
            step1Icon.style.background = '#10b981';
            step1Icon.style.color = 'white';
            step1Icon.style.transform = 'scale(1.2)';
            step1Icon.style.transition = 'transform 0.3s ease';
            
            // Hide step 1, show step 2
            setTimeout(() => {
                step1Container.style.opacity = '0.5';
                step1Container.style.pointerEvents = 'none';
                step2Container.style.display = 'block';
                step2Icon.style.background = '#f97316';
                step2Icon.style.color = 'white';
                
                showStatus('📸 Now take a clear selfie to continue...', 'success');
                startSelfieBtnEl.focus();
            }, 500);
            }, 1000);
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
                
                showStatus('Camera ready. Tap "Take Photo" when ready.', 'info');
            } catch (error) {
                showStatus('Camera access denied. Please enable camera permissions.', 'error');
            }
        });

        takeSelfieBtnEl.addEventListener('click', function() {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = selfieVideoEl.videoWidth;
            canvas.height = selfieVideoEl.videoHeight;
            context.drawImage(selfieVideoEl, 0, 0);
            
            // Convert canvas to blob
            canvas.toBlob(blob => {
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
                    
                    // Update step 2 icon
                    step2Icon.textContent = '✓';
                    step2Icon.style.background = '#10b981';
                    
                    // Move to step 3
                    setTimeout(() => {
                        step2Container.style.display = 'none';
                        step3Container.style.display = 'block';
                        step3Icon.style.background = '#f97316';
                        step3Icon.style.color = 'white';
                        
                        showStatus('Selfie captured! Getting your location and IP address...', 'info');
                        
                        // Capture location and IP
                        captureLocationAndIP();
                    }, 500);
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

        // ==================== LOCATION & IP DETECTION ====================
        function captureLocationAndIP() {
            // Get IP Address
            getIPAddress();
            
            // Get Geolocation
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        state.latitude = position.coords.latitude;
                        state.longitude = position.coords.longitude;
                        
                        // Update location display
                        document.getElementById('location-lat').textContent = state.latitude.toFixed(6);
                        document.getElementById('location-lng').textContent = state.longitude.toFixed(6);
                        document.getElementById('location-time').textContent = new Date().toLocaleTimeString();
                        
                        // Update step 3 icon
                        step3Icon.textContent = '✓';
                        step3Icon.style.background = '#10b981';
                        step3Icon.style.color = 'white';
                        
                        // Enable submit button
                        showStatus('Location and IP detected! Ready to submit.', 'success');
                        enableSubmitButton();
                    },
                    error => {
                        // Try without location
                        showStatus('Location denied, but you can still submit.', 'warning');
                        state.latitude = null;
                        state.longitude = null;
                        document.getElementById('location-lat').textContent = 'Permission denied';
                        document.getElementById('location-lng').textContent = 'Permission denied';
                        
                        step3Icon.textContent = '!';
                        step3Icon.style.background = '#f59e0b';
                        enableSubmitButton();
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                showStatus('Geolocation not supported.', 'error');
            }
        }

        function getIPAddress() {
            fetch('https://api.ipify.org?format=json')
                .then(response => response.json())
                .then(data => {
                    state.ipAddress = data.ip;
                    document.getElementById('location-ip').textContent = data.ip;
                })
                .catch(error => {
                    console.log('Could not fetch IP:', error);
                    document.getElementById('location-ip').textContent = 'Unable to detect';
                });
        }

        function enableSubmitButton() {
            submitBtnEl.style.display = 'block';
        }

        // ==================== SUBMIT ====================
        submitBtnEl.addEventListener('click', function() {
            @if($currentUserStudent && $todayAbsence)
                showStatus('You have already submitted your attendance today.', 'warning');
                return;
            @endif
            
            if (!state.qrCode) {
                showStatus('Error: QR code missing.', 'error');
                return;
            }
            
            if (!state.selfieBlob) {
                showStatus('Error: Selfie missing.', 'error');
                return;
            }
            
            submitAttendance();
        });

        function submitAttendance() {
            showStatus('Processing your attendance...', 'info');
            submitBtnEl.disabled = true;
            
            const formData = new FormData();
            formData.append('code', state.qrCode);
            formData.append('selfie', state.selfieBlob, 'selfie.jpg');
            formData.append('latitude', state.latitude || '');
            formData.append('longitude', state.longitude || '');
            formData.append('ip_address', state.ipAddress || '');
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("qrcode.scan") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Attendance submitted successfully!', 'success');
                    setTimeout(() => location.reload(), 2500);
                } else {
                    showStatus('Error: ' + data.message, 'error');
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
            const bgColor = type === 'success' ? '#dcfce7' : type === 'error' ? '#fee2e2' : type === 'warning' ? '#fef3c7' : '#fed7aa';
            const textColor = type === 'success' ? '#166534' : type === 'error' ? '#991b1b' : type === 'warning' ? '#92400e' : '#92400e';

            statusMessage.innerHTML = message;
            statusMessage.style.background = bgColor;
            statusMessage.style.color = textColor;
            statusMessage.style.display = 'block';
            
            // Auto-hide info messages after 5 seconds
            if (type === 'info') {
                setTimeout(() => {
                    if (statusMessage.style.display === 'block') {
                        statusMessage.style.display = 'none';
                    }
                }, 5000);
            }
        }
    </script>
@endsection


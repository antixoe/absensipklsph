@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-clipboard-x" style="margin-right: 8px;"></i>Mark Student Absences</h1>
        <p>Record your attendance with photo and location</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Message -->
    @if(session('error'))
        <div style="padding: 15px 20px; background: #fee2e2; border: 2px solid #ef4444; border-radius: 8px; margin-bottom: 20px; color: #991b1b;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div style="padding: 15px 20px; background: #fee2e2; border: 2px solid #ef4444; border-radius: 8px; margin-bottom: 20px; color: #991b1b;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Already Submitted Alert -->
    @if($currentUserStudent && $todayAbsence)
        <div style="padding: 15px 20px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; margin-bottom: 20px; color: #92400e;">
            <i class="bi bi-info-circle-fill" style="margin-right: 8px;"></i>
            <strong>You have already submitted your absence for today.</strong> The record was submitted at {{ $todayAbsence->created_at->format('H:i') }} on {{ $todayAbsence->absence_date->format('d M Y') }}.
            <span style="display: block; font-size: 12px; margin-top: 8px;">Status: 
                @if($todayAbsence->status === 'pending')
                    <strong style="color: #b45309;">Pending Approval</strong>
                @elseif($todayAbsence->status === 'approved')
                    <strong style="color: #166534;">Approved</strong>
                @else
                    <strong style="color: #991b1b;">{{ ucfirst($todayAbsence->status) }}</strong>
                @endif
            </span>
        </div>
    @endif

    <div class="card">
        <form id="absenceForm" method="POST" action="{{ route('absence.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Auto-fill student ID -->
            @if($currentUserStudent)
                <input type="hidden" name="student_ids[]" value="{{ $currentUserStudent->id }}">
            @else
                <!-- If not linked as student, use a placeholder for now -->
                <input type="hidden" name="student_ids[]" value="0">
            @endif

            <!-- Student Information -->
            <div style="margin-bottom: 30px;">
                <div class="card-title">
                    <i class="bi bi-person-check-fill" style="margin-right: 8px;"></i>
                    Mark Your Attendance
                </div>
                <div style="padding: 15px; background: #dcfce7; border: 2px solid #10b981; border-radius: 6px;">
                    <p style="margin: 0; color: #166534; font-size: 14px;">
                        <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
                        <strong>{{ Auth::user()->name }}</strong> - Your absence will be recorded.
                    </p>
                </div>
            </div>

            <!-- Date & Time Section -->
            <div style="margin-bottom: 30px;">
                <div class="card-title"><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>Absence Date & Time</div>
                <div style="padding: 15px; background: #fed7aa; border: 2px solid #ea580c; border-radius: 6px;">
                    <p style="margin: 0; color: #92400e; font-size: 14px;">
                        <i class="bi bi-clock-fill" style="margin-right: 8px;"></i>
                        <strong>Current Date & Time:</strong> <span id="currentDateTime"></span>
                    </p>
                </div>
                
                <!-- Hidden fields for date and time - automatically set by JavaScript -->
                <input type="hidden" id="absence_date_field" name="absence_date">
                <input type="hidden" id="absence_time_field" name="absence_time">
                
                @error('absence_date')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
                @error('absence_time')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tab Navigation -->
            <div style="margin-bottom: 30px;">
                <div style="display: flex; gap: 0; border-bottom: 2px solid #ddd;">
                    <button type="button" class="tab-btn active" onclick="switchTab(this, 'selfie')" 
                            style="flex: 1; padding: 15px; background: #fff; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid #f97316; transition: all 0.3s;">
                        <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Capture Selfie
                    </button>
                    <button type="button" class="tab-btn" onclick="switchTab(this, 'qr')" 
                            style="flex: 1; padding: 15px; background: #f5f5f5; border: none; cursor: pointer; font-weight: 600; border-bottom: 3px solid transparent; transition: all 0.3s;">
                        <i class="bi bi-qr-code" style="margin-right: 8px;"></i>Scan QR Code
                    </button>
                </div>
            </div>

            <!-- Selfie Capture Section -->
            <div id="selfie-tab" style="margin-bottom: 30px; display: block;">
                <div class="card-title">Capture Selfie</div>
                <div id="webcamStatus" style="padding: 10px 15px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 6px; margin-bottom: 15px; color: #92400e; font-size: 13px;">
                    <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
                    <span id="statusText">Initializing camera...</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="border: 2px solid #ddd; border-radius: 6px; overflow: hidden; background: #000;">
                            <video id="webcam" style="width: 100%; max-height: 400px; display: block;" autoplay playsinline muted></video>
                        </div>
                        <button type="button" onclick="capturePhoto()" class="btn" 
                                style="width: 100%; margin-top: 10px;">
                            <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Capture Photo
                        </button>
                    </div>
                    <div>
                        <div style="border: 2px solid #ddd; border-radius: 6px; overflow: hidden; background: #f5f5f5; min-height: 400px; display: flex; align-items: center; justify-content: center;">
                            <canvas id="canvas" style="width: 100%; height: 100%; display: none;"></canvas>
                            <img id="capturedPhoto" style="width: 100%; height: 100%; object-fit: cover; display: none;" 
                                 src="" alt="Captured Photo">
                            <div id="photoPlaceholder" style="color: #999; text-align: center;">
                                <i class="bi bi-image" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                                <p>Captured photo will appear here</p>
                            </div>
                        </div>
                        <input type="hidden" id="selfieInput" name="selfie">
                        <input type="file" id="selfieFile" name="selfie" accept="image/*" style="display: none;">
                    </div>
                </div>
                @error('selfie')
                    <div style="color: #ef4444; margin-top: 10px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- QR Code Scanning Section -->
            <div id="qr-tab" style="margin-bottom: 30px; display: none;">
                <div class="card-title">Scan QR Code</div>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    Scan a QR code using your camera or upload an image from your gallery.
                </p>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <button type="button" onclick="openQRScanner()" class="btn" 
                            style="padding: 12px 30px; background: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="bi bi-qr-code" style="margin-right: 8px;"></i>Open QR Scanner
                    </button>
                    
                    <div style="position: relative;">
                        <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
                        <button type="button" onclick="document.getElementById('qrFileInput').click()" class="btn" 
                                style="padding: 12px 30px; background: #0ea5e9; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; width: 100%;">
                            <i class="bi bi-image" style="margin-right: 8px;"></i>Upload Image from Gallery
                        </button>
                    </div>
                    
                    <div id="qrScanResult" style="padding: 15px; background: #fed7aa; border: 2px solid #ea580c; border-radius: 6px; display: none;">
                        <p style="margin: 0; color: #92400e; font-size: 14px;">
                            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
                            <strong>QR Code Scanned:</strong> <span id="qrCodeValue"></span>
                        </p>
                    </div>
                    <input type="hidden" id="qrCodeInput" name="qr_code">
                </div>
            </div>

            <!-- Location & IP Section -->
            <div style="margin-bottom: 30px;">
                <div class="card-title"><i class="bi bi-geo-alt-fill" style="margin-right: 8px;"></i>Current Location</div>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    Click the button below to automatically detect and capture your current location.
                </p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">IP Address</label>
                        <input type="text" id="ipAddress" name="ip_address" readonly 
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; background: #f5f5f5; font-size: 13px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Location Name</label>
                        <input type="text" id="locationName" name="location_name" 
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px;"
                               placeholder="Will be auto-detected...">
                    </div>
                </div>
                <button type="button" onclick="getLocationAndIP()" class="btn" style="margin-top: 15px; width: 100%;">
                    <i class="bi bi-geo-alt" style="margin-right: 8px;"></i>Detect My Current Location
                </button>
            </div>

            <!-- Notes Section -->
            <div style="margin-bottom: 30px;">
                <div class="card-title">Additional Notes</div>
                <textarea name="notes" id="notes" placeholder="Add any additional notes..." 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; 
                                 font-family: inherit; min-height: 100px; resize: vertical;"></textarea>
            </div>

            <!-- Submit Button -->
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="padding: 12px 30px;" 
                        @if($currentUserStudent && $todayAbsence) disabled style="opacity: 0.5; cursor: not-allowed;" @endif>
                    <i class="bi bi-check-lg" style="margin-right: 8px;"></i>
                    @if($currentUserStudent && $todayAbsence)
                        Already Submitted
                    @else
                        Submit Absence(s)
                    @endif
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 12px 30px;">Cancel</a>
            </div>
        </form>
    </div>

    <!-- Recent Absence Records -->
    @if($currentUserStudent && isset($absences))
        @php
            $myRecentAbsences = $absences
                ->where('student_id', $currentUserStudent->id)
                ->sortByDesc('absence_date')
                ->take(5);
        @endphp
        
        @if($myRecentAbsences->count() > 0)
            <div class="card" style="margin-top: 20px;">
                <h3><i class="bi bi-clock-history" style="margin-right: 8px;"></i>Your Recent Absence Records</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Date & Time</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRecentAbsences as $record)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">{{ $record->absence_date->format('d M Y H:i') }}</td>
                                <td style="padding: 12px;">{{ $record->location_name ?? '-' }}</td>
                                <td style="padding: 12px;">
                                    @if($record->status === 'pending')
                                        <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pending</span>
                                    @elseif($record->status === 'approved')
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">Approved</span>
                                    @else
                                        <span style="background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($record->status) }}</span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($record->selfie_path)
                                        <a href="{{ asset('storage/' . $record->selfie_path) }}" target="_blank" style="color: #f97316;">
                                            <i class="bi bi-image"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <script>
        let stream = null;
        let capturedPhotoBlob = null;

        // Initialize webcam
        async function initWebcam() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user' },
                    audio: false
                });
                const video = document.getElementById('webcam');
                video.srcObject = stream;
                
                // Update status
                const statusText = document.getElementById('statusText');
                statusText.innerText = 'Camera is ready. Click "Capture Photo" to take a selfie.';
                document.getElementById('webcamStatus').style.background = '#dcfce7';
                document.getElementById('webcamStatus').style.borderColor = '#10b981';
                document.getElementById('webcamStatus').style.color = '#166534';
            } catch (error) {
                console.error('Camera error:', error);
                const statusText = document.getElementById('statusText');
                let errorMsg = 'Unable to access camera';
                
                if (error.name === 'NotAllowedError') {
                    errorMsg = 'Camera permission denied. Please allow camera access.';
                } else if (error.name === 'NotFoundError') {
                    errorMsg = 'No camera found on this device.';
                } else if (error.name === 'NotReadableError') {
                    errorMsg = 'Camera is being used by another application.';
                }
                
                statusText.innerText = errorMsg;
                document.getElementById('webcamStatus').style.background = '#fee2e2';
                document.getElementById('webcamStatus').style.borderColor = '#ef4444';
                document.getElementById('webcamStatus').style.color = '#991b1b';
            }
        }

        // Capture photo from webcam
        function capturePhoto() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            
            if (!video.srcObject) {
                showToast('Camera not ready. Please wait.', 'error');
                return;
            }

            const ctx = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            
            if (canvas.width === 0 || canvas.height === 0) {
                showToast('Video not ready yet. Please wait.', 'error');
                return;
            }
            
            ctx.drawImage(video, 0, 0);
            
            canvas.toBlob(function(blob) {
                capturedPhotoBlob = blob;
                
                const img = document.getElementById('capturedPhoto');
                img.src = URL.createObjectURL(blob);
                img.style.display = 'block';
                document.getElementById('photoPlaceholder').style.display = 'none';
                
                const file = new File([blob], 'selfie_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                try {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('selfieFile').files = dataTransfer.files;
                } catch (e) {
                    console.log('File assignment fallback');
                }
                
                showToast('Selfie captured!', 'success');
            }, 'image/jpeg', 0.9);
        }

        // Get current location and IP
        async function getLocationAndIP() {
            try {
                // Get IP Address
                const ipResponse = await fetch('https://api.ipify.org?format=json');
                const ipData = await ipResponse.json();
                document.getElementById('ipAddress').value = ipData.ip;

                // Get Geolocation
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        async position => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            // Get location name from coordinates using reverse geocoding
                            try {
                                const geoResponse = await fetch(
                                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                                );
                                const geoData = await geoResponse.json();
                                document.getElementById('locationName').value = 
                                    geoData.address?.city || geoData.address?.town || 
                                    geoData.address?.county || geoData.display_name || 
                                    'Current Location';
                            } catch {
                                document.getElementById('locationName').value = 'Current Location';
                            }

                            showToast('Location and IP captured successfully!', 'success');
                        },
                        error => {
                            let errorMsg = '';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMsg = 'Location access denied. Please enable location permission in your browser settings.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMsg = 'Location information is unavailable. Please try again.';
                                    break;
                                case error.TIMEOUT:
                                    errorMsg = 'Location request timed out. Please try again.';
                                    break;
                                default:
                                    errorMsg = 'Unable to detect location.';
                            }
                            showToast(errorMsg, 'warning');
                        }
                    );
                } else {
                    showToast('Geolocation is not supported by your browser.', 'warning');
                }
            } catch (error) {
                showToast('Error getting IP: ' + error.message, 'error');
            }
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#f97316';
            const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill';
            
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
                z-index: 1000;
                max-width: 400px;
                animation: slideIn 0.3s ease-out, slideOut 0.3s ease-out 3.7s forwards;
            `;
            toast.innerHTML = `<i class="bi ${icon}" style="margin-right: 8px;"></i>${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 4000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set current date and time automatically
            function updateDateTime() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                
                // Set the date in YYYY-MM-DD format
                document.getElementById('absence_date_field').value = `${year}-${month}-${day}`;
                
                // Set the time in HH:ii format
                document.getElementById('absence_time_field').value = `${hours}:${minutes}`;
                
                // Display formatted datetime
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const dayName = days[now.getDay()];
                const monthName = months[now.getMonth()];
                
                document.getElementById('currentDateTime').textContent = `${dayName}, ${monthName} ${day}, ${year} at ${hours}:${minutes}:${seconds}`;
            }
            
            // Update immediately and then every second
            updateDateTime();
            setInterval(updateDateTime, 1000);
            
            initWebcam();
            
            // Handle QR code upload from file
            document.getElementById('qrFileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                // Check if file is an image
                if (!file.type.startsWith('image/')) {
                    showToast('Please select a valid image file', 'error');
                    return;
                }
                
                // Load the image and scan for QR code
                const reader = new FileReader();
                reader.onload = function(event) {
                    const img = new Image();
                    img.onload = function() {
                        scanQRFromImage(img);
                    };
                    img.src = event.target.result;
                };
                reader.readAsDataURL(file);
                
                // Reset the file input so the same file can be selected again
                e.target.value = '';
            });

            // Add form validation with confirmation
            document.getElementById('absenceForm').addEventListener('submit', function(e) {
                const selfieFileInput = document.getElementById('selfieFile');
                const hasSelfieFile = selfieFileInput.files.length > 0;
                const hasCapturedPhoto = capturedPhotoBlob !== null;
                const hasQRCode = document.getElementById('qrCodeInput').value !== '';
                const activeSelfieTab = document.getElementById('selfie-tab').style.display !== 'none';
                const alreadySubmitted = {{ $currentUserStudent && $todayAbsence ? 'true' : 'false' }};

                if (alreadySubmitted) {
                    e.preventDefault();
                    showToast('You have already submitted your absence for today.', 'warning');
                    return;
                }

                // Validate based on active tab
                if (activeSelfieTab) {
                    // Selfie tab is active - require selfie
                    if (!hasSelfieFile && !hasCapturedPhoto) {
                        e.preventDefault();
                        showToast('Please capture a selfie before submitting.', 'error');
                        return;
                    }

                    // If we have a blob but file input isn't set, use FormData
                    if (!hasSelfieFile && hasCapturedPhoto) {
                        const formData = new FormData(this);
                        formData.append('selfie', capturedPhotoBlob, 'selfie_' + Date.now() + '.jpg');
                        
                        // Show confirmation
                        if (!confirm('Are you sure you want to submit your absence record? Make sure all information is correct before confirming.')) {
                            e.preventDefault();
                            return;
                        }

                        e.preventDefault();
                        
                        // Submit via fetch to handle FormData with blob
                        fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(data => {
                            // Reload page to show success
                            window.location.reload();
                        })
                        .catch(error => {
                            showToast('Error submitting form: ' + error.message, 'error');
                            console.error('Form submission error:', error);
                        });
                        return;
                    }
                } else {
                    // QR tab is active - require QR code
                    if (!hasQRCode) {
                        e.preventDefault();
                        showToast('Please scan a QR code before submitting.', 'error');
                        return;
                    }
                }

                // Show confirmation dialog for normal form submission
                if (!confirm('Are you sure you want to submit your absence record? Make sure all information is correct before confirming.')) {
                    e.preventDefault();
                    return;
                }
            });

            // Stop webcam on page unload
            window.addEventListener('beforeunload', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        });

        // Tab switching function
        function switchTab(button, tabName) {
            // Hide all tabs
            document.getElementById('selfie-tab').style.display = 'none';
            document.getElementById('qr-tab').style.display = 'none';
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.style.borderBottomColor = 'transparent';
                btn.style.color = '#666';
                btn.style.background = '#f5f5f5';
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').style.display = 'block';
            
            // Add active class to clicked button
            button.style.borderBottomColor = '#f97316';
            button.style.color = '#f97316';
            button.style.background = '#fff';
        }

        // QR Code Scanner Modal and Functions
        let qrScanner = null;
        let qrScannerActive = false;

        function openQRScanner() {
            const html = `
                <div id="qrScannerModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 2000;">
                    <div style="background: white; border-radius: 12px; padding: 0; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden;">
                        <!-- Modal Header -->
                        <div style="padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                            <h3 style="margin: 0; font-size: 18px; font-weight: 600;">
                                <i class="bi bi-qr-code" style="margin-right: 8px;"></i>Scan QR Code
                            </h3>
                            <button type="button" onclick="closeQRScanner()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">×</button>
                        </div>
                        
                        <!-- Modal Body -->
                        <div style="padding: 20px;">
                            <div id="qrPreview" style="width: 100%; border: 3px solid #f97316; border-radius: 8px; overflow: hidden; background: #000; position: relative; height: 400px;">
                                <!-- Scanning line animation -->
                                <div id="scanningLine" style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, transparent, #f97316, #ff6b35, #f97316, transparent); box-shadow: 0 0 10px #f97316, 0 0 20px rgba(249, 115, 22, 0.5); z-index: 10; animation: scanLine 2s ease-in-out infinite;"></div>
                                
                                <!-- Corner markers -->
                                <div style="position: absolute; top: 0; left: 0; width: 30px; height: 30px; border-top: 3px solid #f97316; border-left: 3px solid #f97316; border-radius: 4px; z-index: 9;"></div>
                                <div style="position: absolute; top: 0; right: 0; width: 30px; height: 30px; border-top: 3px solid #f97316; border-right: 3px solid #f97316; border-radius: 4px; z-index: 9;"></div>
                                <div style="position: absolute; bottom: 0; left: 0; width: 30px; height: 30px; border-bottom: 3px solid #f97316; border-left: 3px solid #f97316; border-radius: 4px; z-index: 9;"></div>
                                <div style="position: absolute; bottom: 0; right: 0; width: 30px; height: 30px; border-bottom: 3px solid #f97316; border-right: 3px solid #f97316; border-radius: 4px; z-index: 9;"></div>
                            </div>
                            <div style="margin-top: 15px; padding: 10px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 6px; color: #92400e; font-size: 13px;">
                                <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
                                <span id="qrStatus">Position QR code in front of the camera...</span>
                            </div>
                        </div>
                        
                        <!-- Modal Footer -->
                        <div style="padding: 15px 20px; background: #f5f5f5; border-top: 1px solid #ddd; display: flex; gap: 10px;">
                            <button type="button" onclick="closeQRScanner()" class="btn btn-secondary" style="flex: 1; padding: 10px;">Cancel</button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', html);
            
            // Load and initialize the QR code scanner
            loadQRScannerLibrary();
        }

        function closeQRScanner() {
            const modal = document.getElementById('qrScannerModal');
            if (modal) {
                modal.remove();
            }
            
            // Clean up all streams
            if (window.currentQRStream) {
                window.currentQRStream.getTracks().forEach(track => {
                    track.stop();
                });
                window.currentQRStream = null;
            }
            
            if (qrScanner) {
                qrScanner.stop();
                qrScanner = null;
                qrScannerActive = false;
            }
            
            qrScannerActive = false;
        }

        function loadQRScannerLibrary() {
            // Check if jsQR is already loaded
            if (typeof jsQR === 'undefined') {
                // Load jsQR library
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
                script.onload = function() {
                    initializeQRScanner();
                };
                document.head.appendChild(script);
            } else {
                initializeQRScanner();
            }
        }

        function initializeQRScanner() {
            const previewElement = document.getElementById('qrPreview');
            const statusElement = document.getElementById('qrStatus');
            
            if (!previewElement) return;
            
            // Create video element
            const video = document.createElement('video');
            video.setAttribute('autoplay', 'true');
            video.setAttribute('playsinline', 'true');
            video.setAttribute('muted', 'true');
            video.style.width = '100%';
            video.style.height = '400px';
            video.style.objectFit = 'cover';
            video.style.display = 'block';
            
            previewElement.innerHTML = '';
            previewElement.appendChild(video);
            
            statusElement.innerText = 'Requesting camera access...';
            statusElement.style.background = '#fef3c7';
            statusElement.style.borderColor = '#f59e0b';
            statusElement.style.color = '#92400e';
            
            // Check if mediaDevices is available
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                statusElement.innerText = '❌ Error: Your browser does not support camera access. Please use Chrome, Firefox, Safari, or Edge.';
                statusElement.style.background = '#fee2e2';
                statusElement.style.borderColor = '#ef4444';
                statusElement.style.color = '#991b1b';
                showToast('Camera not supported. Use upload option instead.', 'error');
                return;
            }
            
            // Try multiple camera constraint variations
            const cameraConstraints = [
                { video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } },
                { video: { facingMode: 'environment' } },
                { video: true }
            ];
            
            let constraintAttempt = 0;
            
            const tryGetUserMedia = () => {
                if (constraintAttempt >= cameraConstraints.length) {
                    statusElement.innerText = '❌ Camera Error: Unable to access camera on this device. Steps to fix:\n1. Check if camera permission is allowed in browser settings\n2. Make sure no other app is using the camera\n3. Try refreshing the page\n4. Use "Upload QR Image" option instead';
                    statusElement.style.background = '#fee2e2';
                    statusElement.style.borderColor = '#ef4444';
                    statusElement.style.color = '#991b1b';
                    statusElement.style.whiteSpace = 'pre-wrap';
                    statusElement.style.fontSize = '12px';
                    showToast('❌ Cannot access camera. Try uploading a QR image instead.', 'error');
                    return;
                }
                
                const constraints = cameraConstraints[constraintAttempt];
                constraintAttempt++;
                
                navigator.mediaDevices.getUserMedia(constraints)
                    .then(stream => {
                        video.srcObject = stream;
                        qrScannerActive = true;
                        statusElement.innerText = '✓ Camera ready. Point at QR code...';
                        statusElement.style.background = '#dcfce7';
                        statusElement.style.borderColor = '#10b981';
                        statusElement.style.color = '#166534';
                        statusElement.style.whiteSpace = 'normal';
                        statusElement.style.fontSize = '13px';
                        
                        // Store stream reference for cleanup
                        window.currentQRStream = stream;
                        
                        // Wait for video to be ready with timeout
                        let loadTimeout = setTimeout(() => {
                            statusElement.innerText = '⏱ Video loading... (takes 2-3 seconds on some devices)';
                            statusElement.style.background = '#fef3c7';
                            statusElement.style.borderColor = '#f59e0b';
                        }, 1000);
                        
                        video.onloadedmetadata = () => {
                            clearTimeout(loadTimeout);
                            console.log('Video metadata loaded. Video dimensions:', video.videoWidth, 'x', video.videoHeight);
                            
                            // Create canvas for QR scanning
                            const canvas = document.createElement('canvas');
                            const canvasContext = canvas.getContext('2d', { willReadFrequently: true });
                            
                            let scanAttempts = 0;
                            const maxAttempts = 600; // Max 20 seconds at 30fps
                            let lastDetectionTime = 0;
                            
                            const scanQRCode = () => {
                                if (!qrScannerActive || scanAttempts >= maxAttempts) {
                                    if (scanAttempts >= maxAttempts) {
                                        statusElement.innerText = 'ℹ Scan timeout (20 seconds). Camera is working but no QR detected. Check QR code quality or positioning.';
                                        statusElement.style.background = '#fef3c7';
                                        statusElement.style.borderColor = '#f59e0b';
                                        statusElement.style.color = '#92400e';
                                    }
                                    return;
                                }
                                
                                scanAttempts++;
                                
                                try {
                                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                        canvas.width = video.videoWidth;
                                        canvas.height = video.videoHeight;
                                        
                                        if (canvas.width === 0 || canvas.height === 0) {
                                            // Video not ready yet
                                            requestAnimationFrame(scanQRCode);
                                            return;
                                        }
                                        
                                        canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
                                        const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
                                        
                                        // Try scanning with both normal and inverted attempts
                                        const code = jsQR(imageData.data, imageData.width, imageData.height, {
                                            inversionAttempts: 'attemptBoth'
                                        });
                                        
                                        if (code) {
                                            // Only process if not detected recently (debounce)
                                            if (Date.now() - lastDetectionTime > 500) {
                                                lastDetectionTime = Date.now();
                                                
                                                // QR code found!
                                                statusElement.innerText = '✓ QR Code detected! Processing...';
                                                statusElement.style.background = '#dcfce7';
                                                statusElement.style.borderColor = '#10b981';
                                                statusElement.style.color = '#166534';
                                                qrScannerActive = false;
                                                
                                                console.log('QR Code detected:', code.data);
                                                
                                                // Add success animation to preview
                                                const qrPreview = document.getElementById('qrPreview');
                                                if (qrPreview) {
                                                    qrPreview.style.boxShadow = '0 0 30px #10b981, inset 0 0 30px rgba(16, 185, 129, 0.3)';
                                                    qrPreview.style.borderColor = '#10b981';
                                                }
                                                
                                                // Stop the stream
                                                if (window.currentQRStream) {
                                                    window.currentQRStream.getTracks().forEach(track => track.stop());
                                                    window.currentQRStream = null;
                                                }
                                                
                                                // Set the QR code value
                                                const qrValue = code.data;
                                                document.getElementById('qrCodeInput').value = qrValue;
                                                document.getElementById('qrCodeValue').innerText = qrValue.substring(0, 50) + (qrValue.length > 50 ? '...' : '');
                                                document.getElementById('qrScanResult').style.display = 'block';
                                                
                                                // Close modal after 1.5 seconds and auto-submit
                                                setTimeout(() => {
                                                    closeQRScanner();
                                                    showToast('✓ QR Code scanned! Recording absence...', 'success');
                                                    
                                                    // Automatically submit after QR scan
                                                    setTimeout(() => {
                                                        submitQRAbsence(qrValue);
                                                    }, 500);
                                                }, 1500);
                                                return;
                                            }
                                        }
                                    }
                                } catch (err) {
                                    console.error('QR scan error:', err);
                                }
                                
                                requestAnimationFrame(scanQRCode);
                            };
                            
                            scanQRCode();
                        };
                        
                        // Fallback for videos that don't trigger onloadedmetadata
                        setTimeout(() => {
                            if (video.srcObject && video.videoWidth === 0) {
                                console.warn('Video not loading, trying fallback');
                                video.load();
                            }
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('Camera constraint failed:', constraints, 'Error:', err.message);
                        
                        // Provide specific error messages
                        let errorMsg = '❌ Camera Error:';
                        if (err.name === 'NotAllowedError') {
                            errorMsg += ' Camera permission denied. Please:\n1. Click the camera icon in your address bar\n2. Select "Allow" for camera access\n3. Refresh and try again';
                        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                            errorMsg += ' No camera found. Please connect a camera and try again.';
                        } else if (err.name === 'NotReadableError') {
                            errorMsg += ' Camera is in use by another app. Close other apps using the camera and try again.';
                        } else if (err.name === 'OverconstrainedError') {
                            errorMsg += ' Camera does not support requested resolution. Trying different settings...';
                            tryGetUserMedia();
                            return;
                        } else {
                            errorMsg += ' ' + err.message;
                        }
                        
                        // If this wasn't the last attempt and it's a constraint error, try next
                        if (constraintAttempt < cameraConstraints.length && err.name === 'OverconstrainedError') {
                            tryGetUserMedia();
                        } else {
                            statusElement.innerText = errorMsg;
                            statusElement.style.background = '#fee2e2';
                            statusElement.style.borderColor = '#ef4444';
                            statusElement.style.color = '#991b1b';
                            statusElement.style.whiteSpace = 'pre-wrap';
                            statusElement.style.fontSize = '12px';
                            showToast('Camera access failed. Try uploading a QR image instead.', 'error');
                        }
                    });
            };
            
            tryGetUserMedia();
        }

        function scanQRFromImage(img) {
            // Ensure jsQR library is loaded
            if (typeof jsQR === 'undefined') {
                showToast('Loading QR parser...', 'info');
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js';
                script.onload = function() {
                    scanQRFromImage(img);
                };
                document.head.appendChild(script);
                return;
            }

            try {
                // Create a canvas and draw the image
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const context = canvas.getContext('2d');
                context.drawImage(img, 0, 0);

                // Get image data
                const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

                // Scan for QR code with both normal and inverted attempts
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'attemptBoth'
                });

                if (code) {
                    // QR code found
                    const qrValue = code.data;
                    document.getElementById('qrCodeInput').value = qrValue;
                    document.getElementById('qrCodeValue').innerText = qrValue.substring(0, 50) + (qrValue.length > 50 ? '...' : '');
                    document.getElementById('qrScanResult').style.display = 'block';
                    
                    showToast('✓ QR Code detected!', 'success');
                    
                    // Auto-submit after successful scan
                    setTimeout(() => {
                        submitQRAbsence(qrValue);
                    }, 500);
                } else {
                    showToast('❌ No QR code found in the image. Please try another image.', 'error');
                }
            } catch (err) {
                console.error('Image scan error:', err);
                showToast('Error processing image: ' + err.message, 'error');
            }
        }

        async function submitQRAbsence(qrCode) {
            try {
                // Auto-capture IP address
                let ipAddress = '';
                try {
                    const ipResponse = await fetch('https://api.ipify.org?format=json');
                    const ipData = await ipResponse.json();
                    ipAddress = ipData.ip;
                } catch (e) {
                    console.log('IP fetch error:', e);
                }
                
                // Auto-capture location
                let locationName = 'Current Location';
                
                if (navigator.geolocation) {
                    await new Promise((resolve) => {
                        navigator.geolocation.getCurrentPosition(
                            async position => {
                                const lat = position.coords.latitude;
                                const lng = position.coords.longitude;

                                // Get location name from coordinates using reverse geocoding
                                try {
                                    const geoResponse = await fetch(
                                        `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                                    );
                                    const geoData = await geoResponse.json();
                                    locationName = geoData.address?.city || geoData.address?.town || 
                                                   geoData.address?.county || geoData.display_name || 
                                                   'Current Location';
                                } catch (e) {
                                    console.log('Geocoding error:', e);
                                }
                                
                                resolve();
                            },
                            error => {
                                console.log('Location error:', error);
                                resolve();
                            }
                        );
                    });
                }
                
                // Prepare data for QR absence submission
                const formData = new FormData();
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                formData.append('student_ids[]', document.querySelector('input[name="student_ids[]"]').value);
                formData.append('qr_code', qrCode);
                formData.append('ip_address', ipAddress);
                formData.append('location_name', locationName);
                formData.append('notes', ''); // Empty notes for QR scans
                formData.append('method', 'qr'); // Mark as QR submission
                
                // Get current absence date and time
                formData.append('absence_date', document.getElementById('absence_date_field').value);
                formData.append('absence_time', document.getElementById('absence_time_field').value);
                
                // Submit the form
                const response = await fetch('{{ route('absence.store') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Error submitting QR absence');
                }
                
                showToast('✓ Absence recorded successfully!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } catch (error) {
                showToast('Error submitting absence: ' + error.message, 'error');
                console.error('Submission error:', error);
            }
        }
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        
        @keyframes scanLine {
            0% {
                top: 0%;
            }
            50% {
                top: 50%;
            }
            100% {
                top: 100%;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(249, 115, 22, 0);
            }
        }
    </style>
@endsection

@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-clipboard-x" style="margin-right: 8px;"></i>Mark Student Absences</h1>
        <p>Record your attendance with photo and location</p>
        <div style="margin-top: 12px;">
            <a href="{{ route('absence.pending') }}" class="btn" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-clipboard-check"></i>View Pending Approvals
            </a>
        </div>
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

            <!-- Selfie Capture Section -->
            <div style="margin-bottom: 30px;">
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
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Date</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myRecentAbsences as $record)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">{{ $record->absence_date->format('d M Y') }}</td>
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
                                        <a href="{{ asset('storage/' . $record->selfie_path) }}" target="_blank" style="color: #0084ff;">
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
            const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#3b82f6';
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
            initWebcam();

            // Add form validation with confirmation
            document.getElementById('absenceForm').addEventListener('submit', function(e) {
                const selfieFileInput = document.getElementById('selfieFile');
                const hasSelfieFile = selfieFileInput.files.length > 0;
                const hasCapturedPhoto = capturedPhotoBlob !== null;
                const alreadySubmitted = {{ $currentUserStudent && $todayAbsence ? 'true' : 'false' }};

                if (alreadySubmitted) {
                    e.preventDefault();
                    showToast('You have already submitted your absence for today.', 'warning');
                    return;
                }

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
    </style>
@endsection

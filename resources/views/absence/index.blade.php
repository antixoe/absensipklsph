@extends('layouts.app')

@section('title', 'Mark Absences')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-clipboard-x" style="margin-right: 8px;"></i>Mark Student Absences</h1>
        <p>Select students, capture selfie, and record their absence with location</p>
    </div>

    <div class="card">
        <form id="absenceForm" method="POST" action="{{ route('absence.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Students Selection -->
            <div style="margin-bottom: 30px;">
                <div class="card-title">Select Students as Absent</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                    @forelse($students as $student)
                        <div style="padding: 15px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer;" 
                             onclick="toggleStudent(this, {{ $student->id }})">
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" 
                                   id="student_{{ $student->id }}" style="margin-right: 10px;">
                            <label for="student_{{ $student->id }}" style="cursor: pointer; margin: 0;">
                                <strong>{{ $student->user->name }}</strong><br>
                                <small style="color: #666;">NIM: {{ $student->nim }}</small>
                            </label>
                        </div>
                    @empty
                        <p style="color: #666;">No students found.</p>
                    @endforelse
                </div>
                @error('student_ids')
                    <div style="color: #ef4444; margin-top: 10px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Selfie Capture Section -->
            <div style="margin-bottom: 30px;">
                <div class="card-title">Capture Selfie</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <div style="border: 2px solid #ddd; border-radius: 6px; overflow: hidden; background: #000;">
                            <video id="webcam" style="width: 100%; max-height: 400px; display: block;"></video>
                        </div>
                        <button type="button" onclick="capturePhoto()" class="btn" 
                                style="width: 100%; margin-top: 10px;">
                            <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>Capture Photo
                        </button>
                    </div>
                    <div>
                        <div style="border: 2px solid #ddd; border-radius: 6px; overflow: hidden; background: #f5f5f5; min-height: 400px;">
                            <canvas id="canvas" style="width: 100%; height: 100%; display: none;"></canvas>
                            <img id="capturedPhoto" style="width: 100%; height: 100%; object-fit: cover;" 
                                 src="" alt="Captured Photo">
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
                <div class="card-title">Location & IP Information</div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">IP Address</label>
                        <input type="text" id="ipAddress" name="ip_address" readonly 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f5f5f5;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Latitude</label>
                        <input type="number" id="latitude" name="latitude" step="0.000001" readonly 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f5f5f5;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Longitude</label>
                        <input type="number" id="longitude" name="longitude" step="0.000001" readonly 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f5f5f5;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Location Name</label>
                        <input type="text" id="locationName" name="location_name" readonly 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; background: #f5f5f5;">
                    </div>
                </div>
                <button type="button" onclick="getLocationAndIP()" class="btn" style="margin-top: 15px;">
                    <i class="bi bi-geo-alt-fill" style="margin-right: 8px;"></i>Get Current Location & IP
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
                <button type="submit" class="btn" style="padding: 12px 30px;">
                    <i class="bi bi-check-lg" style="margin-right: 8px;"></i>Submit Absence(s)
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 12px 30px;">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let stream = null;
        let capturedPhotoBlob = null;

        // Initialize webcam
        async function initWebcam() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user' } 
                });
                document.getElementById('webcam').srcObject = stream;
            } catch (error) {
                alert('Unable to access web camera: ' + error.message);
            }
        }

        // Capture photo from webcam
        function capturePhoto() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0);

            // Convert to blob and display
            canvas.toBlob(blob => {
                capturedPhotoBlob = blob;
                const img = document.getElementById('capturedPhoto');
                img.src = URL.createObjectURL(blob);
                
                // Create a File object for form submission
                const file = new File([blob], 'selfie_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('selfieFile').files = dataTransfer.files;
            }, 'image/jpeg');

            alert('Photo captured successfully!');
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

                            document.getElementById('latitude').value = lat;
                            document.getElementById('longitude').value = lng;

                            // Get location name from coordinates using reverse geocoding
                            try {
                                const geoResponse = await fetch(
                                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`
                                );
                                const geoData = await geoResponse.json();
                                document.getElementById('locationName').value = 
                                    geoData.address?.city || geoData.address?.town || 
                                    geoData.address?.county || geoData.display_name || 
                                    `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            } catch {
                                document.getElementById('locationName').value = 
                                    `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            }

                            alert('Location and IP captured successfully!');
                        },
                        error => {
                            alert('Unable to get location: ' + error.message);
                        }
                    );
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            } catch (error) {
                alert('Error getting IP: ' + error.message);
            }
        }

        // Toggle student checkbox with visual feedback
        function toggleStudent(element, studentId) {
            const checkbox = element.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            element.style.background = checkbox.checked ? '#f0f9ff' : 'white';
            element.style.borderColor = checkbox.checked ? '#f97316' : '#ddd';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initWebcam();

            // Set checked status on load
            document.querySelectorAll('input[type="checkbox"][name="student_ids[]"]').forEach(checkbox => {
                if (checkbox.checked) {
                    checkbox.closest('div').style.background = '#f0f9ff';
                    checkbox.closest('div').style.borderColor = '#f97316';
                }
            });

            // Add form validation
            document.getElementById('absenceForm').addEventListener('submit', function(e) {
                const studentIds = document.querySelectorAll('input[name="student_ids[]"]:checked');
                const ipAddress = document.getElementById('ipAddress').value;
                const latitude = document.getElementById('latitude').value;
                const longitude = document.getElementById('longitude').value;

                if (studentIds.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one student as absent.');
                    return;
                }

                if (!ipAddress || !latitude || !longitude) {
                    e.preventDefault();
                    alert('Please capture location and IP address before submitting.');
                    return;
                }

                if (!document.getElementById('selfieFile').files.length) {
                    e.preventDefault();
                    alert('Please capture a selfie before submitting.');
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
@endsection

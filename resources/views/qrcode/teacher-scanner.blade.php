@extends('layouts.app')

@section('styles')
<style>
    .teacher-scanner-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .teacher-scanner-hero {
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

    .teacher-scanner-hero::after {
        content: "";
        position: absolute;
        inset: auto -60px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        filter: blur(2px);
    }

    .teacher-scanner-hero h1 {
        position: relative;
        z-index: 1;
        margin: 0 0 12px;
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1.05;
        color: #fff;
    }

    .teacher-scanner-hero p {
        position: relative;
        z-index: 1;
        max-width: 600px;
        margin: 0;
        font-size: 16px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.88);
    }

    .teacher-scanner-layout {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .teacher-scanner-main {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        padding: 28px;
    }

    .teacher-scanner-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .teacher-scanner-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        padding: 20px;
    }

    .teacher-scanner-card.primary {
        border-color: #f97316;
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    }

    .teacher-scanner-card h3 {
        margin: 0 0 12px;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .teacher-scanner-card i {
        font-size: 18px;
        color: #f97316;
    }

    .teacher-scanner-card p {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .qr-video-container {
        position: relative;
        width: 100%;
        aspect-ratio: 1;
        border: 2px solid #f97316;
        border-radius: 16px;
        overflow: hidden;
        background: #000;
        margin-bottom: 20px;
    }

    #teacher-qr-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-video-hint {
        position: absolute;
        left: 12px;
        top: 12px;
        z-index: 2;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.72);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        backdrop-filter: blur(8px);
        pointer-events: none;
    }

    .scan-instructions {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #16a34a;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        color: #166534;
        font-size: 14px;
        line-height: 1.6;
    }

    .scan-instructions strong {
        display: block;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .scan-result {
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: none;
        font-weight: 600;
    }

    .scan-result.success {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        border: 1px solid #10b981;
        color: #166534;
    }

    .scan-result.error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 1px solid #ef4444;
        color: #991b1b;
    }

    .scan-result.warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #f59e0b;
        color: #92400e;
    }

    .scan-result i {
        margin-right: 8px;
        font-size: 18px;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .btn-scanner {
        flex: 1;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .btn-start {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
    }

    .btn-start:hover {
        box-shadow: 0 10px 25px rgba(249, 115, 22, 0.3);
        transform: translateY(-2px);
    }

    .btn-stop {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
    }

    .btn-stop:hover {
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
    }

    .recent-scans {
        max-height: 300px;
        overflow-y: auto;
    }

    .scan-item {
        padding: 12px;
        border-radius: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        margin-bottom: 8px;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .scan-item.success {
        background: #f0fdf4;
        border-color: #10b981;
    }

    .scan-item.error {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .scan-item strong {
        display: block;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .scan-item-time {
        font-size: 12px;
        color: #94a3b8;
    }

    .scan-item-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-state {
        text-align: center;
        padding: 20px;
        color: #94a3b8;
        font-size: 14px;
    }

    .empty-state i {
        display: block;
        font-size: 32px;
        margin-bottom: 8px;
        color: #cbd5e1;
    }

    @media (max-width: 1024px) {
        .teacher-scanner-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .teacher-scanner-hero {
            padding: 24px 18px;
        }

        .teacher-scanner-main {
            padding: 20px;
        }

        .teacher-scanner-hero h1 {
            font-size: 24px;
        }

        .teacher-scanner-hero p {
            font-size: 14px;
        }

        .button-group {
            flex-direction: column;
        }

        .qr-video-hint {
            left: 10px;
            top: 10px;
            font-size: 11px;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-qr-code-scan" style="margin-right: 8px;"></i>QR Code Scanner</h1>
    <p>Scan student QR codes to record attendance</p>
</div>

<div class="teacher-scanner-container">
    <!-- Hero Section -->
    <div class="teacher-scanner-hero">
        <h1><i class="bi bi-scan"></i> Student Attendance Scanner</h1>
        <p>Use your device camera to scan student QR codes and record their attendance automatically. Each scan will mark a student as present.</p>
    </div>

    <!-- Main Scanner Layout -->
    <div class="teacher-scanner-layout">
        <!-- Left: Scanner -->
        <div class="teacher-scanner-main">
            <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 20px;">
                <i class="bi bi-camera-fill" style="color: #f97316; margin-right: 8px;"></i>Camera Scanner
            </h2>

            <div class="scan-instructions">
                <strong>How to scan:</strong>
                1. Point camera at student's QR code<br>
                2. The whole camera view is active, no small box required<br>
                3. Attendance will be recorded automatically
            </div>

            <!-- Video Container -->
            <div class="qr-video-container">
                <video id="teacher-qr-video" autoplay playsinline muted></video>
                <div class="qr-video-hint">Scan anywhere in the frame</div>
            </div>

            <!-- Scan Result -->
            <div id="scan-result" class="scan-result">
                <i id="result-icon" class="bi"></i>
                <span id="result-message"></span>
            </div>

            <!-- Controls -->
            <div class="button-group">
                <button id="btn-start-scan" class="btn-scanner btn-start" type="button">
                    <i class="bi bi-play-circle"></i> Start Scanner
                </button>
                <button id="btn-stop-scan" class="btn-scanner btn-stop" type="button" style="display: none;">
                    <i class="bi bi-stop-circle"></i> Stop Scanner
                </button>
            </div>

            <!-- Manual Input -->
            <div style="margin-bottom: 20px;">
                <label for="manual-qr-input" style="display: block; margin-bottom: 8px; font-weight: 600; color: #0f172a;">
                    <i class="bi bi-keyboard" style="color: #f97316; margin-right: 6px;"></i>Or enter QR code manually:
                </label>
                <input 
                    type="text" 
                    id="manual-qr-input" 
                    placeholder="Paste QR code here..." 
                    style="width: 100%; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 14px; outline: none;"
                >
                <p style="margin: 8px 0 0; font-size: 12px; color: #64748b;">
                    Paste the QR code text and press Enter
                </p>
            </div>
        </div>

        <!-- Right: Activity Sidebar -->
        <div class="teacher-scanner-sidebar">
            <!-- Quick Stats -->
            <div class="teacher-scanner-card primary">
                <h3>
                    <i class="bi bi-clock"></i> Today's Scans
                </h3>
                <div style="font-size: 28px; font-weight: 800; color: #f97316; margin: 8px 0;">
                    <span id="scan-count">0</span>
                </div>
                <p style="margin: 8px 0 0; font-size: 13px;">
                    <i class="bi bi-person-check" style="margin-right: 6px;"></i>
                    <span id="scan-count-text">Students marked present</span>
                </p>
            </div>

            <!-- Active Codes Info -->
            <div class="teacher-scanner-card">
                <h3>
                    <i class="bi bi-info-circle"></i> Available QR Codes
                </h3>
                <p style="margin-bottom: 12px;">
                    {{ $todayQRCodes ? $todayQRCodes->count() : 0 }} active code{{ $todayQRCodes && $todayQRCodes->count() !== 1 ? 's' : '' }} available today
                </p>
                @if($todayQRCodes && $todayQRCodes->count() > 0)
                    <ul style="margin: 0; padding-left: 20px; font-size: 13px;">
                        @foreach($todayQRCodes->take(5) as $code)
                            <li style="margin-bottom: 4px; color: #475569;">
                                {{ $code->code }}
                            </li>
                        @endforeach
                        @if($todayQRCodes->count() > 5)
                            <li style="color: #94a3b8; font-style: italic;">
                                +{{ $todayQRCodes->count() - 5 }} more codes
                            </li>
                        @endif
                    </ul>
                @else
                    <div style="padding: 12px; background: #fef3c7; border-radius: 8px; color: #92400e; font-size: 13px;">
                        <i class="bi bi-exclamation-triangle"></i> No QR codes available
                    </div>
                @endif
            </div>

            <!-- Recent Scans -->
            <div class="teacher-scanner-card">
                <h3>
                    <i class="bi bi-list-ul"></i> Recent Scans
                </h3>
                <div id="recent-scans" class="recent-scans">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <div>No scans yet</div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="teacher-scanner-card" style="border-color: #3b82f6; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                <h3 style="color: #1e40af;">
                    <i class="bi bi-lightbulb" style="color: #3b82f6;"></i> Tips
                </h3>
                <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #1e40af; line-height: 1.7;">
                    <li>Use good lighting for better scans</li>
                    <li>Hold camera steady</li>
                    <li>QR code should be clear and visible</li>
                    <li>Each scan records one attendance</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Canvas for QR Detection -->
<canvas id="qr-canvas" style="display: none;"></canvas>

@vite('resources/js/jsqr-loader.js')
<script>
    let isScanning = false;
    let scanTimer = null;
    let activeStream = null;
    let lastScannedCode = null;
    let lastScannedAt = 0;
    let jsQRReady = false;
    const MAX_RECENT_SCANS = 5;
    const barcodeDetector = (() => {
        try {
            if (typeof window.BarcodeDetector !== 'function') {
                return null;
            }

            return new window.BarcodeDetector({ formats: ['qr_code'] });
        } catch (error) {
            console.warn('BarcodeDetector is unavailable, falling back to jsQR.', error);
            return null;
        }
    })();

    const jsQRPoll = setInterval(() => {
        if (typeof window.jsQR === 'function') {
            jsQRReady = true;
            clearInterval(jsQRPoll);
            console.log('jsQR loaded for teacher scanner');
        }
    }, 100);

    function waitForJsQR(timeoutMs = 5000) {
        return new Promise((resolve) => {
            if (typeof window.jsQR === 'function') {
                jsQRReady = true;
                resolve(true);
                return;
            }

            const poll = setInterval(() => {
                if (typeof window.jsQR === 'function') {
                    jsQRReady = true;
                    clearInterval(poll);
                    clearTimeout(timer);
                    resolve(true);
                }
            }, 100);

            const timer = setTimeout(() => {
                clearInterval(poll);
                resolve(false);
            }, timeoutMs);
        });
    }

    setTimeout(() => {
        if (!jsQRReady && typeof window.jsQR !== 'function') {
            console.warn('jsQR did not load in time for teacher scanner.');
        }
    }, 5000);

    function stopScanner() {
        const video = document.getElementById('teacher-qr-video');

        if (scanTimer) {
            clearTimeout(scanTimer);
            scanTimer = null;
        }

        if (activeStream) {
            activeStream.getTracks().forEach(track => track.stop());
            activeStream = null;
        } else if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }

        isScanning = false;
        document.getElementById('btn-start-scan').style.display = 'inline-flex';
        document.getElementById('btn-stop-scan').style.display = 'none';
    }

    // Start Scanner
    document.getElementById('btn-start-scan').addEventListener('click', startScanner);

    // Stop Scanner
    document.getElementById('btn-stop-scan').addEventListener('click', stopScanner);

    async function startScanner() {
        if (isScanning) {
            return;
        }

        try {
            const video = document.getElementById('teacher-qr-video');
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            });

            video.srcObject = stream;
            activeStream = stream;

            await new Promise((resolve, reject) => {
                const onReady = () => {
                    video.play().then(resolve).catch(reject);
                };

                if (video.readyState >= HTMLMediaElement.HAVE_METADATA) {
                    onReady();
                    return;
                }

                video.onloadedmetadata = onReady;
                setTimeout(() => {
                    if (video.readyState < HTMLMediaElement.HAVE_METADATA) {
                        reject(new Error('Camera stream did not become ready in time.'));
                    }
                }, 5000);
            });

            isScanning = true;
            document.getElementById('btn-start-scan').style.display = 'none';
            document.getElementById('btn-stop-scan').style.display = 'inline-flex';

            const jsQRLoaded = await waitForJsQR(5000);
            if (!barcodeDetector && !jsQRLoaded) {
                showResult('error', 'QR scanner decoder failed to load. Refresh the page and try again.');
                stopScanner();
                return;
            }

            showResult('warning', 'Camera ready. Hold the QR code inside the frame.');
            scanQRCode();
        } catch (error) {
            console.error('Camera access error:', error);
            showResult('warning', 'Camera access failed. Check permissions and try again.');
        }
    }

    // Continuous QR Scanning
    async function scanQRCode() {
        if (!isScanning) return;

        const video = document.getElementById('teacher-qr-video');

        if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA && video.videoWidth > 0) {
            try {
                const code = await detectQRCode(video);
                if (code) {
                    processScannedCode(code);
                }
            } catch (error) {
                // Ignore frame decode errors and keep scanning.
            }
        }

        scanTimer = setTimeout(scanQRCode, 120);
    }

    async function detectQRCode(video) {
        if (barcodeDetector) {
            try {
                const barcodes = await barcodeDetector.detect(video);
                if (barcodes.length > 0 && barcodes[0].rawValue) {
                    return barcodes[0].rawValue.trim();
                }
            } catch (error) {
                console.warn('BarcodeDetector failed, falling back to jsQR.', error);
            }
        }

        if (jsQRReady && typeof window.jsQR === 'function') {
            const canvas = document.getElementById('qr-canvas');
            const context = canvas.getContext('2d', { willReadFrequently: true });

            const tryDecode = (imageData) => {
                const qrCode = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: 'dontInvert',
                });

                if (qrCode && qrCode.data) {
                    return qrCode.data.trim();
                }

                for (let i = 0; i < imageData.data.length; i += 4) {
                    imageData.data[i] = 255 - imageData.data[i];
                    imageData.data[i + 1] = 255 - imageData.data[i + 1];
                    imageData.data[i + 2] = 255 - imageData.data[i + 2];
                }

                const invertedQrCode = jsQR(imageData.data, imageData.width, imageData.height);

                if (invertedQrCode && invertedQrCode.data) {
                    return invertedQrCode.data.trim();
                }

                return '';
            };

            const scanScales = [1, 0.85, 0.7, 0.55];

            for (const scale of scanScales) {
                const targetWidth = Math.max(240, Math.floor(video.videoWidth * scale));
                const targetHeight = Math.max(240, Math.floor(video.videoHeight * scale));

                canvas.width = targetWidth;
                canvas.height = targetHeight;
                context.drawImage(video, 0, 0, targetWidth, targetHeight);

                const imageData = context.getImageData(0, 0, targetWidth, targetHeight);
                const code = tryDecode(imageData);

                if (code) {
                    return code;
                }
            }
        }

        return '';
    }

    // Manual QR Input
    document.getElementById('manual-qr-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && this.value.trim()) {
            processScannedCode(this.value.trim());
            this.value = '';
        }
    });

    // Process Scanned Code
    function processScannedCode(code) {
        const normalizedCode = (code || '').trim();

        if (!normalizedCode) {
            return;
        }

        const now = Date.now();
        if (normalizedCode === lastScannedCode && (now - lastScannedAt) < 2500) {
            return;
        }

        lastScannedCode = normalizedCode;
        lastScannedAt = now;

        // Send to backend
        fetch('{{ route("qrcode.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code: normalizedCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showResult('success', `✓ ${data.message || 'Attendance recorded'}`);
                addRecentScan(data.student_name || 'Unknown', 'success');
                updateScanCount();
            } else {
                showResult('error', `✗ ${data.message || 'Invalid QR code'}`);
                addRecentScan(normalizedCode, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showResult('error', '✗ Error processing QR code');
        });
    }

    // Show Result Message
    function showResult(type, message) {
        const resultEl = document.getElementById('scan-result');
        const icon = document.getElementById('result-icon');
        const messageEl = document.getElementById('result-message');

        resultEl.className = 'scan-result ' + type;
        messageEl.textContent = message;

        if (type === 'success') {
            icon.className = 'bi bi-check-circle-fill';
        } else if (type === 'error') {
            icon.className = 'bi bi-x-circle-fill';
        } else {
            icon.className = 'bi bi-exclamation-circle-fill';
        }

        resultEl.style.display = 'block';
        setTimeout(() => {
            resultEl.style.display = 'none';
        }, 3000);
    }

    // Add Recent Scan
    function addRecentScan(name, status) {
        const container = document.getElementById('recent-scans');
        
        // Clear empty state if exists
        if (container.querySelector('.empty-state')) {
            container.innerHTML = '';
        }

        const time = new Date().toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });

        const scanItem = document.createElement('div');
        scanItem.className = `scan-item ${status}`;
        scanItem.innerHTML = `
            <div>
                <strong>${name}</strong>
                <div class="scan-item-time">${time}</div>
            </div>
            <span class="scan-item-badge badge-${status}">
                ${status === 'success' ? 'RECORDED' : 'FAILED'}
            </span>
        `;

        container.insertBefore(scanItem, container.firstChild);

        // Keep only last 5 scans
        while (container.children.length > MAX_RECENT_SCANS) {
            container.removeChild(container.lastChild);
        }
    }

    // Update Scan Count
    function updateScanCount() {
        const count = parseInt(document.getElementById('scan-count').textContent) + 1;
        document.getElementById('scan-count').textContent = count;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Check if camera is available
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showResult('warning', 'Camera not supported on this device');
        }
    });
</script>
@endsection

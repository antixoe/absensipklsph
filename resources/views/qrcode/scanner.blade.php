@extends('layouts.app')

@section('styles')
<style>
    .scanner-shell {
        max-width: 1100px;
        margin: 0 auto;
    }

    .scanner-hero {
        position: relative;
        overflow: hidden;
        padding: 28px;
        border-radius: 20px;
        color: #fff;
        background:
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.18), transparent 32%),
            linear-gradient(135deg, #7c2d12 0%, #ea580c 52%, #f97316 100%);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        margin-bottom: 22px;
    }

    .scanner-hero::after {
        content: "";
        position: absolute;
        inset: auto -60px -80px auto;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        filter: blur(2px);
    }

    .scanner-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 14px;
    }

    .scanner-hero__title {
        position: relative;
        z-index: 1;
        max-width: 680px;
    }

    .scanner-hero__title h1 {
        margin: 0 0 10px;
        color: #fff;
        font-size: clamp(28px, 4vw, 42px);
        line-height: 1.05;
    }

    .scanner-hero__title p {
        margin: 0;
        color: rgba(255, 255, 255, 0.88);
        font-size: 16px;
        line-height: 1.6;
    }

    .scanner-hero__chips {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 22px;
        max-width: 520px;
    }

    .scanner-chip {
        padding: 14px 16px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(8px);
    }

    .scanner-chip span {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.74);
        margin-bottom: 4px;
    }

    .scanner-chip strong {
        display: block;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
    }

    .scanner-banner {
        margin-bottom: 20px;
        padding: 16px 18px;
        border-radius: 16px;
        border: 1px solid transparent;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
    }

    .scanner-banner--success {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #10b981;
        color: #166534;
    }

    .scanner-banner--warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #f59e0b;
        color: #92400e;
    }

    .scanner-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
        gap: 22px;
        align-items: start;
    }

    .scanner-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        padding: 22px;
    }

    .scanner-panel + .scanner-panel {
        margin-top: 18px;
    }

    .scanner-steps {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .scanner-step {
        padding: 14px 12px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.2s ease;
    }

    .scanner-step.is-active {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border-color: #fb923c;
        box-shadow: 0 10px 22px rgba(249, 115, 22, 0.12);
    }

    .scanner-step.is-complete {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border-color: #34d399;
    }

    .scanner-step.is-warning {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border-color: #f59e0b;
    }

    .scanner-step__badge {
        width: 42px;
        height: 42px;
        margin: 0 auto 8px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e2e8f0;
        color: #334155;
        font-weight: 800;
        font-size: 15px;
        line-height: 1;
    }

    .scanner-step.is-active .scanner-step__badge {
        background: #f97316;
        color: #fff;
    }

    .scanner-step.is-complete .scanner-step__badge {
        background: #10b981;
        color: #fff;
    }

    .scanner-step__label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .scanner-status {
        display: none;
        margin-bottom: 18px;
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.5;
        font-weight: 600;
    }

    .scanner-panel__header {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .scanner-panel__icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        color: #ea580c;
        font-size: 24px;
    }

    .scanner-panel__header h3 {
        margin: 0 0 6px;
        font-size: 18px;
        color: #0f172a;
    }

    .scanner-panel__header p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.5;
    }

    .scanner-input {
        width: 100%;
        padding: 13px 14px;
        border: 1px solid #dbe3ea;
        border-radius: 12px;
        background: #fff;
        color: #0f172a;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .scanner-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.12);
    }

    .scanner-input:disabled {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
    }

    .scanner-form {
        display: flex;
        gap: 10px;
        margin-top: 12px;
    }

    .scanner-btn {
        flex: 0 0 auto;
        min-width: 140px;
        padding: 13px 16px;
        border-radius: 12px;
        font-weight: 700;
    }

    .scanner-media {
        position: relative;
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        aspect-ratio: 3 / 4;
        border-radius: 18px;
        overflow: hidden;
        background: linear-gradient(135deg, #e2e8f0 0%, #f8fafc 100%);
        border: 1px dashed #cbd5e1;
    }

    .scanner-media video,
    .scanner-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }

    .scanner-media__placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #94a3b8;
        text-align: center;
        padding: 20px;
    }

    .scanner-media__placeholder i {
        font-size: 62px;
        color: #cbd5e1;
    }

    .scanner-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .scanner-actions .btn {
        flex: 1 1 160px;
        padding: 12px 14px;
        border-radius: 12px;
        font-weight: 700;
    }

    .scanner-note {
        margin-top: 12px;
        color: #64748b;
        font-size: 13px;
        line-height: 1.6;
    }

    .scanner-location {
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #16a34a;
        color: #166534;
    }

    .scanner-location__title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-weight: 800;
        font-size: 15px;
    }

    .scanner-location__grid {
        display: grid;
        gap: 8px;
        font-size: 13px;
        line-height: 1.7;
    }

    .scanner-summary {
        display: grid;
        gap: 12px;
    }

    .scanner-summary__item {
        padding: 14px 15px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .scanner-summary__item strong {
        display: block;
        margin-bottom: 6px;
        color: #0f172a;
        font-size: 14px;
    }

    .scanner-summary__item span {
        color: #64748b;
        font-size: 13px;
        line-height: 1.5;
    }

    .scanner-summary__checklist {
        margin: 0;
        padding-left: 18px;
        color: #475569;
        font-size: 13px;
        line-height: 1.8;
    }

    .scanner-summary__checklist li + li {
        margin-top: 4px;
    }

    .scanner-submit-wrap {
        margin-top: 18px;
    }

    .scanner-submit-wrap .btn {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        font-weight: 800;
    }

    .scanner-help {
        margin-top: 18px;
        padding: 16px;
        border-radius: 16px;
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        border: 1px solid #fb923c;
        color: #9a3412;
        font-size: 13px;
        line-height: 1.7;
    }

    .scanner-help ul {
        margin: 10px 0 0;
        padding-left: 18px;
    }

    .scanner-help li + li {
        margin-top: 4px;
    }

    @media (max-width: 960px) {
        .scanner-grid {
            grid-template-columns: 1fr;
        }

        .scanner-hero__chips {
            grid-template-columns: 1fr;
            max-width: none;
        }
    }

    @media (max-width: 640px) {
        .scanner-hero {
            padding: 22px 18px;
            border-radius: 18px;
        }

        .scanner-panel {
            padding: 18px;
            border-radius: 18px;
        }

        .scanner-steps {
            grid-template-columns: 1fr;
        }

        .scanner-form {
            flex-direction: column;
        }

        .scanner-btn {
            width: 100%;
            min-width: 0;
        }

        .scanner-actions .btn {
            flex-basis: 100%;
        }
    }
</style>
@endsection

@section('content')
    @php($isCheckoutPage = request()->routeIs('absen.pulang'))
    @php($pageTitle = $isCheckoutPage ? 'Absen Pulang' : 'Mark Attendance')
    @php($pageSubtitle = $isCheckoutPage
        ? 'Checkout flow for students. Scan the QR again when it is time to go home.'
        : 'Check in flow for students. Scan the QR, take a selfie, and share your location.')
    <div class="page-header">
        <h1><i class="bi bi-qr-code-scan" style="margin-right: 8px;"></i>{{ $pageTitle }}</h1>
        <p>{{ $pageSubtitle }}</p>
    </div>

    @if($isCheckoutPage)
        <div class="scanner-hero" style="margin-bottom: 20px;">
            <div class="scanner-hero__title">
                <div class="scanner-hero__eyebrow" style="background: rgba(255, 255, 255, 0.18);">
                    <i class="bi bi-box-arrow-right"></i>
                    Checkout mode
                </div>
                <h1 style="margin-bottom: 8px;">Jam pulang checkout</h1>
                <p>Use the same QR flow again to mark checkout. This page is meant to feel different from the check-in page, so students know they are leaving, not arriving.</p>
            </div>

            <div class="scanner-hero__chips">
                <div class="scanner-chip">
                    <span>Flow</span>
                    <strong>Checkout only</strong>
                </div>
                <div class="scanner-chip">
                    <span>Action</span>
                    <strong>Scan the QR again</strong>
                </div>
                <div class="scanner-chip">
                    <span>Result</span>
                    <strong>Save checkout time</strong>
                </div>
            </div>
        </div>
    @endif

    @if($isCheckoutPage && $todayAbsence && $todayAbsence->scanned_qr_out_at)
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>Checkout already recorded today</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">
                Checked out at: {{ optional($todayAbsence->scanned_qr_out_at)->format('H:i:s') ?? 'N/A' }}
            </p>
            <p style="margin: 6px 0 0 0; font-size: 13px;">
                You can review the checkout record below.
            </p>
        </div>
    @elseif($isCheckoutPage && $todayAbsence)
        <div style="padding: 15px 20px; background: #fffbeb; border: 2px solid #f59e0b; border-radius: 8px; margin-bottom: 20px; color: #92400e;">
            <i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>
            <strong>Check-in recorded. Ready for checkout scan.</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">
                Checked in at: {{ optional($todayAbsence->scanned_qr_at)->format('H:i:s') ?? 'N/A' }}
            </p>
            <p style="margin: 6px 0 0 0; font-size: 13px;">
                Scan the same QR code again to save your pulang time.
            </p>
        </div>
    @elseif($todayAbsence)
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>You have already marked your attendance today</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">
                <i class="bi bi-box-arrow-in-right" style="margin-right: 5px;"></i>
                <strong>Jam Masuk (Check-in):</strong> {{ optional($todayAbsence->scanned_qr_at)->format('H:i:s') ?? 'N/A' }}
            </p>
            @if($todayAbsence->scanned_qr_out_at)
                <p style="margin: 6px 0 0 0; font-size: 14px;">
                    <i class="bi bi-box-arrow-out-right" style="margin-right: 5px;"></i>
                    <strong>Jam Pulang (Check-out):</strong> {{ $todayAbsence->scanned_qr_out_at->format('H:i:s') }}
                </p>
            @else
                <p style="margin: 6px 0 0 0; font-size: 13px;">
                    Scan the same QR code again at jam pulang to save checkout automatically.
                </p>
            @endif
        </div>
    @elseif(!$todayQRCodes)
        <div style="padding: 15px 20px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; margin-bottom: 20px; color: #92400e;">
            <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
            <strong>{{ $isCheckoutPage ? 'No checkout QR codes available today' : 'No QR codes available today' }}</strong>
            <p style="margin: 8px 0 0 0; font-size: 14px;">Wait for your instructor to generate codes for today.</p>
        </div>
    @endif

    @if($todayQRCodes)
    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <!-- Progress Indicators -->
        <div style="display: flex; gap: 15px; margin-bottom: 30px; padding: 15px; background: #f5f5f5; border-radius: 6px;">
            <div id="step1-step" style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #f97316; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step1-icon">1</div>
                <div style="font-weight: 600; font-size: 13px;">{{ $isCheckoutPage ? 'QR Pulang' : 'Scan QR' }}</div>
            </div>
            <div id="step2-step" style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #d1d5db; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step2-icon">2</div>
                <div style="font-weight: 600; font-size: 13px;">{{ $isCheckoutPage ? 'Selfie Out' : 'Selfie' }}</div>
            </div>
            <div id="step3-step" style="flex: 1; text-align: center;">
                <div style="width: 40px; height: 40px; background: #d1d5db; color: #666; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: 600;" id="step3-icon">3</div>
                <div style="font-weight: 600; font-size: 13px;">{{ $isCheckoutPage ? 'Confirm' : 'Location' }}</div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="status-message" style="display: none; padding: 15px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;"></div>

        <!-- Step 1: QR Code Scanner -->
        <div id="step1-container" style="margin-bottom: 30px; padding: 20px; border: 3px solid {{ $isCheckoutPage ? '#dc2626' : '#f97316' }}; border-radius: 8px; background: {{ $isCheckoutPage ? '#fff7f7' : '#fafafa' }};">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-qr-code" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Step 1: Scan QR Code for Checkout' : 'Step 1: Scan QR Code with Camera' }}
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px; text-align: center;">
                @if($isCheckoutPage)
                    <div style="position: relative; width: 100%; max-width: 400px; margin: 0 auto; overflow: hidden; border-radius: 6px;">
                        <video id="qr-video" autoplay playsinline muted style="width: 100%; height: 100%; aspect-ratio: 1; display: none; background: #000; object-fit: cover;"></video>
                        <canvas id="qr-canvas" style="display: none;"></canvas>

                        <div id="qr-scan-overlay" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; aspect-ratio: 1; border: 3px solid #dc2626; border-radius: 6px; animation: scanPulse 1.5s ease-in-out infinite; pointer-events: none;">
                            <div style="position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, #dc2626, transparent); animation: scanLine 1s ease-in-out infinite;"></div>
                        </div>

                        <div id="qr-placeholder" style="width: 100%; max-width: 400px; aspect-ratio: 1; background: #fee2e2; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #991b1b; border: 1px dashed #dc2626;">
                            <i class="bi bi-box-arrow-right" style="font-size: 80px;"></i>
                        </div>
                    </div>

                    <button type="button" id="startQRBtn" style="width: 100%; margin-top: 15px; padding: 12px 15px; background: #dc2626; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                        <i class="bi bi-camera" style="margin-right: 8px;"></i>Start Checkout Scanner
                    </button>
                    <button type="button" id="stopQRBtn" style="width: 100%; margin-top: 10px; padding: 12px 15px; background: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; display: none;">
                        <i class="bi bi-stop-circle" style="margin-right: 8px;"></i>Stop Scanner
                    </button>

                    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">
                        <p style="text-align: center; font-size: 14px; color: #666; margin-bottom: 10px;">
                            <strong>Can't scan?</strong> Enter checkout QR manually:
                        </p>
                        <form id="manual-qr-form" style="display: flex; gap: 10px;">
                            <input type="text" id="manual-qr-code" placeholder="Enter checkout QR code here"
                                   style="flex: 1; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <button type="submit" style="padding: 12px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                <i class="bi bi-arrow-right" style="margin-right: 8px;"></i>Submit
                            </button>
                        </form>
                        <small style="color: #999; display: block; margin-top: 8px; text-align: center;">
                            Use the checkout QR shown by your instructor
                        </small>
                    </div>
                @else
                    <input type="text" id="qr-input" placeholder="Enter QR code or paste here"
                           style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 14px; text-align: center;">

                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                        <p style="text-align: center; font-size: 14px; color: #666; margin-bottom: 10px;">
                            <strong>Can't scan?</strong> Enter QR code manually:
                        </p>
                        <form id="manual-form" style="display: flex; gap: 10px;">
                            <input type="text" id="manual-code" placeholder="Or enter code: QR-A1B2C3D4"
                                   style="flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                            <button type="submit" class="btn" style="padding: 10px 15px;">Submit</button>
                        </form>
                        <small style="color: #666; display: block; margin-top: 8px; text-align: center;">
                            Your instructor will provide the QR code value
                        </small>
                    </div>
                @endif
            </div>
            <small style="color: #666; display: block; margin-top: 10px; text-align: center;">
                <i class="bi bi-info-circle"></i> {{ $isCheckoutPage ? 'Point your camera at the QR code to scan checkout automatically' : 'Point your camera at the QR code to scan it automatically' }}
            </small>
        </div>

        <!-- Step 2: Selfie Capture -->
        <div id="step2-container" style="margin-bottom: 30px; padding: 20px; border: 2px solid #ddd; border-radius: 8px; background: #fafafa; display: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Step 2: Take Selfie for Checkout' : 'Step 2: Take Selfie' }}
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div style="text-align: center;">
                    <video id="selfie-video" autoplay playsinline muted style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 6px; display: none; background: #000;"></video>
                    <img id="selfie-preview" style="width: 100%; max-width: 400px; margin: 0 auto; border-radius: 6px; display: none;">

                    <div id="selfie-placeholder" style="width: 100%; max-width: 400px; margin: 0 auto; aspect-ratio: 3/4; background: #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #999;">
                        <i class="bi bi-camera" style="font-size: 64px;"></i>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button type="button" id="start-selfie-btn" class="btn" style="flex: 1; padding: 10px 15px;">
                        <i class="bi bi-camera" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Start Checkout Camera' : 'Start Camera' }}
                    </button>
                    <button type="button" id="take-selfie-btn" class="btn" style="flex: 1; padding: 10px 15px; display: none;">
                        <i class="bi bi-camera-fill" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Capture Selfie' : 'Take Photo' }}
                    </button>
                    <button type="button" id="retake-selfie-btn" class="btn btn-secondary" style="flex: 1; padding: 10px 15px; display: none;">
                        <i class="bi bi-arrow-counterclockwise" style="margin-right: 8px;"></i>Retake
                    </button>
                </div>
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-lightbulb"></i> {{ $isCheckoutPage ? 'Keep your face clearly visible for checkout verification' : 'Make sure your face is clearly visible in the camera' }}
            </small>
        </div>

        <!-- Step 3: Location & IP Detection -->
        <div id="step3-container" style="margin-bottom: 30px; padding: 20px; border: 2px solid #ddd; border-radius: 8px; background: #fafafa; display: none;">
            <h3 style="margin-bottom: 15px; font-size: 16px; font-weight: 600;">
                <i class="bi bi-geo-alt-fill" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Step 3: Checkout Location' : 'Step 3: Location Detected' }}
            </h3>
            <div style="padding: 15px; background: #fff; border-radius: 6px;">
                <div id="location-info" style="padding: 15px; background: #f0fdf4; border: 2px solid #16a34a; border-radius: 6px; color: #166534;">
                    <strong style="display: block; margin-bottom: 12px;">{{ $isCheckoutPage ? 'Checkout Location Information' : 'Location Information' }}</strong>
                    <div style="font-size: 13px; line-height: 2;">
                        <div><i class="bi bi-geo-alt" style="margin-right: 5px;"></i><strong>Latitude:</strong> <span id="location-lat">Detecting...</span></div>
                        <div><i class="bi bi-geo-alt" style="margin-right: 5px;"></i><strong>Longitude:</strong> <span id="location-lng">Detecting...</span></div>
                        <div><i class="bi bi-globe" style="margin-right: 5px;"></i><strong>IP Address:</strong> <span id="location-ip">Detecting...</span></div>
                        <div><i class="bi bi-clock" style="margin-right: 5px;"></i><strong>Time:</strong> <span id="location-time">—</span></div>
                    </div>
                </div>
            </div>
            <small style="color: #666; display: block; margin-top: 10px;">
                <i class="bi bi-info-circle"></i> {{ $isCheckoutPage ? 'Your checkout location and IP have been automatically detected' : 'Your location and IP have been automatically detected' }}
            </small>
        </div>

        <!-- Submit Button -->
        <div style="display: flex; gap: 10px;">
            <button type="button" id="submit-attendance-btn" class="btn" style="flex: 1; padding: 12px 20px; display: none; cursor: pointer; background: #f97316; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 14px;">
                <i class="bi bi-check-circle" style="margin-right: 8px;"></i>{{ $isCheckoutPage ? 'Complete Checkout' : 'Complete Attendance' }}
            </button>
        </div>

        <!-- Tips -->
        <div style="margin-top: 25px; padding: 15px; background: #fef3c7; border: 2px solid #ea580c; border-radius: 6px; color: #92400e; font-size: 13px;">
            <i class="bi bi-lightbulb" style="margin-right: 8px;"></i>
            <strong>{{ $isCheckoutPage ? 'Tips for Checkout QR Scanning:' : 'Tips for QR Code Scanning:' }}</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                <li>{{ $isCheckoutPage ? 'Make sure the checkout QR code is well-lit and clearly visible' : 'Make sure the QR code is well-lit and clearly visible' }}</li>
                <li>{{ $isCheckoutPage ? 'Keep the checkout QR code within 10-30 cm from your camera' : 'Keep the QR code within 10-30 cm from your camera' }}</li>
                <li>{{ $isCheckoutPage ? 'Hold the device steady at a slight angle (avoid glare)' : 'Hold the device steady at a slight angle (avoid glare)' }}</li>
                <li>{{ $isCheckoutPage ? 'The checkout QR will automatically detect - no manual input needed' : 'The QR code will automatically detect - no manual input needed' }}</li>
                <li>{{ $isCheckoutPage ? 'After QR detected → take selfie → location detected → complete checkout' : 'After QR detected → take selfie → location detected → submit' }}</li>
            </ul>
        </div>
    </div>
    @endif

    @if($todayQRCodes)
        @vite('resources/js/jsqr-loader.js')
        <script>
            const state = {
                qrCode: null,
                selfieBlob: null,
                latitude: null,
                longitude: null,
                ipAddress: null
            };

            const isCheckoutPage = {{ $isCheckoutPage ? 'true' : 'false' }};

            const qrInput = document.getElementById('qr-input');
            const manualForm = document.getElementById('manual-form');
            const manualCode = document.getElementById('manual-code');
            const manualQRForm = document.getElementById('manual-qr-form');
            const manualQRCode = document.getElementById('manual-qr-code');
            const qrVideoEl = document.getElementById('qr-video');
            const qrCanvasEl = document.getElementById('qr-canvas');
            const qrPlaceholderEl = document.getElementById('qr-placeholder');
            const qrScanOverlay = document.getElementById('qr-scan-overlay');
            const startQRBtn = document.getElementById('startQRBtn');
            const stopQRBtn = document.getElementById('stopQRBtn');
            const statusMessage = document.getElementById('status-message');

            const step1Container = document.getElementById('step1-container');
            const step2Container = document.getElementById('step2-container');
            const step3Container = document.getElementById('step3-container');

            const step1Icon = document.getElementById('step1-icon');
            const step2Icon = document.getElementById('step2-icon');
            const step3Icon = document.getElementById('step3-icon');

            const step1Step = document.getElementById('step1-step');
            const step2Step = document.getElementById('step2-step');
            const step3Step = document.getElementById('step3-step');

            const startSelfieBtnEl = document.getElementById('start-selfie-btn');
            const takeSelfieBtnEl = document.getElementById('take-selfie-btn');
            const retakeSelfieBtnEl = document.getElementById('retake-selfie-btn');
            const selfieVideoEl = document.getElementById('selfie-video');
            const selfiePreviewEl = document.getElementById('selfie-preview');
            const selfiePlaceholderEl = document.getElementById('selfie-placeholder');
            const submitBtnEl = document.getElementById('submit-attendance-btn');

            let qrStream = null;
            let qrScannerActive = false;
            let qrScanInterval = null;
            let selfieStream = null;

            if (qrInput) {
                qrInput.focus();
                qrInput.addEventListener('change', function () {
                    if (this.value.trim()) {
                        validateAndSetQRCode(this.value.trim());
                    }
                });
            }

            if (manualForm) {
                manualForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const code = manualCode.value.trim();

                    if (code) {
                        validateAndSetQRCode(code);
                    }
                });
            }

            if (manualQRForm) {
                manualQRForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const code = manualQRCode.value.trim();

                    if (code) {
                        validateAndSetQRCode(code);
                    }
                });
            }

            function initCheckoutScanner() {
                if (!isCheckoutPage || !startQRBtn || !stopQRBtn || !qrVideoEl || !qrCanvasEl || !qrPlaceholderEl || !qrScanOverlay) {
                    return;
                }

                startQRBtn.addEventListener('click', startCheckoutScanner);
                stopQRBtn.addEventListener('click', stopCheckoutScanner);

                setTimeout(() => {
                    if (!qrScannerActive) {
                        startCheckoutScanner();
                    }
                }, 300);
            }

            async function startCheckoutScanner() {
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

                    await new Promise((resolve, reject) => {
                        qrVideoEl.onloadedmetadata = () => {
                            qrVideoEl.play().then(resolve).catch(reject);
                        };
                        setTimeout(() => {
                            if (qrVideoEl.readyState < 2) {
                                reject(new Error('Video metadata timeout'));
                            }
                        }, 5000);
                    });

                    qrVideoEl.style.display = 'block';
                    qrPlaceholderEl.style.display = 'none';
                    qrScanOverlay.style.display = 'block';
                    startQRBtn.style.display = 'none';
                    stopQRBtn.style.display = 'block';
                    qrScannerActive = true;

                    showStatus('Scanner ready. Point your camera at the checkout QR code.', 'success');
                    startCheckoutScanLoop();
                } catch (error) {
                    showStatus('Camera access denied. Please enable camera permissions.', 'error');
                }
            }

            function startCheckoutScanLoop() {
                let scanCount = 0;

                if (qrScanInterval) {
                    clearInterval(qrScanInterval);
                }

                qrScanInterval = setInterval(() => {
                    if (!qrScannerActive) {
                        return;
                    }

                    try {
                        if (typeof jsQR === 'undefined') {
                            return;
                        }

                        const video = qrVideoEl;
                        const canvas = qrCanvasEl;

                        if (video.videoWidth === 0 || video.videoHeight === 0) {
                            return;
                        }

                        if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                        }

                        const ctx = canvas.getContext('2d', { willReadFrequently: true });
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                        let code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (!code || !code.data) {
                            for (let i = 0; i < imageData.data.length; i += 4) {
                                imageData.data[i] = 255 - imageData.data[i];
                                imageData.data[i + 1] = 255 - imageData.data[i + 1];
                                imageData.data[i + 2] = 255 - imageData.data[i + 2];
                            }
                            code = jsQR(imageData.data, imageData.width, imageData.height);
                        }

                        if (code && code.data) {
                            qrScannerActive = false;
                            clearInterval(qrScanInterval);
                            validateAndSetQRCode(code.data);
                            return;
                        }

                        scanCount++;
                        if (scanCount % 25 === 0) {
                            console.log(`Checkout scanning... (${scanCount} frames scanned)`);
                        }
                    } catch (error) {
                        console.error('Checkout scan loop error:', error);
                    }
                }, 200);
            }

            function stopCheckoutScanner() {
                qrScannerActive = false;

                if (qrScanInterval) {
                    clearInterval(qrScanInterval);
                }

                if (qrStream) {
                    qrStream.getTracks().forEach(track => track.stop());
                }

                if (qrVideoEl) {
                    qrVideoEl.style.display = 'none';
                }
                if (qrScanOverlay) {
                    qrScanOverlay.style.display = 'none';
                }
                if (qrPlaceholderEl) {
                    qrPlaceholderEl.style.display = 'flex';
                }
                if (startQRBtn) {
                    startQRBtn.style.display = 'block';
                }
                if (stopQRBtn) {
                    stopQRBtn.style.display = 'none';
                }
            }

            initCheckoutScanner();

            function setStepComplete(stepEl, badgeEl) {
                if (!stepEl || !badgeEl) {
                    return;
                }
                stepEl.classList.remove('is-active');
                stepEl.classList.add('is-complete');
                badgeEl.innerHTML = '<i class="bi bi-check-lg"></i>';
            }

            function setStepActive(stepEl) {
                if (!stepEl) {
                    return;
                }
                stepEl.classList.add('is-active');
            }

            function validateAndSetQRCode(code) {
                showStatus('Validating QR code...', 'info');

                state.qrCode = code;
                if (qrInput) {
                    qrInput.value = code;
                    qrInput.disabled = true;
                }
                if (manualCode) {
                    manualCode.disabled = true;
                }
                if (manualForm) {
                    const submitBtn = manualForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                }
                if (manualQRCode) {
                    manualQRCode.disabled = true;
                }
                if (manualQRForm) {
                    const submitBtn = manualQRForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                }

                if (isCheckoutPage) {
                    stopCheckoutScanner();
                }

                setStepComplete(step1Step, step1Icon);

                setTimeout(() => {
                    step1Container.style.display = 'none';
                    step2Container.style.display = 'block';
                    setStepActive(step2Step);
                    showStatus('QR code accepted. Continue with the selfie step.', 'success');
                    startSelfieBtnEl.focus();
                }, 700);
            }

            startSelfieBtnEl.addEventListener('click', async function () {
                try {
                    selfieStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                    });

                    selfieVideoEl.srcObject = selfieStream;
                    await selfieVideoEl.play();

                    selfieVideoEl.style.display = 'block';
                    selfiePlaceholderEl.style.display = 'none';
                    startSelfieBtnEl.style.display = 'none';
                    takeSelfieBtnEl.style.display = 'block';
                    showStatus('Camera ready. Take the photo when your face is centered.', 'info');
                } catch (error) {
                    showStatus('Camera access denied. Please enable camera permissions.', 'error');
                }
            });

            takeSelfieBtnEl.addEventListener('click', function () {
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                canvas.width = selfieVideoEl.videoWidth;
                canvas.height = selfieVideoEl.videoHeight;
                context.drawImage(selfieVideoEl, 0, 0);

                canvas.toBlob(blob => {
                    state.selfieBlob = blob;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        selfiePreviewEl.src = e.target.result;
                        selfiePreviewEl.style.display = 'block';
                        selfieVideoEl.style.display = 'none';
                        selfiePlaceholderEl.style.display = 'none';

                        takeSelfieBtnEl.style.display = 'none';
                        retakeSelfieBtnEl.style.display = 'block';

                        if (selfieStream) {
                            selfieStream.getTracks().forEach(track => track.stop());
                        }

                        setStepComplete(step2Step, step2Icon);

                        setTimeout(() => {
                            step2Container.style.display = 'none';
                            step3Container.style.display = 'block';
                            setStepActive(step3Step);
                            showStatus('Selfie captured. Detecting your location and IP address...', 'info');
                            captureLocationAndIP();
                        }, 500);
                    };

                    reader.readAsDataURL(blob);
                }, 'image/jpeg', 0.95);
            });

            retakeSelfieBtnEl.addEventListener('click', async function () {
                try {
                    selfieStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
                    });

                    selfieVideoEl.srcObject = selfieStream;
                    await selfieVideoEl.play();

                    selfieVideoEl.style.display = 'block';
                    selfiePreviewEl.style.display = 'none';
                    selfiePlaceholderEl.style.display = 'none';
                    takeSelfieBtnEl.style.display = 'block';
                    retakeSelfieBtnEl.style.display = 'none';
                    showStatus('Camera restarted. You can take another photo.', 'success');
                } catch (error) {
                    showStatus('Camera access error.', 'error');
                }
            });

            function captureLocationAndIP() {
                getIPAddress();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            state.latitude = position.coords.latitude;
                            state.longitude = position.coords.longitude;

                            document.getElementById('location-lat').textContent = state.latitude.toFixed(6);
                            document.getElementById('location-lng').textContent = state.longitude.toFixed(6);
                            document.getElementById('location-time').textContent = new Date().toLocaleTimeString();

                            setStepComplete(step3Step, step3Icon);
                            showStatus('Location and IP detected. You are ready to submit.', 'success');
                            enableSubmitButton();
                        },
                        error => {
                            state.latitude = null;
                            state.longitude = null;
                            document.getElementById('location-lat').textContent = 'Permission denied';
                            document.getElementById('location-lng').textContent = 'Permission denied';
                            step3Icon.innerHTML = '<i class="bi bi-exclamation-lg"></i>';
                            step3Step.classList.remove('is-active', 'is-complete');
                            step3Step.classList.add('is-warning');
                            showStatus('Location permission was denied, but you can still submit.', 'warning');
                            enableSubmitButton();
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                } else {
                    showStatus('Geolocation is not supported on this device.', 'error');
                }
            }

            function getIPAddress() {
                fetch('https://api.ipify.org?format=json')
                    .then(response => response.json())
                    .then(data => {
                        state.ipAddress = data.ip;
                        document.getElementById('location-ip').textContent = data.ip;
                    })
                    .catch(() => {
                        document.getElementById('location-ip').textContent = 'Unable to detect';
                    });
            }

            function enableSubmitButton() {
                submitBtnEl.style.display = 'block';
            }

            submitBtnEl.addEventListener('click', function () {
                if (!state.qrCode) {
                    showStatus('QR code is missing.', 'error');
                    return;
                }

                if (!state.selfieBlob) {
                    showStatus('Selfie is missing.', 'error');
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
                formData.append('mode', isCheckoutPage ? 'checkout' : 'checkin');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("qrcode.scan") }}', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (isCheckoutPage && data.data && data.data.action === 'checkout') {
                                showStatus('Checkout saved successfully. Redirecting to Dashboard...', 'success');
                                setTimeout(() => {
                                    window.location.href = '{{ route("dashboard") }}';
                                }, 1800);
                                return;
                            }

                            showStatus('Attendance saved successfully. Reloading the page...', 'success');
                            setTimeout(() => location.reload(), 2200);
                        } else {
                            showStatus(data.message || 'Failed to save attendance.', 'error');
                            submitBtnEl.disabled = false;
                        }
                    })
                    .catch(error => {
                        showStatus('Error: ' + error.message, 'error');
                        submitBtnEl.disabled = false;
                    });
            }

            function showStatus(message, type) {
                const colors = {
                    success: ['#dcfce7', '#166534'],
                    error: ['#fee2e2', '#991b1b'],
                    warning: ['#fef3c7', '#92400e'],
                    info: ['#ffedd5', '#9a3412']
                };

                const palette = colors[type] || colors.info;
                statusMessage.innerHTML = message;
                statusMessage.style.background = palette[0];
                statusMessage.style.color = palette[1];
                statusMessage.style.display = 'block';

                if (type === 'info') {
                    setTimeout(() => {
                        if (statusMessage.style.display === 'block') {
                            statusMessage.style.display = 'none';
                        }
                    }, 5000);
                }
            }
        </script>
    @endif
@endsection

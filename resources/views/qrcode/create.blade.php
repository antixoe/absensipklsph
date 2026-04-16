@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Generate QR Codes</h1>
        <p>Create new attendance QR codes in bulk</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form method="POST" action="{{ route('qrcode.store') }}">
            @csrf

            <!-- Date & Time Selection -->
            <div style="margin-bottom: 25px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Attendance Date <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="qr_date" required
                               value="{{ old('qr_date', now()->format('Y-m-d')) }}"
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        @error('qr_date')
                            <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Attendance Time <span style="color: #ef4444;">*</span></label>
                        <input type="time" name="qr_time" required
                               value="{{ old('qr_time', now()->format('H:i')) }}"
                               style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                        @error('qr_time')
                            <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Number of QR Codes <span style="color: #ef4444;">*</span></label>
                <input type="number" name="quantity" required min="1" max="100"
                       value="{{ old('quantity', 1) }}"
                       style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <small style="color: #666; display: block; margin-top: 5px;">Create 1-100 codes at once</small>
                @error('quantity')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Expiration Date -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Expiration Date (Optional)</label>
                <input type="date" name="expires_at"
                       value="{{ old('expires_at') }}"
                       style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <small style="color: #666; display: block; margin-top: 5px;">Leave empty if no expiration</small>
                @error('expires_at')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Notes -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Notes (Optional)</label>
                <textarea name="notes" placeholder="e.g., For Class A, Period 1"
                          style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit; min-height: 80px; resize: vertical;">{{ old('notes') }}</textarea>
                @error('notes')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Info Box -->
            <div style="padding: 15px; background: #e0f2fe; border: 2px solid #0284c7; border-radius: 6px; margin-bottom: 25px; color: #0c4a6e; font-size: 14px;">
                <i class="bi bi-info-circle" style="margin-right: 8px;"></i>
                <strong>Tip:</strong> QR codes are unique and reusable. Multiple students can scan the same code on the same day.
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="padding: 12px 30px; flex: 1;">
                    <i class="bi bi-check-lg" style="margin-right: 8px;"></i>Generate QR Codes
                </button>
                <a href="{{ route('qrcode.index') }}" class="btn btn-secondary" style="padding: 12px 30px;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

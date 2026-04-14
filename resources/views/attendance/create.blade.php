@extends('layouts.app')


@section('content')
    <div class="page-header">
        <h1><i class="bi bi-geo-alt" style="margin-right: 8px;"></i>Create Attendance Record</h1>
        <p>Record your attendance</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form method="POST" action="{{ route('attendance.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <div>
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600;">Date <span style="color: #dc2626;">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('date') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="check_in_time" style="display: block; margin-bottom: 8px; font-weight: 600;">Check In Time <span style="color: #dc2626;">*</span></label>
                <input type="time" id="check_in_time" name="check_in_time" value="{{ old('check_in_time') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('check_in_time') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="check_out_time" style="display: block; margin-bottom: 8px; font-weight: 600;">Check Out Time</label>
                <input type="time" id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('check_out_time') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600;">Status <span style="color: #dc2626;">*</span></label>
                <select id="status" name="status" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                    <option value="">Select Status</option>
                    <option value="present" {{ old('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="sick" {{ old('status') === 'sick' ? 'selected' : '' }}>Sick</option>
                    <option value="permission" {{ old('status') === 'permission' ? 'selected' : '' }}>Permission</option>
                </select>
                @error('status') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="latitude" style="display: block; margin-bottom: 8px; font-weight: 600;">Latitude (GPS)</label>
                <input type="number" id="latitude" name="latitude" value="{{ old('latitude') }}" step="0.0001" placeholder="-6.2088" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('latitude') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="longitude" style="display: block; margin-bottom: 8px; font-weight: 600;">Longitude (GPS)</label>
                <input type="number" id="longitude" name="longitude" value="{{ old('longitude') }}" step="0.0001" placeholder="106.8456" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('longitude') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="notes" style="display: block; margin-bottom: 8px; font-weight: 600;">Notes</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Add any notes..." style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('notes') }}</textarea>
                @error('notes') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Save Record</button>
                <a href="{{ route('attendance.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Attendance Record')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-geo-alt" style="margin-right: 8px;"></i>Attendance Record</h1>
        <p>{{ $attendance->attendance_date->format('F d, Y') }}</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Date</p>
                <p style="font-weight: 600; font-size: 18px;">{{ $attendance->attendance_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Status</p>
                <p style="font-weight: 600; font-size: 18px; color: 
                    @if ($attendance->status === 'present') #10b981 @elseif ($attendance->status === 'absent') #dc2626 @else #f59e0b @endif;">
                    {{ ucfirst($attendance->status) }}
                </p>
            </div>
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Check In Time</p>
                <p style="font-weight: 600; font-size: 18px;">{{ $attendance->check_in_time }}</p>
            </div>
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Check Out Time</p>
                <p style="font-weight: 600; font-size: 18px;">{{ $attendance->check_out_time ?? '-' }}</p>
            </div>
        </div>

        @if ($attendance->check_in_latitude && $attendance->check_in_longitude)
            <div style="margin-bottom: 20px; padding: 15px; background: #f0f9ff; border-radius: 6px;">
                <p style="color: #666; font-size: 14px; margin-bottom: 8px;">GPS Location</p>
                <p style="font-weight: 600;"><i class="bi bi-geo-alt" style="margin-right: 5px;"></i>{{ $attendance->check_in_latitude }}, {{ $attendance->check_in_longitude }}</p>
            </div>
        @endif

        @if ($attendance->notes)
            <div style="margin-bottom: 20px; padding: 15px; background: #fef3c7; border-radius: 6px;">
                <p style="color: #666; font-size: 14px; margin-bottom: 8px;">Notes</p>
                <p>{{ $attendance->notes }}</p>
            </div>
        @endif

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <a href="{{ route('attendance.edit', $attendance) }}" class="btn" style="flex: 1; text-align: center;">Edit</a>
            <form method="POST" action="{{ route('attendance.destroy', $attendance) }}" style="flex: 1;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Delete</button>
            </form>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Back to List</a>
        </div>
    </div>
@endsection

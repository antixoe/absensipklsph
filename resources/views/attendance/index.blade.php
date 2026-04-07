@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-geo-alt" style="margin-right: 8px;"></i>Attendance Management</h1>
        <p>Manage your attendance records</p>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">All Records ({{ $attendances->total() }})</div>
            <a href="{{ route('attendance.create') }}" class="btn">+ New Record</a>
        </div>

        @if ($attendances->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left;">Date</th>
                            <th style="padding: 12px; text-align: left;">Check In</th>
                            <th style="padding: 12px; text-align: left;">Check Out</th>
                            <th style="padding: 12px; text-align: left;">Status</th>
                            <th style="padding: 12px; text-align: left;">GPS</th>
                            <th style="padding: 12px; text-align: left;">Notes</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $record)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px;">{{ $record->attendance_date->format('M d, Y') }}</td>
                                <td style="padding: 12px;">{{ $record->check_in_time }}</td>
                                <td style="padding: 12px;">{{ $record->check_out_time ?? '-' }}</td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                        background: {{ $record->status === 'present' ? '#dcfce7' : ($record->status === 'absent' ? '#fee2e2' : '#fef3c7') }};
                                        color: {{ $record->status === 'present' ? '#166534' : ($record->status === 'absent' ? '#991b1b' : '#92400e') }};">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </td>
                                <td style="padding: 12px;">
                                    @if ($record->check_in_latitude && $record->check_in_longitude)
                                        <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding: 12px; max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $record->notes ? substr($record->notes, 0, 40) . '...' : '-' }}
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="{{ route('attendance.show', $record) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">View</a>
                                    <a href="{{ route('attendance.edit', $record) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                    <form method="POST" action="{{ route('attendance.destroy', $record) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; font-size: 12px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                {{ $attendances->render() }}
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #999;">No attendance records found. <a href="{{ route('attendance.create') }}">Create one now</a></p>
        @endif
    </div>
@endsection

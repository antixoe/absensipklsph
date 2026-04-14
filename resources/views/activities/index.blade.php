@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>Activities Management</h1>
        <p>Track your assigned tasks and activities</p>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">All Activities ({{ $activities->total() }})</div>
            <a href="{{ route('activities.create') }}" class="btn">+ New Activity</a>
        </div>

        @if ($activities->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left;">Activity</th>
                            <th style="padding: 12px; text-align: left;">Category</th>
                            <th style="padding: 12px; text-align: left;">Date</th>
                            <th style="padding: 12px; text-align: left;">Duration</th>
                            <th style="padding: 12px; text-align: left;">Status</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 12px; font-weight: 500;">{{ $activity->name }}</td>
                                <td style="padding: 12px;">{{ $activity->category }}</td>
                                <td style="padding: 12px;">{{ $activity->activity_date->format('M d, Y') }}</td>
                                <td style="padding: 12px;">{{ $activity->duration_hours }} hrs</td>
                                <td style="padding: 12px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;
                                        background: {{ $activity->status === 'completed' ? '#dcfce7' : '#fef3c7' }};
                                        color: {{ $activity->status === 'completed' ? '#166534' : '#92400e' }};">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="{{ route('activities.show', $activity) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">View</a>
                                    <a href="{{ route('activities.edit', $activity) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                    @if ($activity->status === 'pending')
                                        <form method="POST" action="{{ route('activities.complete', $activity) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" style="padding: 6px 12px; font-size: 12px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer;">Complete</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('activities.destroy', $activity) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
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
                {{ $activities->render() }}
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #999;">No activities found. <a href="{{ route('activities.create') }}">Create one now</a></p>
        @endif
    </div>
@endsection

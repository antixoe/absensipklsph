@extends('layouts.app')

@section('title', 'Activity Details')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>{{ $activity->activity_name }}</h1>
        <p>{{ $activity->category }} • {{ $activity->activity_date->format('F d, Y') }}</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Status</p>
                <p style="font-weight: 600; font-size: 18px; color: 
                    @if ($activity->status === 'completed') #10b981 @else #f59e0b @endif;">
                    {{ ucfirst($activity->status) }}
                </p>
            </div>
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Duration</p>
                <p style="font-weight: 600; font-size: 18px;">{{ $activity->duration_hours }} hours</p>
            </div>
        </div>

        <div style="margin-bottom: 20px; padding: 15px; background: #f0f9ff; border-radius: 6px;">
            <p style="color: #666; font-size: 14px; margin-bottom: 8px;">Description</p>
            <p style="line-height: 1.6;">{{ $activity->description }}</p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <a href="{{ route('activities.edit', $activity) }}" class="btn" style="flex: 1; text-align: center;">Edit</a>
            @if ($activity->status === 'pending')
                <form method="POST" action="{{ route('activities.complete', $activity) }}" style="flex: 1;">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Mark as Completed</button>
                </form>
            @endif
            <form method="POST" action="{{ route('activities.destroy', $activity) }}" style="flex: 1;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Delete</button>
            </form>
            <a href="{{ route('activities.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Back to List</a>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Activity')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Activity</h1>
        <p>Update the activity</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form method="POST" action="{{ route('activities.update', $activity) }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            @method('PUT')

            <div>
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">Activity Name <span style="color: #dc2626;">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $activity->activity_name) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('name') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">Description <span style="color: #dc2626;">*</span></label>
                <textarea id="description" name="description" rows="4" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('description', $activity->description) }}</textarea>
                @error('description') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="category" style="display: block; margin-bottom: 8px; font-weight: 600;">Category <span style="color: #dc2626;">*</span></label>
                <input type="text" id="category" name="category" value="{{ old('category', $activity->category) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('category') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600;">Date <span style="color: #dc2626;">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date', $activity->activity_date->format('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('date') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="duration_hours" style="display: block; margin-bottom: 8px; font-weight: 600;">Duration (Hours) <span style="color: #dc2626;">*</span></label>
                <input type="number" id="duration_hours" name="duration_hours" min="0" max="24" step="0.5" value="{{ old('duration_hours', $activity->duration_hours) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('duration_hours') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Update Activity</button>
                <a href="{{ route('activities.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

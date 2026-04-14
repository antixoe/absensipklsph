@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Create Activity</h1>
        <p>Add a new task</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form method="POST" action="{{ route('activities.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <div>
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">Activity Name <span style="color: #dc2626;">*</span></label>
                <input type="text" id="name" name="name" placeholder="Enter activity name" value="{{ old('name') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('name') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">Description <span style="color: #dc2626;">*</span></label>
                <textarea id="description" name="description" rows="4" placeholder="Describe the activity..." required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('description') }}</textarea>
                @error('description') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="category" style="display: block; margin-bottom: 8px; font-weight: 600;">Category <span style="color: #dc2626;">*</span></label>
                <input type="text" id="category" name="category" placeholder="e.g., Development, Training, Meeting" value="{{ old('category') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('category') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600;">Date <span style="color: #dc2626;">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('date') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="duration_hours" style="display: block; margin-bottom: 8px; font-weight: 600;">Duration (Hours) <span style="color: #dc2626;">*</span></label>
                <input type="number" id="duration_hours" name="duration_hours" min="0" max="24" step="0.5" value="{{ old('duration_hours') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('duration_hours') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Create Activity</button>
                <a href="{{ route('activities.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

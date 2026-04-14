@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-journal-text" style="margin-right: 8px;"></i>Edit Logbook Entry</h1>
        <p>Update your entry</p>
    </div>

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form method="POST" action="{{ route('logbook.update', $entry) }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            @method('PUT')

            <div>
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600;">Date <span style="color: #dc2626;">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date', $entry->entry_date->format('Y-m-d')) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('date') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600;">Title <span style="color: #dc2626;">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $entry->title) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('title') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">Description <span style="color: #dc2626;">*</span></label>
                <textarea id="description" name="description" rows="6" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('description', $entry->description) }}</textarea>
                @error('description') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="achievements" style="display: block; margin-bottom: 8px; font-weight: 600;">Achievements/Accomplishments</label>
                <textarea id="achievements" name="achievements" rows="4" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('achievements', $entry->achievements) }}</textarea>
                @error('achievements') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="challenges" style="display: block; margin-bottom: 8px; font-weight: 600;">Challenges Faced</label>
                <textarea id="challenges" name="challenges" rows="4" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('challenges', $entry->challenges) }}</textarea>
                @error('challenges') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="learning_outcomes" style="display: block; margin-bottom: 8px; font-weight: 600;">Learning Outcomes</label>
                <textarea id="learning_outcomes" name="learning_outcomes" rows="4" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('learning_outcomes', $entry->learning_outcomes) }}</textarea>
                @error('learning_outcomes') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="hours_worked" style="display: block; margin-bottom: 8px; font-weight: 600;">Hours Worked <span style="color: #dc2626;">*</span></label>
                <input type="number" id="hours_worked" name="hours_worked" min="0" max="24" step="0.5" value="{{ old('hours_worked', $entry->hours_worked) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('hours_worked') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Update Entry</button>
                <a href="{{ route('logbook.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

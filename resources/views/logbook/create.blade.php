@extends('layouts.app')

@section('title', 'Create Logbook Entry')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-journal-text" style="margin-right: 8px;"></i>Create Logbook Entry</h1>
        <p>Write about your daily activities</p>
    </div>

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form method="POST" action="{{ route('logbook.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <div>
                <label for="date" style="display: block; margin-bottom: 8px; font-weight: 600;">Date <span style="color: #dc2626;">*</span></label>
                <input type="date" id="date" name="date" value="{{ old('date') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('date') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="title" style="display: block; margin-bottom: 8px; font-weight: 600;">Title <span style="color: #dc2626;">*</span></label>
                <input type="text" id="title" name="title" placeholder="What did you do today?" value="{{ old('title') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('title') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600;">Description <span style="color: #dc2626;">*</span></label>
                <textarea id="description" name="description" rows="6" placeholder="Write a detailed description of your activities..." required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('description') }}</textarea>
                @error('description') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="achievements" style="display: block; margin-bottom: 8px; font-weight: 600;">Achievements/Accomplishments</label>
                <textarea id="achievements" name="achievements" rows="4" placeholder="What did you achieve?" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('achievements') }}</textarea>
                @error('achievements') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="challenges" style="display: block; margin-bottom: 8px; font-weight: 600;">Challenges Faced</label>
                <textarea id="challenges" name="challenges" rows="4" placeholder="What challenges did you face?" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('challenges') }}</textarea>
                @error('challenges') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="learning_outcomes" style="display: block; margin-bottom: 8px; font-weight: 600;">Learning Outcomes</label>
                <textarea id="learning_outcomes" name="learning_outcomes" rows="4" placeholder="What did you learn?" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit;">{{ old('learning_outcomes') }}</textarea>
                @error('learning_outcomes') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="hours_worked" style="display: block; margin-bottom: 8px; font-weight: 600;">Hours Worked <span style="color: #dc2626;">*</span></label>
                <input type="number" id="hours_worked" name="hours_worked" min="0" max="24" step="0.5" value="{{ old('hours_worked') }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px;">
                @error('hours_worked') <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn" style="flex: 1;">Save as Draft</button>
                <a href="{{ route('logbook.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

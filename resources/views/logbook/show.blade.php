@extends('layouts.app')

@section('title', 'Logbook Entry')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-journal-text" style="margin-right: 8px;"></i>{{ $entry->title }}</h1>
        <p>{{ $entry->entry_date->format('F d, Y') }} • {{ $entry->hours_worked }} hours • {{ ucfirst($entry->status) }}</p>
    </div>

    <div class="card" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 30px; padding: 15px; background: #f0f9ff; border-radius: 6px;">
            <h3 style="margin-bottom: 15px; color: #333;">Description</h3>
            <p style="line-height: 1.6; color: #666;">{{ $entry->description }}</p>
        </div>

        @if ($entry->achievements)
            <div style="margin-bottom: 20px; padding: 15px; background: #dcfce7; border-radius: 6px;">
                <h3 style="margin-bottom: 10px; color: #166534;"><i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>Achievements</h3>
                <p style="line-height: 1.6; color: #166534;">{{ $entry->achievements }}</p>
            </div>
        @endif

        @if ($entry->challenges)
            <div style="margin-bottom: 20px; padding: 15px; background: #fee2e2; border-radius: 6px;">
                <h3 style="margin-bottom: 10px; color: #991b1b;">⚠ Challenges</h3>
                <p style="line-height: 1.6; color: #991b1b;">{{ $entry->challenges }}</p>
            </div>
        @endif

        @if ($entry->learning_outcomes)
            <div style="margin-bottom: 20px; padding: 15px; background: #fef3c7; border-radius: 6px;">
                <h3 style="margin-bottom: 10px; color: #92400e;">💡 Learning Outcomes</h3>
                <p style="line-height: 1.6; color: #92400e;">{{ $entry->learning_outcomes }}</p>
            </div>
        @endif

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            @if ($entry->status === 'draft')
                <a href="{{ route('logbook.edit', $entry) }}" class="btn" style="flex: 1; text-align: center;">Edit</a>
                <form method="POST" action="{{ route('logbook.submit', $entry) }}" style="flex: 1;">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Submit for Approval</button>
                </form>
            @endif
            <form method="POST" action="{{ route('logbook.destroy', $entry) }}" style="flex: 1;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Delete</button>
            </form>
            <a href="{{ route('logbook.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Back to List</a>
        </div>
    </div>
@endsection

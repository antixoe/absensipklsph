@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-journal-text" style="margin-right: 8px;"></i>Logbook Entries</h1>
        <p>Manage your daily logbook entries</p>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="card-title">All Entries ({{ $entries->total() }})</div>
            <a href="{{ route('logbook.create') }}" class="btn">+ New Entry</a>
        </div>

        @if ($entries->count() > 0)
            <div style="display: grid; gap: 15px;">
                @foreach ($entries as $entry)
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h3 style="margin-bottom: 5px; color: #333;">{{ $entry->title }}</h3>
                                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">{{ $entry->entry_date->format('M d, Y') }} • {{ $entry->hours_worked }} hours</p>
                                <p style="color: #666; margin-bottom: 10px;">{{ substr($entry->description, 0, 100) }}...</p>
                                <span style="display: inline-block; padding: 4px 8px; background: 
                                    @if ($entry->status === 'draft') #e5e7eb @elseif ($entry->status === 'submitted') #fef3c7 @else #dcfce7 @endif;
                                    color: @if ($entry->status === 'draft') #374151 @elseif ($entry->status === 'submitted') #92400e @else #166534 @endif;
                                    border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px; margin-left: 15px;">
                                <a href="{{ route('logbook.show', $entry) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">View</a>
                                @if ($entry->status === 'draft')
                                    <a href="{{ route('logbook.edit', $entry) }}" class="btn" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                @endif
                                @if ($entry->status === 'draft')
                                    <form method="POST" action="{{ route('logbook.submit', $entry) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" style="padding: 6px 12px; font-size: 12px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer;">Submit</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('logbook.destroy', $entry) }}" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="padding: 6px 12px; font-size: 12px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer;">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                {{ $entries->render() }}
            </div>
        @else
            <p style="text-align: center; padding: 40px; color: #999;">No logbook entries found. <a href="{{ route('logbook.create') }}">Create your first entry</a></p>
        @endif
    </div>
@endsection

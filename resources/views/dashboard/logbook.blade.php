@extends('layouts.app')

@section('title', 'Logbook')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-journal-text" style="margin-right: 8px;"></i>Logbook</h1>
        <p>Daily activity journal</p>
    </div>

    <div class="card">
        <div class="card-title">Create New Entry</div>
        <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
            @csrf
            <div>
                <label>Date</label>
                <input type="date" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label>Title</label>
                <input type="text" placeholder="What did you do today?" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label>Description</label>
                <textarea placeholder="Write your activity..." rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit;"></textarea>
            </div>
            <button type="submit" class="btn">Save Entry</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title">Previous Entries</div>
        <p>Your logbook entries will appear here...</p>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-hand-thumbs-up" style="margin-right: 8px;"></i>Welcome, {{ $user->name }}!</h1>
        <p>Student Internship Attendance & Logbook System</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Today's Attendance</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Total Hours</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Logbook Entries</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">0</div>
            <div class="stat-label">Pending Tasks</div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">User Information</div>
        <p><strong>Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Role:</strong> {{ ucfirst($role) }}</p>
    </div>
@endsection

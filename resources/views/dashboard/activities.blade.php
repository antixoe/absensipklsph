@extends('layouts.app')

@section('title', 'Activities')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>Activities</h1>
        <p>Track your assigned tasks</p>
    </div>

    <div class="card">
        <div class="card-title">Assigned Tasks</div>
        <p>Your assigned activities will appear here...</p>
    </div>

    <div class="card">
        <div class="card-title">Completed Tasks</div>
        <p>Your completed tasks will appear here...</p>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-geo-alt" style="margin-right: 8px;"></i>Attendance</h1>
        <p>Manage your attendance records</p>
    </div>

    <div class="card">
        <div class="card-title">Check In / Check Out</div>
        <div class="button-group">
            <button class="btn" onclick="checkIn()"><i class="bi bi-check-circle" style="margin-right: 5px;"></i>Check In</button>
            <button class="btn btn-secondary" onclick="checkOut()"><i class="bi bi-x-circle" style="margin-right: 5px;"></i>Check Out</button>
        </div>
        <div id="status" style="margin-top: 20px;"></div>
    </div>

    <div class="card">
        <div class="card-title">Attendance History</div>
        <p>Your attendance records will appear here...</p>
    </div>

    <script>
        function checkIn() {
            document.getElementById('status').innerHTML = '<p style="color: green;"><strong><i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>Checked in successfully at ' + new Date().toLocaleTimeString() + '</strong></p>';
        }

        function checkOut() {
            document.getElementById('status').innerHTML = '<p style="color: orange;"><strong><i class="bi bi-x-circle-fill" style="margin-right: 5px;"></i>Checked out successfully at ' + new Date().toLocaleTimeString() + '</strong></p>';
        }
    </script>
@endsection

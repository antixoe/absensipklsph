@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Attendance Record</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">View your complete attendance history</p>
    </div>

    <!-- Student Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Full Name</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ Auth::user()->name }}
                </p>
            </div>
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">NIM / Student ID</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $currentUserStudent->nim ?? 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-gray-600 dark:text-gray-400 text-sm">Class</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $currentUserStudent->class_name ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Today's Status (if available) -->
    @if($todayAbsence)
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-green-800 dark:text-green-200 font-semibold">Today's Attendance</p>
                <p class="text-green-700 dark:text-green-300 text-sm">
                    Check-in at {{ $todayAbsence->scanned_qr_at?->format('H:i:s') ?? $todayAbsence->absence_date->format('H:i:s') }}
                    <span class="ml-2 inline-block px-2 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs font-semibold rounded">
                        {{ ucfirst($todayAbsence->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-blue-800 dark:text-blue-200 font-semibold">No attendance today</p>
                <p class="text-blue-700 dark:text-blue-300 text-sm">You have not checked in today yet. Teachers will scan your QR code to record your attendance.</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Attendance Records Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Attendance History</h2>
        </div>

        <div class="overflow-x-auto">
            @if($attendanceRecords->count() > 0)
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Check-in Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Submitted By
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($attendanceRecords as $record)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $record->absence_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-700 dark:text-gray-300">
                                @if($record->scanned_qr_at)
                                    {{ $record->scanned_qr_at->format('H:i:s') }}
                                @elseif($record->absence_time)
                                    {{ $record->absence_time->format('H:i:s') }}
                                @else
                                    {{ $record->absence_date->format('H:i:s') }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold
                                @if($record->status === 'approved')
                                    bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200
                                @elseif($record->status === 'pending')
                                    bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200
                                @else
                                    bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200
                                @endif
                            ">
                                {{ ucfirst($record->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700 dark:text-gray-300">
                                {{ $record->location_name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-700 dark:text-gray-300 text-sm">
                                @if($record->scanned_qr_at)
                                    <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded text-xs font-semibold">
                                        QR Code
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 rounded text-xs font-semibold">
                                        Manual
                                    </span>
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $attendanceRecords->render() }}
            </div>
            @else
            <div class="px-6 py-8 text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-600 dark:text-gray-400">No attendance records yet.</p>
                <p class="text-gray-500 dark:text-gray-500 text-sm mt-1">Your attendance will appear here when teachers scan your QR code.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <h3 class="text-blue-900 dark:text-blue-100 font-semibold mb-2">About Your Attendance</h3>
        <ul class="text-blue-800 dark:text-blue-200 text-sm space-y-1">
            <li>✓ Your QR code is printed on your student ID card</li>
            <li>✓ Teachers scan your QR code to record your attendance</li>
            <li>✓ You cannot manually mark your own attendance</li>
            <li>✓ This is a read-only view of your attendance records</li>
        </ul>
    </div>
</div>

<style>
    /* Dark mode support for pagination */
    .dark .pagination {
        --tw-bg-opacity: 1;
        background-color: rgba(31, 41, 55, var(--tw-bg-opacity));
    }
    
    .dark .pagination a {
        --tw-text-opacity: 1;
        color: rgba(209, 213, 219, var(--tw-text-opacity));
    }
    
    .dark .pagination .active {
        --tw-bg-opacity: 1;
        background-color: rgba(75, 85, 99, var(--tw-bg-opacity));
    }
</style>
@endsection

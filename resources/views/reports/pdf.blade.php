<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence Report - PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .page {
            max-width: 1000px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #f97316;
            padding-bottom: 20px;
        }

        .header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .period {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin: 30px 0 15px 0;
            border-bottom: 2px solid #f97316;
            padding-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            padding: 20px;
            background-color: #f9fafb;
            border-left: 4px solid #f97316;
            border-radius: 4px;
        }

        .stat-label {
            color: #666;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #f97316;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        thead {
            background-color: #f9fafb;
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #999;
            font-size: 11px;
        }

        @page {
            margin: 0.5in;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <h1>Absence Report</h1>
            <p>{{ config('app.name', 'Internship Management System') }}</p>
        </div>

        <div class="period">
            <strong>Period:</strong> {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
        </div>

        <!-- Summary Statistics -->
        <div class="section-title">Summary Statistics</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Total Absences</div>
                <div class="stat-value">{{ $absenceStats['total'] }}</div>
            </div>
            <div class="stat-box" style="border-left-color: #10b981;">
                <div class="stat-label">Approved</div>
                <div class="stat-value" style="color: #10b981;">{{ $absenceStats['approved'] }}</div>
            </div>
            <div class="stat-box" style="border-left-color: #f59e0b;">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color: #f59e0b;">{{ $absenceStats['pending'] }}</div>
            </div>
            <div class="stat-box" style="border-left-color: #ef4444;">
                <div class="stat-label">Rejected</div>
                <div class="stat-value" style="color: #ef4444;">{{ $absenceStats['rejected'] }}</div>
            </div>
        </div>

        <!-- Detailed Records -->
        <div class="section-title">Detailed Absence Records</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absences as $absence)
                    <tr>
                        <td>{{ $absence->absence_date->format('M d, Y') }}</td>
                        <td><strong>{{ $absence->student->user->name ?? 'N/A' }}</strong></td>
                        <td>{{ $absence->student->user->email ?? 'N/A' }}</td>
                        <td>{{ $absence->reason ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($absence->status) }}">
                                {{ ucfirst($absence->status) }}
                            </span>
                        </td>
                        <td>{{ $absence->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #999; padding: 20px;">
                            No absence records found for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Report generated on {{ now()->format('M d, Y \a\t H:i') }}</p>
            <p>This is an automated report. Please contact the administrator for any inquiries.</p>
        </div>
    </div>
</body>
</html>

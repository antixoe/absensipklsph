@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-qr-code" style="margin-right: 8px;"></i>QR Code Details</h1>
        <p>View scan history and details</p>
        <div style="margin-top: 12px;">
            <a href="{{ route('qrcode.index') }}" class="btn" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-arrow-left"></i>Back to QR Codes
            </a>
        </div>
    </div>

    <!-- QR Code Info -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px;">QR CODE</div>
            <code style="background: #f5f5f5; padding: 8px 12px; border-radius: 4px; font-family: monospace; font-size: 14px; font-weight: 600;">{{ $qrCode->code }}</code>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px;">DATE & TIME</div>
            <div style="font-size: 18px; font-weight: 600; color: #222;">{{ $qrCode->qr_date->format('M d, Y \\a\\t H:i') }}</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px;">TOTAL SCANS</div>
            <div style="font-size: 28px; font-weight: 700; color: #3b82f6;">{{ $scanHistory->total() }}</div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 14px; color: #666; margin-bottom: 10px;">STATUS</div>
            @if($qrCode->status === 'active' && $qrCode->isActive())
                <span style="display: inline-block; padding: 6px 12px; background: #dcfce7; color: #166534; border-radius: 20px; font-weight: 600;">
                    <i class="bi bi-check-circle-fill"></i> Active
                </span>
            @elseif($qrCode->status === 'expired')
                <span style="display: inline-block; padding: 6px 12px; background: #fee2e2; color: #991b1b; border-radius: 20px; font-weight: 600;">
                    <i class="bi bi-clock-history"></i> Expired
                </span>
            @else
                <span style="display: inline-block; padding: 6px 12px; background: #f5f5f5; color: #666; border-radius: 20px; font-weight: 600;">
                    <i class="bi bi-x-circle-fill"></i> Disabled
                </span>
            @endif
        </div>
    </div>

    <!-- Download QR Image -->
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
        <h3 style="margin-bottom: 15px; font-weight: 600;">QR Code Image</h3>
        <div style="text-align: center; padding: 30px; background: #f5f5f5; border-radius: 6px;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrCode->code) }}" 
                 alt="QR Code" style="max-width: 300px; height: auto;">
            <div style="margin-top: 20px;">
                <a href="{{ route('qrcode.download', $qrCode) }}" class="btn">
                    <i class="bi bi-download" style="margin-right: 8px;"></i>Download QR Image
                </a>
            </div>
        </div>
    </div>

    <!-- Scan History -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-weight: 600;">
            <i class="bi bi-clock" style="margin-right: 8px;"></i>Scan History ({{ $scanHistory->total() }} scans)
        </h3>

        @if($scanHistory->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Student Name</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">NIM</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Scanned At</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($scanHistory as $scan)
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.3s;">
                                <td style="padding: 12px;">
                                    <strong>{{ $scan->student->user->name }}</strong>
                                </td>
                                <td style="padding: 12px;">
                                    <small style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px;">{{ $scan->student->nim }}</small>
                                </td>
                                <td style="padding: 12px;">{{ $scan->scanned_qr_at->format('M d, Y H:i:s') }}</td>
                                <td style="padding: 12px;">
                                    <small style="color: #666;">{{ $scan->location_name ?? '—' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($scanHistory->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $scanHistory->links() }}
                </div>
            @endif
        @else
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                <p style="font-size: 16px; margin: 10px 0;">No scans yet</p>
                <p style="font-size: 14px; color: #999;">Students who scan this code will appear here</p>
            </div>
        @endif
    </div>
@endsection

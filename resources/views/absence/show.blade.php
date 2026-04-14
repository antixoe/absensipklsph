@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-clock-history" style="margin-right: 8px;"></i>Absence History</h1>
        <p>{{ $student->user->name }} (NIM: {{ $student->nim }})</p>
    </div>

    <div class="card">
        <div class="card-title">Absence Records</div>
        
        @if($absences->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Date</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">IP Address</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Coordinates</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.3s;">
                                <td style="padding: 12px;">{{ $absence->absence_date->format('M d, Y') }}</td>
                                <td style="padding: 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; 
                                                 font-size: 12px; font-weight: 600;
                                                 @if($absence->status === 'approved')
                                                    background: #d1fae5; color: #065f46;
                                                 @elseif($absence->status === 'rejected')
                                                    background: #fee2e2; color: #7f1d1d;
                                                 @else
                                                    background: #fef3c7; color: #78350f;
                                                 @endif">
                                        {{ ucfirst($absence->status) }}
                                    </span>
                                </td>
                                <td style="padding: 12px;">{{ $absence->ip_address }}</td>
                                <td style="padding: 12px;">{{ $absence->location_name }}</td>
                                <td style="padding: 12px;">
                                    <small style="word-break: break-all;">
                                        {{ number_format($absence->latitude, 6) }}, 
                                        {{ number_format($absence->longitude, 6) }}
                                    </small>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($absence->selfie_path)
                                        <button type="button" onclick="viewSelfie('{{ asset('storage/' . $absence->selfie_path) }}')" 
                                                class="btn" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="bi bi-image" style="margin-right: 4px;"></i>View
                                        </button>
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                                <td style="padding: 12px;">
                                    <small style="color: #666;">{{ $absence->notes ?? '—' }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 20px; text-align: center; color: #666;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                No absence records found for this student.
            </div>
        @endif
    </div>

    <div style="margin-top: 20px;">
        <a href="{{ route('absence.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left" style="margin-right: 8px;"></i>Back to Absences
        </a>
    </div>

    <!-- Selfie Modal -->
    <div id="selfieModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
                                  background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; padding: 20px; max-width: 600px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Selfie Photo</h2>
                <button type="button" onclick="closeSelfieModal()" style="background: none; border: none; 
                                                                           font-size: 24px; cursor: pointer;">×</button>
            </div>
            <img id="modalSelfie" style="width: 100%; border-radius: 6px;" src="" alt="Selfie">
        </div>
    </div>

    <script>
        function viewSelfie(photoUrl) {
            document.getElementById('modalSelfie').src = photoUrl;
            document.getElementById('selfieModal').style.display = 'flex';
        }

        function closeSelfieModal() {
            document.getElementById('selfieModal').style.display = 'none';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSelfieModal();
            }
        });

        // Close modal on outside click
        document.getElementById('selfieModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSelfieModal();
            }
        });
    </script>
@endsection

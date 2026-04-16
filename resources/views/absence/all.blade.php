@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-list-check" style="margin-right: 8px;"></i>All Student Absences</h1>
        <p>View complete absence records for all students</p>
    </div>

    <!-- Search & Filter Section -->
    <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
        <form method="GET" action="{{ route('absence.all') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: flex-end;">
            <!-- Search Input -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Search by Name or NIM</label>
                <input type="text" name="search" placeholder="Enter student name or NIM..." 
                       value="{{ request('search') }}"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; 
                              font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Status Filter -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Status</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; 
                                           font-size: 14px; background: white;">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; 
                              font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Date To -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; 
                              font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Search Button -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="flex: 1; padding: 10px 16px; background: #f97316; color: white; border: none; 
                                            border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-search"></i> Search
                </button>
                <a href="{{ route('absence.all') }}" style="padding: 10px 16px; background: #6b7280; color: white; 
                                                           border: none; border-radius: 6px; font-weight: 600; 
                                                           cursor: pointer; text-decoration: none; display: flex; 
                                                           align-items: center; font-size: 14px;">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 28px; font-weight: 700; color: #f97316; margin-bottom: 5px;">{{ $totalAbsences }}</div>
            <div style="color: #666; font-size: 14px;">Total Absences</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 28px; font-weight: 700; color: #10b981; margin-bottom: 5px;">{{ $approvedAbsences }}</div>
            <div style="color: #666; font-size: 14px;">Approved</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-bottom: 5px;">{{ $pendingAbsences }}</div>
            <div style="color: #666; font-size: 14px;">Pending</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
            <div style="font-size: 28px; font-weight: 700; color: #ef4444; margin-bottom: 5px;">{{ $rejectedAbsences }}</div>
            <div style="color: #666; font-size: 14px;">Rejected</div>
        </div>
    </div>

    @if($absences->count() > 0)
        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Student Name</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">NIM</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Date & Time</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Location</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Notes</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                            <tr style="border-bottom: 1px solid #eee; transition: background 0.3s;">
                                <td style="padding: 12px;">
                                    <strong>{{ $absence->student->user->name }}</strong>
                                </td>
                                <td style="padding: 12px;">
                                    <small style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px;">{{ $absence->student->nim }}</small>
                                </td>
                                <td style="padding: 12px;">{{ $absence->absence_date->format('M d, Y H:i') }}</td>
                                <td style="padding: 12px;">
                                    <small style="color: #666;">{{ $absence->location_name ?? '—' }}</small>
                                </td>
                                <td style="padding: 12px;">
                                    @if($absence->status === 'approved')
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; 
                                                     background: #dcfce7; color: #166534; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i>Approved
                                        </span>
                                    @elseif($absence->status === 'rejected')
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; 
                                                     background: #fee2e2; color: #991b1b; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i>Rejected
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; 
                                                     background: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-clock-fill" style="margin-right: 4px;"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px;">
                                    <small style="color: #666;">{{ $absence->notes ?? '—' }}</small>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($absence->selfie_path)
                                        <button type="button" onclick="viewPhoto('{{ asset('storage/' . $absence->selfie_path) }}')"
                                                class="btn" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="bi bi-image"></i>
                                        </button>
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $absences->links() }}
            </div>
        </div>
    @else
        <div class="card">
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                <p style="font-size: 18px; margin: 10px 0;">No absence records found</p>
                <a href="{{ route('dashboard') }}" class="btn" style="margin-top: 20px;">Back to Dashboard</a>
            </div>
        </div>
    @endif

    <!-- Photo Modal -->
    <div id="photoModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; 
                                background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; padding: 20px; max-width: 600px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Student Selfie</h2>
                <button type="button" onclick="closePhotoModal()" style="background: none; border: none; 
                                                                         font-size: 24px; cursor: pointer;">×</button>
            </div>
            <img id="modalPhoto" style="width: 100%; border-radius: 6px;" src="" alt="Selfie">
        </div>
    </div>

    <script>
        function viewPhoto(photoUrl) {
            document.getElementById('modalPhoto').src = photoUrl;
            document.getElementById('photoModal').style.display = 'flex';
        }

        function closePhotoModal() {
            document.getElementById('photoModal').style.display = 'none';
        }

        document.getElementById('photoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePhotoModal();
            }
        });

        // Close modal on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection

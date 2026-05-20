@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-clipboard-check" style="margin-right: 8px;"></i>Attendance / Absence Overview</h1>
        <p>
            @if($isWaliKelas)
                Your view is restricted to <strong>{{ $rombelLabel ?? 'your assigned rombel' }}</strong>.
            @else
                Kesiswaan can review every class and filter the data by <strong>Rombongan Belajar</strong>.
            @endif
        </p>
    </div>

    @if($isWaliKelas)
        <div class="card" style="margin-bottom: 20px; background: #eff6ff; border-left: 4px solid #2563eb;">
            <strong>Class scope:</strong>
            @if($rombelLabel)
                You are viewing only students from <strong>{{ $rombelLabel }}</strong>.
            @else
                Your homeroom class is not configured yet, so no class-filtered data can be shown.
            @endif
        </div>
    @endif

    @if($isKesiswaan)
        <div class="card" style="margin-bottom: 20px; background: #f8fafc; border-left: 4px solid #f97316;">
            <form method="GET" action="{{ route('absence.all') }}" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Student name, NIM, or major"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Rombel</label>
                    <select name="rombel_filter" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background: white;">
                        <option value="">All Rombel</option>
                        @foreach($rombelFilters as $filter)
                            <option value="{{ $filter['key'] }}" {{ $selectedGroup === $filter['key'] ? 'selected' : '' }}>
                                {{ $filter['label'] }} ({{ $filter['count'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Status</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background: white;">
                        <option value="">All Status</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div style="display: flex; gap: 8px; align-items: end;">
                    <button type="submit" class="btn" style="padding: 10px 16px;">Filter</button>
                    <a href="{{ route('absence.all') }}" class="btn btn-secondary" style="padding: 10px 16px;">Reset</a>
                </div>
            </form>
        </div>

        @if($selectedGroup)
            <div class="card" style="margin-bottom: 20px; background: #fff7ed; border-left: 4px solid #fb923c;">
                Showing data for <strong>{{ $selectedGroupLabel ?? $selectedGroup }}</strong>.
                <a href="{{ route('absence.all', array_filter([
                    'search' => $search,
                    'status' => $status,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                ], fn ($value) => $value !== null && $value !== '')) }}" style="margin-left: 10px; font-weight: 600; text-decoration: underline;">Clear rombel filter</a>
            </div>
        @endif
    @else
        <div class="card" style="margin-bottom: 20px;">
            <form method="GET" action="{{ route('absence.all') }}" style="display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Search</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Student name, NIM, or major"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Status</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; background: white;">
                        <option value="">All Status</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date From</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Date To</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                </div>
                <div style="display: flex; gap: 8px; align-items: end;">
                    <button type="submit" class="btn" style="padding: 10px 16px;">Filter</button>
                    <a href="{{ route('absence.all') }}" class="btn btn-secondary" style="padding: 10px 16px;">Reset</a>
                </div>
            </form>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div class="card" style="text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #f97316; margin-bottom: 5px;">{{ $summaryStats['total'] }}</div>
            <div style="color: #666; font-size: 14px;">Total Records</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #10b981; margin-bottom: 5px;">{{ $summaryStats['approved'] }}</div>
            <div style="color: #666; font-size: 14px;">Approved</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #f59e0b; margin-bottom: 5px;">{{ $summaryStats['pending'] }}</div>
            <div style="color: #666; font-size: 14px;">Pending</div>
        </div>
        <div class="card" style="text-align: center;">
            <div style="font-size: 28px; font-weight: 700; color: #ef4444; margin-bottom: 5px;">{{ $summaryStats['rejected'] }}</div>
            <div style="color: #666; font-size: 14px;">Rejected</div>
        </div>
    </div>

    @if($isKesiswaan && !$selectedGroup)
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-top: 0; margin-bottom: 16px;">Rombel Overview</h3>
            @if($groupedAbsences->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                    @foreach($groupedAbsences as $group)
                        <a href="{{ route('absence.all', array_filter([
                            'rombel_filter' => $group['key'],
                            'search' => $search,
                            'status' => $status,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                        ], fn ($value) => $value !== null && $value !== '')) }}"
                           style="display: block; padding: 16px; border: 1px solid #eee; border-radius: 10px; text-decoration: none; color: inherit; background: #fff;">
                            <div style="font-weight: 700; margin-bottom: 6px;">{{ $group['label'] }}</div>
                            <div style="font-size: 28px; font-weight: 700; color: #f97316;">{{ $group['count'] }}</div>
                            <div style="font-size: 13px; color: #666; margin-top: 6px;">Approved {{ $group['approved'] }} · Pending {{ $group['pending'] }} · Rejected {{ $group['rejected'] }}</div>
                        </a>
                    @endforeach
                </div>
            @else
                <p style="color: #666; margin: 0;">No grouped data found for the current filters.</p>
            @endif
        </div>

        @foreach($groupedAbsences as $group)
            <details class="card" style="margin-bottom: 14px;" {{ $loop->first ? 'open' : '' }}>
                <summary style="cursor: pointer; font-weight: 700; font-size: 16px;">
                    {{ $group['label'] }} <span style="color: #666; font-weight: 400;">({{ $group['count'] }})</span>
                </summary>
                <div style="margin-top: 16px; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Student</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">NIM</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Rombel</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Check In</th>
                                <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                                <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['records'] as $absence)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px;"><strong>{{ $absence->student->user->name ?? 'N/A' }}</strong></td>
                                    <td style="padding: 12px;">{{ $absence->student->nim ?? 'N/A' }}</td>
                                    <td style="padding: 12px;">{{ $absence->student->rombel->name ?? $absence->student->major ?? '—' }}</td>
                                    <td style="padding: 12px;">{{ optional($absence->scanned_qr_at ?? $absence->absence_date)->format('M d, Y H:i') ?? 'N/A' }}</td>
                                    <td style="padding: 12px;">
                                        <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: {{ $absence->status === 'approved' ? '#dcfce7' : ($absence->status === 'rejected' ? '#fee2e2' : '#fef3c7') }}; color: {{ $absence->status === 'approved' ? '#166534' : ($absence->status === 'rejected' ? '#991b1b' : '#92400e') }}; font-size: 12px; font-weight: 600;">
                                            {{ ucfirst($absence->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        @if($absence->selfie_path)
                                            <button type="button" onclick="viewPhoto('{{ asset('storage/' . $absence->selfie_path) }}')" class="btn" style="padding: 6px 12px; font-size: 12px;">View</button>
                                        @else
                                            <span style="color: #999;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endforeach
    @else
        <div class="card">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd; background: #f5f5f5;">
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Student Name</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">NIM</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Rombel</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Check In</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600;">Selfie</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $absence)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;"><strong>{{ $absence->student->user->name ?? 'N/A' }}</strong></td>
                                <td style="padding: 12px;">{{ $absence->student->nim ?? 'N/A' }}</td>
                                <td style="padding: 12px;">{{ $absence->student->rombel->name ?? $absence->student->major ?? '—' }}</td>
                                <td style="padding: 12px;">{{ optional($absence->scanned_qr_at ?? $absence->absence_date)->format('M d, Y H:i') ?? 'N/A' }}</td>
                                <td style="padding: 12px;">
                                    <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; background: {{ $absence->status === 'approved' ? '#dcfce7' : ($absence->status === 'rejected' ? '#fee2e2' : '#fef3c7') }}; color: {{ $absence->status === 'approved' ? '#166534' : ($absence->status === 'rejected' ? '#991b1b' : '#92400e') }}; font-size: 12px; font-weight: 600;">
                                        {{ ucfirst($absence->status) }}
                                    </span>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    @if($absence->selfie_path)
                                        <button type="button" onclick="viewPhoto('{{ asset('storage/' . $absence->selfie_path) }}')" class="btn" style="padding: 6px 12px; font-size: 12px;">View</button>
                                    @else
                                        <span style="color: #999;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 40px; text-align: center; color: #666;">
                                    <i class="bi bi-inbox" style="font-size: 48px; margin-bottom: 10px; display: block; opacity: 0.3;"></i>
                                    <p style="font-size: 18px; margin: 10px 0;">No absence records found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($absences, 'hasPages') && $absences->hasPages())
                <div style="margin-top: 20px; padding: 20px; background: #f5f5f5; border-top: 1px solid #ddd; border-radius: 0 0 8px 8px;">
                    {{ $absences->links() }}
                </div>
            @endif
        </div>
    @endif

    <div id="photoModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 8px; padding: 20px; max-width: 600px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;">Student Selfie</h2>
                <button type="button" onclick="closePhotoModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
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

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection

@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>Daftar Agenda Harian Siswa</h1>
        <p>Lihat dan pantau agenda harian yang diinput oleh siswa</p>
    </div>

    @if (session('success'))
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    @if (session('error'))
        <div style="padding: 15px 20px; background: #fee2e2; border: 2px solid #dc2626; border-radius: 8px; margin-bottom: 20px; color: #7f1d1d;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    <div class="card" style="margin-bottom: 20px;">
        <h2 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600;">Filter & Pencarian</h2>
        
        <form method="GET" action="{{ route('daily-agenda.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <!-- Student Filter -->
            <div>
                <label for="student_id" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Pilih Siswa</label>
                <select name="student_id" id="student_id" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Semua Siswa --</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->user?->name ?? 'N/A' }} ({{ $student->nim }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From Filter -->
            <div>
                <label for="date_from" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" 
                       value="{{ request('date_from') }}" 
                       style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
            </div>

            <!-- Date To Filter -->
            <div>
                <label for="date_to" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px;">Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" 
                       value="{{ request('date_to') }}" 
                       style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
            </div>

            <!-- Submit Button -->
            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 10px; background: #f97316; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;"
                        onmouseover="this.style.background='#ea580c'"
                        onmouseout="this.style.background='#f97316'">
                    <i class="bi bi-search"></i> Filter
                </button>
                <a href="{{ route('daily-agenda.index') }}" style="padding: 10px 15px; background: #6b7280; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                   onmouseover="this.style.background='#4b5563'"
                   onmouseout="this.style.background='#6b7280'">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Agenda Harian Siswa</h2>
            <span style="background: #fed7aa; color: #92400e; padding: 8px 12px; border-radius: 4px; font-weight: 600;">
                Total: {{ $agendas->total() }} agenda
            </span>
        </div>

        @if ($agendas->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">No</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Nama Siswa</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">NIM</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Tanggal</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Jam Datang</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Jam Pulang</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Status Submit</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Verifikasi PKL</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agendas as $key => $agenda)
                            @php($agendaModalData = [
                                'student_name' => $agenda->student?->user?->name ?? 'N/A',
                                'nim' => $agenda->student?->nim ?? '-',
                                'agenda_date' => $agenda->agenda_date?->format('d/m/Y') ?? '-',
                                'day_name' => $agenda->agenda_date?->format('l') ?? '-',
                                'time_in' => $agenda->time_in ?? '-',
                                'time_out' => $agenda->time_out ?? '-',
                                'submitted_at' => $agenda->submitted_at?->format('d/m/Y H:i') ?? '',
                                'completion_status' => $agenda->completion_status ?? 'pending',
                                'completion_label' => $agenda->is_completed ? ($agenda->completion_status === 'approved' ? 'Disetujui' : ($agenda->completion_status === 'rejected' ? 'Ditolak' : 'Pending')) : 'Pending',
                                'completed_by' => $agenda->completedBy?->name ?? '',
                                'completed_at' => $agenda->completed_at?->format('d/m/Y H:i') ?? '',
                                'instructor_notes' => $agenda->instructor_notes ?? '',
                                'work_plan' => $agenda->work_plan ?? [],
                                'work_realization' => $agenda->work_realization ?? [],
                                'special_assignment' => $agenda->special_assignment ?? '',
                                'problems_found' => $agenda->problems_found ?? '',
                                'daily_assessment' => $agenda->daily_assessment ?? [],
                                'notes' => $agenda->notes ?? '',
                                'student_approved' => (bool) $agenda->student_approved,
                                'student_approved_at' => $agenda->student_approved_at?->format('d/m/Y H:i') ?? '',
                                'company_mentor_approved' => (bool) $agenda->company_mentor_approved,
                                'company_mentor_approved_at' => $agenda->company_mentor_approved_at?->format('d/m/Y H:i') ?? '',
                                'school_teacher_approved' => (bool) $agenda->school_teacher_approved,
                                'school_teacher_approved_at' => $agenda->school_teacher_approved_at?->format('d/m/Y H:i') ?? '',
                                'update_url' => route('daily-agenda.update', $agenda->id),
                            ])
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s; hover: { background: #f9fafb; }">
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #f97316; color: white; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                        {{ $agendas->firstItem() + $key }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <strong>{{ $agenda->student?->user?->name ?? 'N/A' }}</strong>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #f0f9ff; color: #082f49; padding: 4px 8px; border-radius: 4px;">{{ $agenda->student?->nim ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <strong>{{ $agenda->agenda_date->format('d/m/Y') }}</strong>
                                    <br><small style="color: #999;">{{ $agenda->agenda_date->format('l') }}</small>
                                </td>
                                <td style="padding: 12px; font-size: 14px; text-align: center;">
                                    <span style="background: #fed7aa; color: #92400e; padding: 4px 8px; border-radius: 4px;">{{ $agenda->time_in ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px; font-size: 14px; text-align: center;">
                                    <span style="background: #fed7aa; color: #92400e; padding: 4px 8px; border-radius: 4px;">{{ $agenda->time_out ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px; font-size: 14px; text-align: center;">
                                    @if ($agenda->submitted_at)
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-check-circle"></i> Submitted
                                        </span>
                                    @else
                                        <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px;">
                                            <i class="bi bi-pencil-square"></i> Draft
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px; font-size: 14px; text-align: center;">
                                    @if ($agenda->is_completed)
                                        @if ($agenda->completion_status === 'approved')
                                            <span style="background: #f0fdf4; color: #166534; border: 2px solid #10b981; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="bi bi-check-circle-fill"></i> Disetujui
                                            </span>
                                        @else
                                            <span style="background: #fee2e2; color: #7f1d1d; border: 2px solid #dc2626; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="bi bi-x-circle-fill"></i> Ditolak
                                            </span>
                                        @endif
                                    @else
                                        <span style="background: #fffbeb; color: #92400e; border: 2px solid #f59e0b; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="bi bi-hourglass-split"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <button type="button"
                                                onclick="openAgendaModal(this)"
                                                data-agenda='@json($agendaModalData)'
                                                style="padding: 6px 12px; background: #f97316; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s;"
                                                onmouseover="this.style.background='#ea580c'"
                                                onmouseout="this.style.background='#f97316'">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                        <button type="button"
                                                onclick="openAgendaEditModal(this)"
                                                data-agenda='@json($agendaModalData)'
                                                style="padding: 6px 12px; background: #0284c7; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s;"
                                                onmouseover="this.style.background='#0369a1'"
                                                onmouseout="this.style.background='#0284c7'">
                                            <i class="bi bi-pencil-square"></i> Edit Status
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $agendas->appends(request()->query())->links() }}
            </div>
        @else
            <div style="padding: 40px 20px; text-align: center; background: #fafafa; border-radius: 8px;">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 15px;"></i>
                <h3 style="color: #999; margin: 0 0 10px 0; font-size: 18px;">Tidak ada agenda harian</h3>
                <p style="color: #999; margin: 0; font-size: 14px;">Siswa belum membuat agenda harian berdasarkan filter yang Anda pilih</p>
            </div>
        @endif
        </div>

    <style>
        tr:hover {
            background: #f9fafb;
        }
    </style>

    @include('daily-agenda.partials.agenda-modal')
@endsection

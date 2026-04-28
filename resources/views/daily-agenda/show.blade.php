@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>Detail Agenda Harian</h1>
        <p>Lihat detail agenda dan tanda tangan</p>
    </div>

    @if ($errors->any())
        <div style="max-width: 1000px; margin: 0 auto 18px auto; padding: 14px 16px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 10px; color: #991b1b; line-height: 1.6;">
            <strong>Gagal menyimpan perubahan:</strong>
            <ul style="margin: 8px 0 0 18px; padding: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="max-width: 1000px; margin: 0 auto;">
        @php
            $agendaModalData = [
                'student_name' => $dailyAgenda->student?->user?->name ?? 'N/A',
                'nim' => $dailyAgenda->student?->nim ?? '-',
                'agenda_date' => $dailyAgenda->agenda_date?->format('d/m/Y') ?? '-',
                'day_name' => $dailyAgenda->agenda_date?->format('l') ?? '-',
                'time_in' => $dailyAgenda->time_in ?? '-',
                'time_out' => $dailyAgenda->time_out ?? '-',
                'submitted_at' => $dailyAgenda->submitted_at?->format('d/m/Y H:i') ?? '',
                'completion_status' => $dailyAgenda->completion_status ?? 'pending',
                'completion_label' => $dailyAgenda->completion_status === 'approved' ? 'Disetujui' : ($dailyAgenda->completion_status === 'rejected' ? 'Ditolak' : 'Pending'),
                'completed_by' => $dailyAgenda->completedBy?->name ?? '',
                'completed_at' => $dailyAgenda->completed_at?->format('d/m/Y H:i') ?? '',
                'instructor_notes' => $dailyAgenda->instructor_notes ?? '',
                'work_plan' => $dailyAgenda->work_plan ?? [],
                'work_realization' => $dailyAgenda->work_realization ?? [],
                'special_assignment' => $dailyAgenda->special_assignment ?? '',
                'problems_found' => $dailyAgenda->problems_found ?? '',
                'daily_assessment' => $dailyAgenda->daily_assessment ?? [],
                'notes' => $dailyAgenda->notes ?? '',
                'student_approved' => (bool) $dailyAgenda->student_approved,
                'student_approved_at' => $dailyAgenda->student_approved_at?->format('d/m/Y H:i') ?? '',
                'company_mentor_approved' => (bool) $dailyAgenda->company_mentor_approved,
                'company_mentor_approved_at' => $dailyAgenda->company_mentor_approved_at?->format('d/m/Y H:i') ?? '',
                'school_teacher_approved' => (bool) $dailyAgenda->school_teacher_approved,
                'school_teacher_approved_at' => $dailyAgenda->school_teacher_approved_at?->format('d/m/Y H:i') ?? '',
                'update_url' => route('daily-agenda.update', $dailyAgenda->id),
            ];
        @endphp
        <!-- Header Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Informasi Agenda</h2>
                <div style="display: flex; gap: 10px;">
                    @if ($canReviewAgenda)
                        <a href="{{ route('daily-agenda.edit', $dailyAgenda->id) }}"
                           style="padding: 10px 15px; background: #0284c7; color: white; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                           onmouseover="this.style.background='#0369a1'"
                           onmouseout="this.style.background='#0284c7'">
                            <i class="bi bi-pencil-square"></i> Edit Status
                        </a>
                    @endif
                    <a href="{{ route('daily-agenda.index') }}" 
                       style="padding: 10px 15px; background: #6b7280; color: white; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                       onmouseover="this.style.background='#4b5563'"
                       onmouseout="this.style.background='#6b7280'">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            @if ($canReviewAgenda)
                <div style="margin-bottom: 18px; padding: 12px 14px; border-radius: 10px; background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 13px; line-height: 1.6; display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-shield-check" style="font-size: 16px;"></i>
                    <span>Akun Anda memiliki akses verifikator. Gunakan tombol <strong>Edit Status</strong> untuk memperbarui persetujuan dan status PKL.</span>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Nama Siswa</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600;">{{ $dailyAgenda->student?->user?->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Tanggal</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600;">{{ optional($dailyAgenda->agenda_date)->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Jam Datang</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600; background: #fed7aa; display: inline-block; padding: 4px 8px; border-radius: 4px;">{{ $dailyAgenda->time_in ?? '-' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Jam Pulang</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600; background: #fed7aa; display: inline-block; padding: 4px 8px; border-radius: 4px;">{{ $dailyAgenda->time_out ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Rencana Pekerjaan -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #f97316;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-clipboard-check" style="margin-right: 8px; color: #f97316;"></i>Rencana Pekerjaan
            </h3>
            <div style="background: #fff8f0; border-radius: 6px; padding: 15px;">
                @if ($dailyAgenda->work_plan)
                    @foreach ($dailyAgenda->work_plan as $index => $plan)
                        <div style="margin-bottom: {{ $index < count($dailyAgenda->work_plan) - 1 ? '15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;' : '0' }}">
                            <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Rencana {{ $index + 1 }}</p>
                            <p style="margin: 8px 0 0 0; font-size: 14px; line-height: 1.6;">{{ $plan }}</p>
                        </div>
                    @endforeach
                @else
                    <p style="margin: 0; color: #999; font-style: italic;">Tidak ada data rencana pekerjaan.</p>
                @endif
            </div>
        </div>

        <!-- Realisasi Pekerjaan -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #10b981;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-check-circle" style="margin-right: 8px; color: #10b981;"></i>Realisasi Pekerjaan
            </h3>
            <div style="background: #f0fdf4; border-radius: 6px; padding: 15px;">
                @if ($dailyAgenda->work_realization)
                    @foreach ($dailyAgenda->work_realization as $index => $realization)
                        <div style="margin-bottom: {{ $index < count($dailyAgenda->work_realization) - 1 ? '15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;' : '0' }}">
                            <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Realisasi {{ $index + 1 }}</p>
                            <p style="margin: 8px 0 0 0; font-size: 14px; line-height: 1.6;">{{ $realization }}</p>
                        </div>
                    @endforeach
                @else
                    <p style="margin: 0; color: #999; font-style: italic;">Tidak ada data realisasi pekerjaan.</p>
                @endif
            </div>
        </div>

        <!-- Penugasan Khusus dari Atasan -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #f59e0b;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-briefcase" style="margin-right: 8px; color: #f59e0b;"></i>Penugasan Khusus dari Atasan
            </h3>
            <div style="background: #fffbeb; border-radius: 6px; padding: 15px;">
                <p style="margin: 0; font-size: 14px; line-height: 1.6;">{{ $dailyAgenda->special_assignment ?? 'Tidak ada penugasan khusus.' }}</p>
            </div>
        </div>

        <!-- Penemuan Masalah di Lapangan -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #dc2626;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-exclamation-triangle" style="margin-right: 8px; color: #dc2626;"></i>Penemuan Masalah di Lapangan
            </h3>
            <div style="background: #fee2e2; border-radius: 6px; padding: 15px;">
                <p style="margin: 0; font-size: 14px; line-height: 1.6;">{{ $dailyAgenda->problems_found ?? 'Tidak ada masalah ditemukan.' }}</p>
            </div>
        </div>

        <!-- Penilaian Harian -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #0284c7;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-star" style="margin-right: 8px; color: #0284c7;"></i>Penilaian Harian (Diisi Pembimbing Perusahaan)
            </h3>
            <div style="background: #f0f9ff; border-radius: 6px; padding: 15px;">
                @if ($dailyAgenda->daily_assessment)
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #dbeafe; border-bottom: 2px solid #0284c7;">
                                <th style="padding: 10px; text-align: left; font-weight: 600; font-size: 13px;">No</th>
                                <th style="padding: 10px; text-align: left; font-weight: 600; font-size: 13px;">Aspek Penilaian</th>
                                <th style="padding: 10px; text-align: center; font-weight: 600; font-size: 13px;">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dailyAgenda->daily_assessment as $index => $assessment)
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 10px; font-size: 14px;">{{ $index + 1 }}</td>
                                    <td style="padding: 10px; font-size: 14px;">{{ $assessment['label'] ?? 'N/A' }}</td>
                                    <td style="padding: 10px; text-align: center;">
                                        <span style="background: {{ $assessment['value'] === 'Baik' ? '#dcfce7' : '#fef3c7' }}; color: {{ $assessment['value'] === 'Baik' ? '#166534' : '#92400e' }}; padding: 4px 12px; border-radius: 4px; font-weight: 600; font-size: 13px;">
                                            {{ $assessment['value'] ?? 'Tidak dinilai' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="margin: 0; color: #999; font-style: italic;">Belum ada penilaian harian.</p>
                @endif
            </div>
        </div>

        <!-- Catatan untuk Diingat -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #8b5cf6;">
            <h3 style="margin: 0 0 15px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-sticky" style="margin-right: 8px; color: #8b5cf6;"></i>Catatan untuk Diingat
            </h3>
            <div style="background: #faf5ff; border-radius: 6px; padding: 15px;">
                <p style="margin: 0; font-size: 14px; line-height: 1.6;">{{ $dailyAgenda->notes ?? 'Tidak ada catatan.' }}</p>
            </div>
        </div>

        <!-- Approval Status Section -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #0284c7;">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-check2-all" style="margin-right: 8px; color: #0284c7;"></i>Status Persetujuan & Verifikasi Agenda
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                
                <!-- Student Approval -->
                <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="font-size: 24px; color: {{ $dailyAgenda->student_approved ? '#10b981' : '#d1d5db' }};">
                            <i class="bi bi-{{ $dailyAgenda->student_approved ? 'check-circle-fill' : 'circle' }}"></i>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #333;">Murid</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">{{ $dailyAgenda->student?->user?->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    @if ($dailyAgenda->student_approved)
                        <div style="background: #f0fdf4; border: 1px solid #10b981; border-radius: 6px; padding: 12px;">
                            <p style="margin: 0; font-size: 13px; color: #166534;">
                                <i class="bi bi-check" style="margin-right: 4px;"></i>
                                Disetujui pada {{ $dailyAgenda->student_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </p>
                        </div>
                    @else
                        @php
                            $isStudent = Auth::user()->student && Auth::user()->student->id === $dailyAgenda->student_id;
                        @endphp
                        @if ($isStudent)
                            <form method="POST" action="{{ route('daily-agenda.approve-student', $dailyAgenda->id) }}">
                                @csrf
                                <button type="submit" style="width: 100%; padding: 10px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;"
                                        onmouseover="this.style.background='#059669'"
                                        onmouseout="this.style.background='#10b981'">
                                    <i class="bi bi-check-circle"></i> Setujui Agenda
                                </button>
                            </form>
                        @else
                            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px;">
                                <p style="margin: 0; font-size: 13px; color: #92400e;">
                                    <i class="bi bi-info-circle" style="margin-right: 4px;"></i>
                                    Menunggu persetujuan siswa...
                                </p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Company Mentor Approval -->
                <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="font-size: 24px; color: {{ $dailyAgenda->company_mentor_approved ? '#10b981' : '#d1d5db' }};">
                            <i class="bi bi-{{ $dailyAgenda->company_mentor_approved ? 'check-circle-fill' : 'circle' }}"></i>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #333;">Pembimbing Perusahaan</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">Persetujuan dari pembimbing perusahaan</p>
                        </div>
                    </div>
                    
                    @if ($dailyAgenda->company_mentor_approved)
                        <div style="background: #f0fdf4; border: 1px solid #10b981; border-radius: 6px; padding: 12px;">
                            <p style="margin: 0; font-size: 13px; color: #166534;">
                                <i class="bi bi-check" style="margin-right: 4px;"></i>
                                Disetujui pada {{ $dailyAgenda->company_mentor_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </p>
                        </div>
                    @else
                        @php
                            $isReviewer = $canReviewAgenda;
                        @endphp
                        @if ($isReviewer)
                            <form method="POST" action="{{ route('daily-agenda.approve-company-mentor', $dailyAgenda->id) }}">
                                @csrf
                                <button type="submit" style="width: 100%; padding: 10px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;"
                                        onmouseover="this.style.background='#059669'"
                                        onmouseout="this.style.background='#10b981'">
                                    <i class="bi bi-check-circle"></i> Setujui Agenda
                                </button>
                            </form>
                        @else
                            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px;">
                                <p style="margin: 0; font-size: 13px; color: #92400e;">
                                    <i class="bi bi-info-circle" style="margin-right: 4px;"></i>
                                    Menunggu persetujuan verifikator...
                                </p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- School Teacher Approval -->
                <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <div style="font-size: 24px; color: {{ $dailyAgenda->school_teacher_approved ? '#10b981' : '#d1d5db' }};">
                            <i class="bi bi-{{ $dailyAgenda->school_teacher_approved ? 'check-circle-fill' : 'circle' }}"></i>
                        </div>
                        <div>
                            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #333;">Guru Pembimbing (Sekolah)</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">Pembimbing Sekolah</p>
                        </div>
                    </div>
                    
                    @if ($dailyAgenda->school_teacher_approved)
                        <div style="background: #f0fdf4; border: 1px solid #10b981; border-radius: 6px; padding: 12px;">
                            <p style="margin: 0; font-size: 13px; color: #166534;">
                                <i class="bi bi-check" style="margin-right: 4px;"></i>
                                Disetujui pada {{ $dailyAgenda->school_teacher_approved_at?->format('d/m/Y H:i') ?? 'N/A' }}
                            </p>
                        </div>
                    @else
                        @php
                            $isReviewer = $canReviewAgenda;
                        @endphp
                        @if ($isReviewer)
                            <form method="POST" action="{{ route('daily-agenda.approve-school-teacher', $dailyAgenda->id) }}">
                                @csrf
                                <button type="submit" style="width: 100%; padding: 10px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; gap: 6px; transition: background 0.2s;"
                                        onmouseover="this.style.background='#059669'"
                                        onmouseout="this.style.background='#10b981'">
                                    <i class="bi bi-check-circle"></i> Setujui Agenda
                                </button>
                            </form>
                        @else
                            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px;">
                                <p style="margin: 0; font-size: 13px; color: #92400e;">
                                    <i class="bi bi-info-circle" style="margin-right: 4px;"></i>
                                    Menunggu persetujuan verifikator...
                                </p>
                            </div>
                        @endif
                    @endif
                </div>

            </div>
        </div>

        <!-- Timestamps -->
        <div class="card" style="margin-bottom: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 13px; color: #999;">
                <div>
                    <p style="margin: 0; font-weight: 600; text-transform: uppercase;">Dibuat</p>
                    <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">{{ optional($dailyAgenda->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-weight: 600; text-transform: uppercase;">Diperbarui</p>
                    <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">{{ optional($dailyAgenda->updated_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Print Button -->
            <button onclick="window.print()" style="padding: 12px 30px; background: #f97316; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;"
                    onmouseover="this.style.background='#ea580c'"
                    onmouseout="this.style.background='#f97316'">
                <i class="bi bi-printer"></i> Cetak
            </button>

        <!-- Completion Status & Approval Section for Instructors/Admins -->
        @if ($canReviewAgenda)
            <div class="card" style="margin-top: 30px; border-left: 4px solid {{ $dailyAgenda->is_completed && $dailyAgenda->completion_status === 'approved' ? '#10b981' : ($dailyAgenda->is_completed && $dailyAgenda->completion_status === 'rejected' ? '#dc2626' : '#f59e0b') }};">
                <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600;">
                    <i class="bi bi-{{ $dailyAgenda->is_completed ? 'check-circle' : 'hourglass-split' }}" style="margin-right: 8px; color: {{ $dailyAgenda->is_completed && $dailyAgenda->completion_status === 'approved' ? '#10b981' : ($dailyAgenda->is_completed && $dailyAgenda->completion_status === 'rejected' ? '#dc2626' : '#f59e0b') }};"></i>Status Verifikasi PKL
                </h3>

                <!-- Current Status Display -->
                @if ($dailyAgenda->is_completed)
                    <div style="background: {{ $dailyAgenda->completion_status === 'approved' ? '#f0fdf4' : '#fee2e2' }}; border: 2px solid {{ $dailyAgenda->completion_status === 'approved' ? '#10b981' : '#dc2626' }}; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                            <div style="font-size: 24px; color: {{ $dailyAgenda->completion_status === 'approved' ? '#10b981' : '#dc2626' }};">
                                <i class="bi bi-{{ $dailyAgenda->completion_status === 'approved' ? 'check-circle-fill' : 'x-circle-fill' }}"></i>
                            </div>
                            <div>
                                <p style="margin: 0; font-size: 14px; font-weight: 600; color: {{ $dailyAgenda->completion_status === 'approved' ? '#166534' : '#7f1d1d' }};">
                                    {{ $dailyAgenda->completion_status === 'approved' ? 'Disetujui sebagai Bukti PKL' : 'Ditolak - Perlu Revisi' }}
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 13px; color: {{ $dailyAgenda->completion_status === 'approved' ? '#166534' : '#7f1d1d' }};">
                                    Oleh: <strong>{{ $dailyAgenda->completedBy?->name ?? 'N/A' }}</strong> 
                                    pada {{ $dailyAgenda->completed_at?->format('d/m/Y H:i') ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        @if ($dailyAgenda->instructor_notes)
                            <div style="background: white; border-radius: 6px; padding: 12px; margin-top: 12px; border-left: 4px solid {{ $dailyAgenda->completion_status === 'approved' ? '#10b981' : '#dc2626' }};">
                                <p style="margin: 0; font-size: 13px; font-weight: 600; color: #666; text-transform: uppercase;">Catatan dari Verifikator</p>
                                <p style="margin: 8px 0 0 0; font-size: 14px; color: #333; line-height: 1.6;">{{ $dailyAgenda->instructor_notes }}</p>
                            </div>
                        @endif

                        <!-- Unmark Button -->
                        <form method="POST" action="{{ route('daily-agenda.unmark-complete', $dailyAgenda->id) }}" style="margin-top: 15px;">
                            @csrf
                            <button type="submit" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                                    onmouseover="this.style.background='#4b5563'"
                                    onmouseout="this.style.background='#6b7280'"
                                    onclick="return confirm('Apakah Anda yakin ingin mengembalikan status menjadi pending?')">
                                <i class="bi bi-arrow-counterclockwise"></i> Batalkan Verifikasi
                            </button>
                        </form>
                    </div>
                @else
                    <!-- Mark as Complete Form -->
                    <div style="background: #fffbeb; border: 2px solid #f59e0b; border-radius: 8px; padding: 20px;">
                        <p style="margin: 0 0 15px 0; font-size: 14px; color: #92400e;">
                            <i class="bi bi-info-circle" style="margin-right: 6px;"></i>Agenda ini belum diverifikasi. Pilih status verifikasi di bawah ini.
                        </p>

                        <form method="POST" action="{{ route('daily-agenda.mark-complete', $dailyAgenda->id) }}" style="display: grid; gap: 15px;">
                            @csrf

                            <!-- Status Selection -->
                            <div>
                                <label for="completion_status" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Pilih Status Verifikasi</label>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                           onmouseover="this.style.borderColor='#10b981'; this.style.background='#f0fdf4'"
                                           onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                                        <input type="radio" name="completion_status" value="approved" required style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        <div>
                                            <p style="margin: 0; font-weight: 600; font-size: 14px; color: #10b981;">
                                                <i class="bi bi-check-circle"></i> Setujui
                                            </p>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Agenda valid sebagai bukti PKL</p>
                                        </div>
                                    </label>
                                    <label style="display: flex; align-items: center; padding: 12px; border: 2px solid #e5e7eb; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                           onmouseover="this.style.borderColor='#dc2626'; this.style.background='#fee2e2'"
                                           onmouseout="this.style.borderColor='#e5e7eb'; this.style.background='white'">
                                        <input type="radio" name="completion_status" value="rejected" required style="margin-right: 8px; width: 18px; height: 18px; cursor: pointer;">
                                        <div>
                                            <p style="margin: 0; font-weight: 600; font-size: 14px; color: #dc2626;">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </p>
                                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Perlu revisi/perbaikan</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Instructor Notes -->
                            <div>
                                <label for="instructor_notes" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: #333;">Catatan (Opsional)</label>
                                <textarea name="instructor_notes" id="instructor_notes" 
                                          placeholder="Masukkan catatan untuk siswa (misalnya: perbaikan yang diperlukan, poin bagus, dll.)"
                                          style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; font-family: inherit; min-height: 100px; resize: vertical;"
                                          maxlength="1000"></textarea>
                                <p style="margin: 6px 0 0 0; font-size: 12px; color: #999;">Max 1000 karakter</p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" style="padding: 12px 24px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;"
                                    onmouseover="this.style.background='#059669'"
                                    onmouseout="this.style.background='#10b981'">
                                <i class="bi bi-check2-square"></i> Simpan Verifikasi
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        @media print {
            button, a { display: none !important; }
            .page-header { display: none !important; }
        }
    </style>
    @include('daily-agenda.partials.agenda-modal')
@endsection


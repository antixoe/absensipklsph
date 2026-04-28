@extends('layouts.app')

@section('styles')
    <style>
        .agenda-warning-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(6px);
            z-index: 10050;
        }

        .agenda-warning-overlay.active {
            display: flex;
        }

        .agenda-warning-card {
            width: min(560px, 100%);
            border-radius: 24px;
            background: linear-gradient(180deg, #ffffff 0%, #fff8f1 100%);
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.35);
            overflow: hidden;
            border: 1px solid rgba(249, 115, 22, 0.18);
        }

        .agenda-warning-head {
            padding: 24px 24px 18px;
            background: linear-gradient(135deg, #f97316 0%, #fb923c 55%, #fde68a 100%);
            color: #1f2937;
        }

        .agenda-warning-kicker {
            margin: 0 0 8px 0;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(31, 41, 55, 0.7);
        }

        .agenda-warning-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.2;
        }

        .agenda-warning-body {
            padding: 22px 24px 24px;
        }

        .agenda-warning-message {
            margin: 0;
            font-size: 15px;
            line-height: 1.8;
            color: #334155;
        }

        .agenda-warning-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 24px;
        }

        .agenda-warning-close {
            border: none;
            border-radius: 999px;
            padding: 10px 18px;
            background: #f97316;
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
        }

        .agenda-warning-close:hover {
            background: #ea580c;
        }

        .agenda-warning-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.6);
            color: #9a3412;
            font-size: 22px;
            margin-bottom: 12px;
        }

        @media (max-width: 640px) {
            .agenda-warning-head {
                padding: 20px 20px 16px;
            }

            .agenda-warning-body {
                padding: 18px 20px 20px;
            }

            .agenda-warning-title {
                font-size: 20px;
            }

            .agenda-warning-actions {
                flex-direction: column;
            }

            .agenda-warning-close {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $canCreateAgenda = $canCreateAgenda ?? true;
        $agendaBlockMessage = $agendaBlockMessage ?? null;
        $agendaWarningMessage = session('agenda_warning') ?: $agendaBlockMessage;
    @endphp

    <div class="page-header">
        <h1><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>Daftar Agenda Harian</h1>
        <p>Kelola semua agenda harian Anda</p>
    </div>

    @if (session('success'))
        <div style="padding: 15px 20px; background: #dcfce7; border: 2px solid #10b981; border-radius: 8px; margin-bottom: 20px; color: #166534;">
            <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    @if (session('info'))
        <div style="padding: 15px 20px; background: #dbeafe; border: 2px solid #0284c7; border-radius: 8px; margin-bottom: 20px; color: #082f49;">
            <i class="bi bi-info-circle-fill" style="margin-right: 8px;"></i>
            <strong>{{ session('info') }}</strong>
        </div>
    @endif

    @if (session('error'))
        <div style="padding: 15px 20px; background: #fee2e2; border: 2px solid #dc2626; border-radius: 8px; margin-bottom: 20px; color: #7f1d1d;">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            <strong>{{ session('error') }}</strong>
        </div>
    @endif

    <div class="card" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Agenda Harian</h2>
            @if ($canCreateAgenda)
                <a href="{{ route('daily-agenda.create') }}" class="btn" style="gap: 8px; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle"></i>Buat Agenda Baru
                </a>
            @else
                <button type="button" class="btn" id="open-agenda-warning" style="gap: 8px; display: flex; align-items: center;">
                    <i class="bi bi-plus-circle"></i>Buat Agenda Baru
                </button>
            @endif
        </div>

        @if ($agendas->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f5f5f5; border-bottom: 2px solid #e5e7eb;">
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">No</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Tanggal</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Jam Datang</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Jam Pulang</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Status</th>
                            <th style="padding: 12px; text-align: left; font-weight: 600; font-size: 14px;">Verifikasi PKL</th>
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agendas as $key => $agenda)
                            @php($agendaModalData = [
                                'student_name' => $agenda->student?->user?->name ?? auth()->user()->name,
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
                            ])
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #f97316; color: white; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                        {{ $agendas->firstItem() + $key }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <strong>{{ optional($agenda->agenda_date)->format('d/m/Y') ?? 'N/A' }}</strong>
                                    <br><small style="color: #999;">{{ optional($agenda->agenda_date)->format('l') ?? 'N/A' }}</small>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #fed7aa; color: #92400e; padding: 4px 8px; border-radius: 4px;">{{ $agenda->time_in ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #fed7aa; color: #92400e; padding: 4px 8px; border-radius: 4px;">{{ $agenda->time_out ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    @if ($agenda->submitted_at)
                                        <span style="background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                            <i class="bi bi-check-circle"></i> Submitted
                                        </span>
                                    @else
                                        <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                            <i class="bi bi-pencil-square"></i> Draft
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
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
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $agendas->links() }}
            </div>
        @else
            <div style="padding: 40px 20px; text-align: center; background: #fafafa; border-radius: 8px;">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc; display: block; margin-bottom: 15px;"></i>
                <h3 style="color: #999; margin: 0 0 10px 0; font-size: 18px;">Tidak ada agenda harian</h3>
                <p style="color: #999; margin: 0 0 20px 0; font-size: 14px;">Buat agenda harian baru untuk memulai</p>
                @if ($canCreateAgenda)
                    <a href="{{ route('daily-agenda.create') }}" class="btn" style="gap: 8px; display: inline-flex; align-items: center;">
                        <i class="bi bi-plus-circle"></i>Buat Agenda Baru
                    </a>
                @else
                    <button type="button" class="btn" id="open-agenda-warning-empty" style="gap: 8px; display: inline-flex; align-items: center;">
                        <i class="bi bi-plus-circle"></i>Buat Agenda Baru
                    </button>
                @endif
            </div>
        @endif
    </div>

    @include('daily-agenda.partials.agenda-modal')

    <div class="agenda-warning-overlay {{ session('agenda_warning') ? 'active' : '' }}" id="agenda-warning-overlay" role="dialog" aria-modal="true" aria-labelledby="agenda-warning-title">
        <div class="agenda-warning-card">
            <div class="agenda-warning-head">
                <div class="agenda-warning-icon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <p class="agenda-warning-kicker">Agenda Tertahan</p>
                <h3 class="agenda-warning-title" id="agenda-warning-title">Absensi belum lengkap</h3>
            </div>
            <div class="agenda-warning-body">
                <p class="agenda-warning-message" id="agenda-warning-message">
                    {{ $agendaWarningMessage ?? 'Siswa perlu melakukan absensi terlebih dahulu sebelum membuat agenda harian.' }}
                </p>

                <div class="agenda-warning-actions">
                    <button type="button" class="agenda-warning-close" id="close-agenda-warning">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const overlay = document.getElementById('agenda-warning-overlay');
            const openButtons = [
                document.getElementById('open-agenda-warning'),
                document.getElementById('open-agenda-warning-empty'),
            ].filter(Boolean);
            const closeButton = document.getElementById('close-agenda-warning');

            const openModal = () => {
                if (overlay) {
                    overlay.classList.add('active');
                }
            };

            const closeModal = () => {
                if (overlay) {
                    overlay.classList.remove('active');
                }
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', openModal);
            });

            if (closeButton) {
                closeButton.addEventListener('click', closeModal);
            }

            if (overlay) {
                overlay.addEventListener('click', (event) => {
                    if (event.target === overlay) {
                        closeModal();
                    }
                });
            }

            if (overlay && {{ session('agenda_warning') ? 'true' : 'false' }}) {
                openModal();
            }
        });
    </script>

@endsection


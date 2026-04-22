@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>Detail Agenda Harian</h1>
        <p>Lihat detail agenda dan tanda tangan</p>
    </div>

    <div style="max-width: 1000px; margin: 0 auto;">
        <!-- Header Card -->
        <div class="card" style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; font-size: 20px; font-weight: 600;">Informasi Agenda</h2>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('daily-agenda.edit', $dailyAgenda->id) }}" 
                       style="padding: 10px 15px; background: #f59e0b; color: white; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                       onmouseover="this.style.background='#d97706'"
                       onmouseout="this.style.background='#f59e0b'">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('daily-agenda.index') }}" 
                       style="padding: 10px 15px; background: #6b7280; color: white; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                       onmouseover="this.style.background='#4b5563'"
                       onmouseout="this.style.background='#6b7280'">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Nama Siswa</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600;">{{ $dailyAgenda->student->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Tanggal</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600;">{{ $dailyAgenda->agenda_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Jam Datang</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600; background: #dbeafe; display: inline-block; padding: 4px 8px; border-radius: 4px;">{{ $dailyAgenda->time_in ?? '-' }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase;">Jam Pulang</p>
                    <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 600; background: #dbeafe; display: inline-block; padding: 4px 8px; border-radius: 4px;">{{ $dailyAgenda->time_out ?? '-' }}</p>
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

        <!-- Tanda Tangan Section -->
        <div class="card" style="margin-bottom: 20px; border-left: 4px solid #6b7280;">
            <h3 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 600;">
                <i class="bi bi-pen-fill" style="margin-right: 8px; color: #6b7280;"></i>Tanda Tangan
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <!-- Tanda Tangan Murid -->
                <div style="text-align: center;">
                    <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb; min-height: 180px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; cursor: pointer;" 
                         onclick="showSignatureModal('{{ isset($dailyAgenda->student_signature_path) ? asset('storage/' . $dailyAgenda->student_signature_path) : '' }}', 'Murid')">
                        @if ($dailyAgenda->student_signature_path)
                            <img src="{{ asset('storage/' . $dailyAgenda->student_signature_path) }}" 
                                 alt="Tanda Tangan Murid" 
                                 style="max-width: 100%; max-height: 120px; object-fit: contain;">
                        @else
                            <div style="text-align: center;">
                                <i class="bi bi-image" style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 8px;"></i>
                                <p style="color: #999; margin: 0; font-size: 13px;">Belum ada tanda tangan</p>
                            </div>
                        @endif
                    </div>
                    <h5 style="margin: 0 0 5px 0; font-weight: 600; font-size: 14px;">Murid</h5>
                    <p style="margin: 0; color: #999; font-size: 13px;">{{ $dailyAgenda->student->user->name ?? 'N/A' }}</p>
                </div>

                <!-- Tanda Tangan Instruktur (Perusahaan) -->
                <div style="text-align: center;">
                    <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb; min-height: 180px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; cursor: pointer;" 
                         onclick="showSignatureModal('{{ isset($dailyAgenda->company_mentor_signature_path) ? asset('storage/' . $dailyAgenda->company_mentor_signature_path) : '' }}', 'Instruktur')">
                        @if ($dailyAgenda->company_mentor_signature_path)
                            <img src="{{ asset('storage/' . $dailyAgenda->company_mentor_signature_path) }}" 
                                 alt="Tanda Tangan Instruktur" 
                                 style="max-width: 100%; max-height: 120px; object-fit: contain;">
                        @else
                            <div style="text-align: center;">
                                <i class="bi bi-image" style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 8px;"></i>
                                <p style="color: #999; margin: 0; font-size: 13px;">Belum ada tanda tangan</p>
                            </div>
                        @endif
                    </div>
                    <h5 style="margin: 0 0 5px 0; font-weight: 600; font-size: 14px;">Instruktur (Perusahaan)</h5>
                    <p style="margin: 0; color: #999; font-size: 13px;">Pembimbing Perusahaan</p>
                </div>

                <!-- Tanda Tangan Guru Pembimbing (Sekolah) -->
                <div style="text-align: center;">
                    <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #f9fafb; min-height: 180px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; cursor: pointer;" 
                         onclick="showSignatureModal('{{ isset($dailyAgenda->school_teacher_signature_path) ? asset('storage/' . $dailyAgenda->school_teacher_signature_path) : '' }}', 'Guru Pembimbing')">
                        @if ($dailyAgenda->school_teacher_signature_path)
                            <img src="{{ asset('storage/' . $dailyAgenda->school_teacher_signature_path) }}" 
                                 alt="Tanda Tangan Guru Pembimbing" 
                                 style="max-width: 100%; max-height: 120px; object-fit: contain;">
                        @else
                            <div style="text-align: center;">
                                <i class="bi bi-image" style="font-size: 48px; color: #d1d5db; display: block; margin-bottom: 8px;"></i>
                                <p style="color: #999; margin: 0; font-size: 13px;">Belum ada tanda tangan</p>
                            </div>
                        @endif
                    </div>
                    <h5 style="margin: 0 0 5px 0; font-weight: 600; font-size: 14px;">Guru Pembimbing (Sekolah)</h5>
                    <p style="margin: 0; color: #999; font-size: 13px;">Pembimbing Sekolah</p>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="card" style="margin-bottom: 30px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 13px; color: #999;">
                <div>
                    <p style="margin: 0; font-weight: 600; text-transform: uppercase;">Dibuat</p>
                    <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">{{ $dailyAgenda->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p style="margin: 0; font-weight: 600; text-transform: uppercase;">Diperbarui</p>
                    <p style="margin: 8px 0 0 0; font-size: 14px; color: #333;">{{ $dailyAgenda->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div style="text-align: center; margin-bottom: 30px;">
            <button onclick="window.print()" style="padding: 12px 30px; background: #f97316; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s;"
                    onmouseover="this.style.background='#ea580c'"
                    onmouseout="this.style.background='#f97316'">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Signature Modal -->
    <div id="signatureModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); max-width: 600px; width: 90%; overflow: hidden;">
            <div style="padding: 20px; background: #f97316; color: white; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; font-weight: 600;">
                    <i class="bi bi-pen-fill" style="margin-right: 8px;"></i>Tanda Tangan - <span id="signatureTitle"></span>
                </h3>
                <button onclick="closeSignatureModal()" style="background: none; border: none; color: white; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                    ×
                </button>
            </div>
            <div style="padding: 30px; text-align: center; background: #f9fafb;">
                <img id="signatureImage" src="" alt="Signature" style="max-width: 100%; max-height: 400px; object-fit: contain;">
            </div>
            <div style="padding: 15px; background: white; text-align: right; border-top: 1px solid #e5e7eb;">
                <button onclick="closeSignatureModal()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background 0.2s;"
                        onmouseover="this.style.background='#4b5563'"
                        onmouseout="this.style.background='#6b7280'">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function showSignatureModal(imageUrl, personType) {
            if (!imageUrl) return;
            document.getElementById('signatureImage').src = imageUrl;
            document.getElementById('signatureTitle').textContent = personType;
            document.getElementById('signatureModal').style.display = 'flex';
        }

        function closeSignatureModal() {
            document.getElementById('signatureModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('signatureModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeSignatureModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSignatureModal();
            }
        });

        @media print {
            button, a[style*="background"] { display: none !important; }
        }
    </script>

    <style>
        @media print {
            button, a { display: none !important; }
            .page-header { display: none !important; }
        }
    </style>
@endsection


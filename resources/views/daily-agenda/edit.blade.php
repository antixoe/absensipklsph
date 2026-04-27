@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Status Agenda</h1>
        <p>Hanya persetujuan dan verifikasi yang bisa diubah. Isi agenda tetap terkunci.</p>
    </div>

    <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 20px; max-width: 1400px; margin: 0 auto;">
        <div class="card" style="height: fit-content;">
            <h3 style="margin: 0 0 18px 0; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-lock" style="color: #f97316;"></i> Ringkasan Agenda
            </h3>

            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px;">
                <div style="padding: 14px; border-radius: 10px; background: #fff8f0; border: 1px solid #fed7aa;">
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #9a3412; text-transform: uppercase;">Nama Siswa</p>
                    <p style="margin: 8px 0 0 0; font-size: 15px; font-weight: 600; color: #111827;">{{ $dailyAgenda->student?->user?->name ?? 'N/A' }}</p>
                </div>
                <div style="padding: 14px; border-radius: 10px; background: #f8fafc; border: 1px solid #e5e7eb;">
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">NIM</p>
                    <p style="margin: 8px 0 0 0; font-size: 15px; font-weight: 600; color: #111827;">{{ $dailyAgenda->student?->nim ?? 'N/A' }}</p>
                </div>
                <div style="padding: 14px; border-radius: 10px; background: #f8fafc; border: 1px solid #e5e7eb;">
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">Tanggal</p>
                    <p style="margin: 8px 0 0 0; font-size: 15px; font-weight: 600; color: #111827;">{{ $dailyAgenda->agenda_date->format('d/m/Y') }}</p>
                </div>
                <div style="padding: 14px; border-radius: 10px; background: #f8fafc; border: 1px solid #e5e7eb;">
                    <p style="margin: 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">Jam</p>
                    <p style="margin: 8px 0 0 0; font-size: 15px; font-weight: 600; color: #111827;">
                        {{ $dailyAgenda->time_in ?? '-' }} - {{ $dailyAgenda->time_out ?? '-' }}
                    </p>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; border-radius: 12px; background: #f9fafb; border: 1px solid #e5e7eb;">
                <p style="margin: 0 0 10px 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">Status Saat Ini</p>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <span style="padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: {{ $dailyAgenda->student_approved ? '#dcfce7' : '#fef3c7' }}; color: {{ $dailyAgenda->student_approved ? '#166534' : '#92400e' }};">
                        Siswa: {{ $dailyAgenda->student_approved ? 'Disetujui' : 'Belum disetujui' }}
                    </span>
                    <span style="padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: {{ $dailyAgenda->company_mentor_approved ? '#dcfce7' : '#fef3c7' }}; color: {{ $dailyAgenda->company_mentor_approved ? '#166534' : '#92400e' }};">
                        Pembimbing Perusahaan: {{ $dailyAgenda->company_mentor_approved ? 'Disetujui' : 'Belum disetujui' }}
                    </span>
                    <span style="padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: {{ $dailyAgenda->school_teacher_approved ? '#dcfce7' : '#fef3c7' }}; color: {{ $dailyAgenda->school_teacher_approved ? '#166534' : '#92400e' }};">
                        Guru Sekolah: {{ $dailyAgenda->school_teacher_approved ? 'Disetujui' : 'Belum disetujui' }}
                    </span>
                    <span style="padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; background: {{ $dailyAgenda->completion_status === 'approved' ? '#dcfce7' : ($dailyAgenda->completion_status === 'rejected' ? '#fee2e2' : '#fef3c7') }}; color: {{ $dailyAgenda->completion_status === 'approved' ? '#166534' : ($dailyAgenda->completion_status === 'rejected' ? '#991b1b' : '#92400e') }};">
                        Verifikasi: {{ ucfirst($dailyAgenda->completion_status) }}
                    </span>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 16px; border-radius: 12px; background: #fff; border: 1px solid #e5e7eb;">
                <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase;">Catatan Verifikator</p>
                <p style="margin: 0; font-size: 14px; line-height: 1.7; color: #334155;">
                    {{ $dailyAgenda->instructor_notes ?? 'Belum ada catatan verifikator.' }}
                </p>
            </div>

            <div style="margin-top: 16px;">
                <a href="{{ route('daily-agenda.show', $dailyAgenda->id) }}" style="color: #f97316; font-weight: 600; text-decoration: none;">
                    <i class="bi bi-box-arrow-up-right" style="margin-right: 6px;"></i>Lihat detail penuh agenda
                </a>
            </div>
        </div>

        <div class="card" style="height: fit-content;">
            <h3 style="margin: 0 0 18px 0; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-check2-square" style="color: #0284c7;"></i> Persetujuan & Verifikasi
            </h3>

            @if ($errors->any())
                <div style="padding: 14px 16px; background: #fee2e2; border: 1px solid #ef4444; border-radius: 10px; margin-bottom: 16px; color: #991b1b;">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 18px; padding: 0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('daily-agenda.update', $dailyAgenda->id) }}" style="display: flex; flex-direction: column; gap: 18px;">
                @csrf
                @method('PUT')

                <div style="padding: 14px 16px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; color: #1d4ed8; font-size: 13px; line-height: 1.6;">
                    <strong>Catatan:</strong> Halaman ini hanya untuk mengubah status persetujuan dan verifikasi. Teks agenda, rencana kerja, realisasi, dan catatan utama tidak bisa diubah dari sini.
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #111827;">Persetujuan Pembimbing Perusahaan</label>
                    <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 10px; background: #fafafa;">
                        <input type="hidden" name="company_mentor_approved" value="0">
                        <input type="checkbox" name="company_mentor_approved" value="1" {{ old('company_mentor_approved', $dailyAgenda->company_mentor_approved) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: #374151;">Tandai sebagai disetujui oleh pembimbing perusahaan</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 700; color: #111827;">Persetujuan Guru Pembimbing Sekolah</label>
                    <label style="display: flex; align-items: center; gap: 10px; padding: 12px 14px; border: 1px solid #e5e7eb; border-radius: 10px; background: #fafafa;">
                        <input type="hidden" name="school_teacher_approved" value="0">
                        <input type="checkbox" name="school_teacher_approved" value="1" {{ old('school_teacher_approved', $dailyAgenda->school_teacher_approved) ? 'checked' : '' }}>
                        <span style="font-size: 14px; color: #374151;">Tandai sebagai disetujui oleh guru pembimbing sekolah</span>
                    </label>
                </div>

                <div>
                    <label for="completion_status" style="display: block; margin-bottom: 8px; font-weight: 700; color: #111827;">Status Verifikasi PKL</label>
                    <select name="completion_status" id="completion_status" style="width: 100%; padding: 12px 14px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                        <option value="pending" {{ old('completion_status', $dailyAgenda->completion_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('completion_status', $dailyAgenda->completion_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('completion_status', $dailyAgenda->completion_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('completion_status')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="instructor_notes" style="display: block; margin-bottom: 8px; font-weight: 700; color: #111827;">Catatan Verifikator</label>
                    <textarea name="instructor_notes" id="instructor_notes" rows="5"
                              placeholder="Masukkan catatan verifikasi, revisi, atau keterangan persetujuan"
                              style="width: 100%; padding: 12px 14px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: inherit; resize: vertical;">{{ old('instructor_notes', $dailyAgenda->instructor_notes) }}</textarea>
                    @error('instructor_notes')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                    <a href="{{ route('daily-agenda.show', $dailyAgenda->id) }}" style="padding: 10px 18px; background: #6b7280; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        Batal
                    </a>
                    <button type="submit" style="padding: 10px 18px; background: #0284c7; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                        Simpan Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 1.1fr 0.9fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection

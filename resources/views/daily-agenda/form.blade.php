@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-calendar-event" style="margin-right: 8px;"></i>{{ isset($dailyAgenda) ? 'Edit Agenda Harian' : 'Buat Agenda Harian' }}</h1>
        <p>{{ isset($dailyAgenda) ? 'Update your daily agenda' : 'Plan your work for the day' }}</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 1400px; margin: 0 auto;">
        <!-- LEFT SIDE: PREVIEW PANEL -->
        <div class="card" style="position: sticky; top: 100px; height: fit-content;">
            <h3 style="margin: 0 0 20px 0; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-eye" style="color: #f97316;"></i> Pratinjau Agenda
            </h3>

            <!-- Preview Basic Info -->
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb;">
                <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Tanggal</p>
                <p id="preview-date" style="margin: 0; font-size: 15px; font-weight: 600;">-</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
                    <div>
                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #999; font-weight: 600;">Jam Datang</p>
                        <p id="preview-time-in" style="margin: 0; font-size: 14px; background: #fed7aa; padding: 6px 10px; border-radius: 4px; display: inline-block;">-</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; font-size: 12px; color: #999; font-weight: 600;">Jam Pulang</p>
                        <p id="preview-time-out" style="margin: 0; font-size: 14px; background: #fed7aa; padding: 6px 10px; border-radius: 4px; display: inline-block;">-</p>
                    </div>
                </div>
            </div>

            <!-- Preview Work Plan -->
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; border-left: 3px solid #f97316; padding-left: 12px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Rencana Pekerjaan</h5>
                <div id="preview-work-plan" style="font-size: 13px; color: #666; line-height: 1.6;">
                    <p style="margin: 0; color: #999; font-style: italic;">Belum ada rencana</p>
                </div>
            </div>

            <!-- Preview Work Realization -->
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; border-left: 3px solid #10b981; padding-left: 12px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Realisasi Pekerjaan</h5>
                <div id="preview-work-realization" style="font-size: 13px; color: #666; line-height: 1.6;">
                    <p style="margin: 0; color: #999; font-style: italic;">Belum ada realisasi</p>
                </div>
            </div>

            <!-- Preview Special Assignment -->
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; border-left: 3px solid #f97316; padding-left: 12px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Penugasan Khusus</h5>
                <p id="preview-special-assignment" style="margin: 0; font-size: 13px; color: #666; line-height: 1.6;">
                    <span style="color: #999; font-style: italic;">-</span>
                </p>
            </div>

            <!-- Preview Problems Found -->
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #e5e7eb; border-left: 3px solid #dc2626; padding-left: 12px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Penemuan Masalah</h5>
                <p id="preview-problems-found" style="margin: 0; font-size: 13px; color: #666; line-height: 1.6;">
                    <span style="color: #999; font-style: italic;">-</span>
                </p>
            </div>

            <!-- Preview Assessment -->
            <div style="margin-bottom: 20px; border-left: 3px solid #f97316; padding-left: 12px;">
                <h5 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600;">Penilaian Harian</h5>
                <div id="preview-assessment" style="font-size: 12px;">
                    <p style="margin: 0; color: #999; font-style: italic;">Belum ada penilaian</p>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: INPUT FORM -->
        <div class="card">
            {{-- Debug info - helps identify missing absence data --}}
            @if (!isset($timeIn) || !$timeIn)
                <div style="padding: 15px 20px; background: #fef3c7; border: 2px solid #f59e0b; border-radius: 8px; margin-bottom: 20px; color: #92400e;">
                    <i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>
                    <strong>Data Absensi Tidak Ditemukan</strong>
                    <p style="margin: 8px 0 0 0; font-size: 13px; line-height: 1.6;">
                        Sistem tidak menemukan jam check-in dari data absensi Anda hari ini. Pastikan:
                    </p>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 13px;">
                        <li>✓ Anda sudah melakukan QR code scan di halaman Absensi</li>
                        <li>✓ QR code scan berhasil tersimpan (cek di riwayat absensi)</li>
                        <li>✓ Refresh halaman ini setelah QR code scan</li>
                    </ul>
                    <p style="margin: 10px 0 0 0; font-size: 13px;">
                        <a href="{{ route('absence.create') }}" style="color: #92400e; text-decoration: underline; font-weight: 600;">→ Buka Halaman Absensi</a>
                    </p>
                </div>
            @else
                <div style="padding: 12px 16px; background: #dcfce7; border-left: 4px solid #10b981; border-radius: 4px; margin-bottom: 20px; color: #166534; font-size: 13px;">
                    <i class="bi bi-check-circle-fill" style="margin-right: 8px;"></i>
                    <strong>✓ Jam Datang Tersimpan:</strong> <span style="font-weight: 600;">{{ $timeIn }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div style="padding: 15px 20px; background: #fee2e2; border: 2px solid #dc2626; border-radius: 8px; margin-bottom: 20px; color: #7f1d1d;">
                    <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
                    <strong>Error!</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ isset($dailyAgenda) ? route('daily-agenda.update', $dailyAgenda->id) : route('daily-agenda.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
                @csrf
                @if (isset($dailyAgenda))
                    @method('PUT')
                @endif

                <!-- Hari / Tanggal -->
                <div>
                    <label for="agenda_date" style="display: block; margin-bottom: 8px; font-weight: 600;">Hari / Tanggal</label>
                    <input type="date" id="agenda_date" name="agenda_date" 
                           value="{{ old('agenda_date', isset($dailyAgenda) ? $dailyAgenda->agenda_date->format('Y-m-d') : date('Y-m-d')) }}" 
                           style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                    @error('agenda_date')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Jam Datang dan Jam Pulang -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label for="time_in" style="display: block; margin-bottom: 8px; font-weight: 600;">Jam Datang <span style="font-size: 12px; color: #999;">(Dari Absensi)</span></label>
                        <input type="time" id="time_in" name="time_in" 
                               value="{{ old('time_in', isset($timeIn) ? $timeIn : (isset($dailyAgenda) ? $dailyAgenda->time_in : '')) }}" 
                               readonly
                               style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; background: #f3f4f6; color: #666; cursor: not-allowed;">
                        <p style="margin: 6px 0 0 0; font-size: 12px; color: #999;">
                            @if (isset($timeIn) && $timeIn)
                                <i class="bi bi-check-circle-fill" style="color: #10b981;"></i> Diambil dari data absensi
                            @else
                                <i class="bi bi-info-circle-fill"></i> Belum ada data absensi
                            @endif
                        </p>
                        @error('time_in')
                            <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="time_out" style="display: block; margin-bottom: 8px; font-weight: 600;">Jam Pulang <span style="font-size: 12px; color: #999;">{{ isset($timeOut) && $timeOut ? '(Dari Absensi)' : '(Manual)' }}</span></label>
                        <input type="time" id="time_out" name="time_out" 
                               value="{{ old('time_out', isset($timeOut) ? $timeOut : (isset($dailyAgenda) ? $dailyAgenda->time_out : '')) }}" 
                               placeholder="Masukkan jam pulang"
                               {{ isset($timeOut) && $timeOut ? 'readonly style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; background: #f3f4f6; color: #666; cursor: not-allowed;"' : 'style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;"' }}>
                        <p style="margin: 6px 0 0 0; font-size: 12px; color: #999;">
                            @if (isset($timeOut) && $timeOut)
                                <i class="bi bi-check-circle-fill" style="color: #10b981;"></i> Diambil dari data absensi
                            @else
                                <i class="bi bi-info-circle-fill"></i> Belum ada data absensi, input manual jam pulang
                            @endif
                        </p>
                        @error('time_out')
                            <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Rencana Pekerjaan -->
                <div>
                    <label style="display: block; margin-bottom: 12px; font-weight: 600;">Rencana Pekerjaan (5 Baris)</label>
                    <div style="border: 2px solid #f97316; border-radius: 8px; padding: 15px; background: #fff8f0;">
                        @for ($i = 0; $i < 5; $i++)
                            <div style="margin-bottom: {{ $i < 4 ? '15px' : '0' }};">
                                <label for="work_plan_{{ $i }}" style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Rencana {{ $i + 1 }}</label>
                                <textarea id="work_plan_{{ $i }}" name="work_plan[]" rows="2" 
                                          placeholder="Masukkan rencana pekerjaan (opsional)" 
                                          style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit; font-size: 13px;">{{ old("work_plan.$i", isset($dailyAgenda) && isset($dailyAgenda->work_plan[$i]) ? $dailyAgenda->work_plan[$i] : '') }}</textarea>
                                @error("work_plan.$i")
                                    <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                                @enderror
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Realisasi Pekerjaan -->
                <div>
                    <label style="display: block; margin-bottom: 12px; font-weight: 600;">Realisasi Pekerjaan (5 Baris)</label>
                    <div style="border: 2px solid #10b981; border-radius: 8px; padding: 15px; background: #f0fdf4;">
                        @for ($i = 0; $i < 5; $i++)
                            <div style="margin-bottom: {{ $i < 4 ? '15px' : '0' }};">
                                <label for="work_realization_{{ $i }}" style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px;">Realisasi {{ $i + 1 }}</label>
                                <textarea id="work_realization_{{ $i }}" name="work_realization[]" rows="2" 
                                          placeholder="Masukkan realisasi pekerjaan (opsional)" 
                                          style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit; font-size: 13px;">{{ old("work_realization.$i", isset($dailyAgenda) && isset($dailyAgenda->work_realization[$i]) ? $dailyAgenda->work_realization[$i] : '') }}</textarea>
                                @error("work_realization.$i")
                                    <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                                @enderror
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Penugasan Khusus dari Atasan -->
                <div>
                    <label for="special_assignment" style="display: block; margin-bottom: 8px; font-weight: 600;">Penugasan Khusus dari Atasan</label>
                    <textarea id="special_assignment" name="special_assignment" rows="3" 
                              placeholder="Masukkan penugasan khusus (opsional)" 
                              style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit; font-size: 13px;">{{ old('special_assignment', isset($dailyAgenda) ? $dailyAgenda->special_assignment : '') }}</textarea>
                    @error('special_assignment')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Penemuan Masalah di Lapangan -->
                <div>
                    <label for="problems_found" style="display: block; margin-bottom: 8px; font-weight: 600;">Penemuan Masalah di Lapangan</label>
                    <textarea id="problems_found" name="problems_found" rows="3" 
                              placeholder="Masukkan penemuan masalah di lapangan (opsional)" 
                              style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit; font-size: 13px;">{{ old('problems_found', isset($dailyAgenda) ? $dailyAgenda->problems_found : '') }}</textarea>
                    @error('problems_found')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Penilaian Harian (Diisi Pembimbing Perusahaan) -->
                <div>
                    <label style="display: block; margin-bottom: 12px; font-weight: 600;">Penilaian Harian (Diisi Pembimbing Perusahaan)</label>
                    
                    @if (!$isPembimbing)
                        <div style="padding: 12px 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px; margin-bottom: 15px; color: #92400e; font-size: 13px;">
                            <i class="bi bi-info-circle-fill" style="margin-right: 8px;"></i>
                            <strong>Catatan:</strong> Bagian penilaian ini hanya dapat diisi oleh Pembimbing Perusahaan. Silakan tunggu pembimbing untuk melengkapi penilaian.
                        </div>
                    @endif
                    
                    <div style="border: 2px solid #0284c7; border-radius: 8px; padding: 15px; background: #f0f9ff; {{ !$isPembimbing ? 'opacity: 0.6; pointer-events: none;' : '' }}">
                        @php
                            $assessmentItems = [
                                'Senyum',
                                'Keramahan',
                                'Penampilan',
                                'Komunikasi',
                                'Realisasi Kerja'
                            ];
                        @endphp

                        @foreach ($assessmentItems as $index => $label)
                            <div style="margin-bottom: {{ $index < count($assessmentItems) - 1 ? '15px; padding-bottom: 15px; border-bottom: 1px solid #e5e7eb;' : '0' }}">
                                <label style="display: block; margin-bottom: 10px; font-weight: 500; font-size: 13px;">{{ $index + 1 }}. {{ $label }}</label>
                                <div style="display: flex; gap: 20px;">
                                    <label style="display: flex; align-items: center; gap: 8px; {{ !$isPembimbing ? 'cursor: not-allowed; opacity: 0.6;' : 'cursor: pointer;' }}">
                                        <input type="radio" name="assessment_items[{{ $index }}]" value="Baik"
                                               {{ old("assessment_items.$index", isset($dailyAgenda) && isset($dailyAgenda->daily_assessment[$index]) ? $dailyAgenda->daily_assessment[$index]['value'] : '') === 'Baik' ? 'checked' : '' }}
                                               {{ !$isPembimbing ? 'disabled' : '' }}
                                               style="width: 18px; height: 18px; {{ !$isPembimbing ? 'cursor: not-allowed;' : 'cursor: pointer;' }}">
                                        <span style="{{ !$isPembimbing ? 'cursor: not-allowed;' : 'cursor: pointer;' }} font-size: 13px;">Baik</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; {{ !$isPembimbing ? 'cursor: not-allowed; opacity: 0.6;' : 'cursor: pointer;' }}">
                                        <input type="radio" name="assessment_items[{{ $index }}]" value="Kurang"
                                               {{ old("assessment_items.$index", isset($dailyAgenda) && isset($dailyAgenda->daily_assessment[$index]) ? $dailyAgenda->daily_assessment[$index]['value'] : '') === 'Kurang' ? 'checked' : '' }}
                                               {{ !$isPembimbing ? 'disabled' : '' }}
                                               style="width: 18px; height: 18px; {{ !$isPembimbing ? 'cursor: not-allowed;' : 'cursor: pointer;' }}">
                                        <span style="{{ !$isPembimbing ? 'cursor: not-allowed;' : 'cursor: pointer;' }} font-size: 13px;">Kurang</span>
                                    </label>
                                </div>
                                @error("assessment_items.$index")
                                    <span style="color: #dc2626; font-size: 12px; display: block; margin-top: 5px;">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Catatan untuk Diingat -->
                <div>
                    <label for="notes" style="display: block; margin-bottom: 8px; font-weight: 600;">Catatan untuk Diingat</label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Masukkan catatan (opsional)" 
                              style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-family: inherit; font-size: 13px;">{{ old('notes', isset($dailyAgenda) ? $dailyAgenda->notes : '') }}</textarea>
                    @error('notes')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
                    <a href="{{ route('daily-agenda.index') }}" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                       onmouseover="this.style.background='#4b5563'"
                       onmouseout="this.style.background='#6b7280'">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                    <button type="submit" style="padding: 10px 20px; background: #f97316; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s;"
                            onmouseover="this.style.background='#ea580c'"
                            onmouseout="this.style.background='#f97316'">
                        <i class="bi bi-check-circle"></i> {{ isset($dailyAgenda) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const dateInput = document.getElementById('agenda_date');
        const timeInInput = document.getElementById('time_in');
        const timeOutInput = document.getElementById('time_out');
        const specialAssignmentInput = document.getElementById('special_assignment');
        const problemsFoundInput = document.getElementById('problems_found');
        const notesInput = document.getElementById('notes');

        // Update preview on input change
        function updatePreview() {
            // Update date
            if (dateInput.value) {
                const date = new Date(dateInput.value);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                document.getElementById('preview-date').textContent = date.toLocaleDateString('id-ID', options);
            } else {
                document.getElementById('preview-date').textContent = '-';
            }

            // Update times
            document.getElementById('preview-time-in').textContent = timeInInput.value || '-';
            document.getElementById('preview-time-out').textContent = timeOutInput.value || '-';

            // Update work plan
            updateWorkPlanPreview();

            // Update work realization
            updateWorkRealizationPreview();

            // Update special assignment
            const specialAssignment = specialAssignmentInput.value.trim();
            document.getElementById('preview-special-assignment').innerHTML = specialAssignment ? 
                `<span style="color: #333;">${specialAssignment}</span>` : 
                `<span style="color: #999; font-style: italic;">-</span>`;

            // Update problems found
            const problemsFound = problemsFoundInput.value.trim();
            document.getElementById('preview-problems-found').innerHTML = problemsFound ? 
                `<span style="color: #333;">${problemsFound}</span>` : 
                `<span style="color: #999; font-style: italic;">-</span>`;

            // Update assessment
            updateAssessmentPreview();
        }

        function updateWorkPlanPreview() {
            const workPlanInputs = document.querySelectorAll('textarea[name="work_plan[]"]');
            const workPlanItems = [];
            
            workPlanInputs.forEach((input, index) => {
                if (input.value.trim()) {
                    workPlanItems.push(`<div style="margin-bottom: 8px;"><span style="color: #999; font-size: 12px;">Rencana ${index + 1}:</span><div style="color: #333; margin-top: 2px;">${input.value.trim()}</div></div>`);
                }
            });

            const preview = document.getElementById('preview-work-plan');
            if (workPlanItems.length > 0) {
                preview.innerHTML = workPlanItems.join('');
            } else {
                preview.innerHTML = '<p style="margin: 0; color: #999; font-style: italic;">Belum ada rencana</p>';
            }
        }

        function updateWorkRealizationPreview() {
            const realizationInputs = document.querySelectorAll('textarea[name="work_realization[]"]');
            const realizationItems = [];
            
            realizationInputs.forEach((input, index) => {
                if (input.value.trim()) {
                    realizationItems.push(`<div style="margin-bottom: 8px;"><span style="color: #999; font-size: 12px;">Realisasi ${index + 1}:</span><div style="color: #333; margin-top: 2px;">${input.value.trim()}</div></div>`);
                }
            });

            const preview = document.getElementById('preview-work-realization');
            if (realizationItems.length > 0) {
                preview.innerHTML = realizationItems.join('');
            } else {
                preview.innerHTML = '<p style="margin: 0; color: #999; font-style: italic;">Belum ada realisasi</p>';
            }
        }

        function updateAssessmentPreview() {
            const assessmentLabels = ['Senyum', 'Keramahan', 'Penampilan', 'Komunikasi', 'Realisasi Kerja'];
            const assessmentItems = [];
            
            assessmentLabels.forEach((label, index) => {
                const selected = document.querySelector(`input[name="assessment_items[${index}]"]:checked`);
                if (selected) {
                    const value = selected.value;
                    const bgColor = value === 'Baik' ? '#dcfce7' : '#fef3c7';
                    const textColor = value === 'Baik' ? '#166534' : '#92400e';
                    assessmentItems.push(`<div style="margin-bottom: 6px; display: flex; justify-content: space-between; font-size: 12px;"><span>${label}</span><span style="background: ${bgColor}; color: ${textColor}; padding: 2px 8px; border-radius: 3px; font-weight: 600;">${value}</span></div>`);
                }
            });

            const preview = document.getElementById('preview-assessment');
            if (assessmentItems.length > 0) {
                preview.innerHTML = assessmentItems.join('');
            } else {
                preview.innerHTML = '<p style="margin: 0; color: #999; font-style: italic;">Belum ada penilaian</p>';
            }
        }

        // Add event listeners
        dateInput.addEventListener('change', updatePreview);
        timeInInput.addEventListener('change', updatePreview);
        timeOutInput.addEventListener('change', updatePreview);
        specialAssignmentInput.addEventListener('input', updatePreview);
        problemsFoundInput.addEventListener('input', updatePreview);
        notesInput.addEventListener('input', updatePreview);

        document.querySelectorAll('textarea[name="work_plan[]"]').forEach(textarea => {
            textarea.addEventListener('input', updateWorkPlanPreview);
        });

        document.querySelectorAll('textarea[name="work_realization[]"]').forEach(textarea => {
            textarea.addEventListener('input', updateWorkRealizationPreview);
        });

        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', updateAssessmentPreview);
        });

        // Initial preview update
        updatePreview();

        // Make preview sticky on scroll
        window.addEventListener('scroll', () => {
            const previewPanel = document.querySelector('.card');
            if (previewPanel) {
                previewPanel.style.position = 'sticky';
            }
        });
    </script>

    <style>
        @media (max-width: 1024px) {
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
            
            .card[style*="position: sticky"] {
                position: static !important;
            }
        }

        @media print {
            button, a { display: none !important; }
            .card[style*="sticky"] { display: none !important; }
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection



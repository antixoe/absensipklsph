<style>
    .agenda-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.68);
        backdrop-filter: blur(6px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .agenda-modal-shell {
        width: min(1120px, 100%);
        height: min(calc(100vh - 40px), 980px);
        display: flex;
        flex-direction: column;
        min-height: 0;
        overflow: hidden;
        border-radius: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #fffdf8 100%);
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.35);
    }

    .agenda-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding: 24px 24px 22px;
        background: linear-gradient(135deg, #f97316 0%, #fb923c 42%, #fde68a 100%);
        color: #1f2937;
    }

    .agenda-modal-kicker {
        margin: 0 0 6px 0;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(31, 41, 55, 0.72);
    }

    .agenda-modal-title {
        margin: 0;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }

    .agenda-modal-subtitle {
        margin: 8px 0 0 0;
        font-size: 13px;
        line-height: 1.6;
        color: rgba(31, 41, 55, 0.8);
    }

    .agenda-modal-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }

    .agenda-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .agenda-pill.success {
        background: #dcfce7;
        color: #166534;
    }

    .agenda-pill.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .agenda-pill.danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .agenda-pill.neutral {
        background: rgba(255, 255, 255, 0.66);
        color: #334155;
        border: 1px solid rgba(51, 65, 85, 0.08);
    }

    .agenda-modal-close {
        border: none;
        background: rgba(255, 255, 255, 0.72);
        color: #1f2937;
        width: 40px;
        height: 40px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
        flex: 0 0 auto;
    }

    .agenda-modal-close:hover {
        background: #ffffff;
    }

    .agenda-modal-body {
        flex: 1 1 auto;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
        padding: 22px 24px 24px;
    }

    .agenda-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .agenda-summary-card {
        border-radius: 16px;
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .agenda-summary-label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .agenda-summary-value {
        margin: 8px 0 0 0;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.6;
        color: #0f172a;
    }

    .agenda-section-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .agenda-card {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .agenda-card h4 {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 12px 0;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .agenda-card-wide {
        margin-bottom: 16px;
    }

    .agenda-list-item {
        margin-bottom: 10px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .agenda-list-item:last-child {
        margin-bottom: 0;
    }

    .agenda-list-label {
        margin: 0 0 6px 0;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .agenda-list-text {
        margin: 0;
        font-size: 14px;
        line-height: 1.7;
        color: #0f172a;
        white-space: normal;
    }

    .agenda-empty {
        margin: 0;
        font-size: 14px;
        line-height: 1.7;
        color: #94a3b8;
        font-style: italic;
    }

    .agenda-approval-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .agenda-approval-card {
        border-radius: 14px;
        padding: 14px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .agenda-approval-head {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }

    .agenda-approval-icon {
        font-size: 20px;
        line-height: 1;
        color: #94a3b8;
    }

    .agenda-approval-title {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .agenda-approval-subtitle {
        margin: 3px 0 0 0;
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }

    .agenda-approval-state {
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 13px;
        line-height: 1.6;
        border: 1px solid #e5e7eb;
    }

    .agenda-approval-state.success {
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .agenda-approval-state.warning {
        color: #92400e;
        background: #fffbeb;
        border-color: #fcd34d;
    }

    .agenda-verification {
        border-radius: 16px;
        padding: 16px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .agenda-verification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    .agenda-verification-title {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: #0f172a;
    }

    .agenda-verification-meta {
        margin: 4px 0 0 0;
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
    }

    .agenda-notes {
        margin-top: 12px;
        border-radius: 12px;
        padding: 12px 14px;
        background: #eef2ff;
        border-left: 4px solid #6366f1;
        color: #3730a3;
        font-size: 14px;
        line-height: 1.7;
        white-space: normal;
    }

    .agenda-modal-footer {
        display: flex;
        justify-content: flex-end;
        flex: 0 0 auto;
        padding: 0 24px 24px;
    }

    .agenda-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .agenda-edit-layout {
        display: grid;
        grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
        gap: 16px;
    }

    .agenda-edit-panel {
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .agenda-edit-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .agenda-edit-note {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px 15px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        font-size: 13px;
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .agenda-edit-note strong {
        display: block;
        margin-bottom: 2px;
        font-size: 14px;
        color: #1e3a8a;
    }

    .agenda-edit-field {
        margin-bottom: 14px;
    }

    .agenda-edit-label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #0f172a;
    }

    .agenda-edit-help {
        margin: 6px 0 0 0;
        font-size: 12px;
        line-height: 1.5;
        color: #64748b;
    }

    .agenda-edit-toggle {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
    }

    .agenda-edit-toggle input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .agenda-edit-toggle-text {
        font-size: 14px;
        line-height: 1.6;
        color: #374151;
    }

    .agenda-edit-select,
    .agenda-edit-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        color: #0f172a;
        background: #ffffff;
    }

    .agenda-edit-textarea {
        min-height: 110px;
        resize: vertical;
    }

    .agenda-assessment-section {
        margin-bottom: 14px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #bfdbfe;
        background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    }

    .agenda-assessment-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .agenda-assessment-title {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
    }

    .agenda-assessment-description {
        margin: 0 0 14px 0;
        font-size: 13px;
        line-height: 1.6;
        color: #1d4ed8;
    }

    .agenda-assessment-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .agenda-assessment-item {
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #dbeafe;
        background: #ffffff;
    }

    .agenda-assessment-label {
        margin: 0 0 10px 0;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }

    .agenda-assessment-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .agenda-assessment-option {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        cursor: pointer;
    }

    .agenda-assessment-option input[type="radio"] {
        width: 18px;
        height: 18px;
        margin: 0;
    }

    .agenda-assessment-option .label-text {
        font-size: 13px;
        font-weight: 700;
    }

    @media (max-width: 900px) {
        .agenda-summary-grid,
        .agenda-section-grid,
        .agenda-approval-grid,
        .agenda-edit-grid,
        .agenda-edit-layout,
        .agenda-edit-summary-grid {
            grid-template-columns: 1fr;
        }

        .agenda-modal-shell {
            height: calc(100vh - 20px);
        }
    }
</style>

<div id="agendaModal" class="agenda-modal-overlay" aria-hidden="true">
    <div class="agenda-modal-shell" role="dialog" aria-modal="true" aria-labelledby="agendaModalTitle">
        <div class="agenda-modal-header">
            <div style="min-width: 0;">
                <p class="agenda-modal-kicker">Detail agenda harian</p>
                <h3 id="agendaModalTitle" class="agenda-modal-title">Agenda Harian</h3>
                <p id="agendaModalSubtitle" class="agenda-modal-subtitle"></p>
                <div id="agendaModalBadges" class="agenda-modal-badges"></div>
            </div>
            <button type="button" class="agenda-modal-close" data-close-agenda-modal aria-label="Tutup modal">
                &times;
            </button>
        </div>

        <div class="agenda-modal-body">
            <div id="agendaModalSummary" class="agenda-summary-grid"></div>
            <div id="agendaModalContent"></div>
        </div>

        <div class="agenda-modal-footer">
            <button type="button" class="btn" data-close-agenda-modal style="gap: 8px; display: inline-flex; align-items: center;">
                <i class="bi bi-x-circle"></i> Tutup
            </button>
        </div>
    </div>
</div>

<div id="agendaEditModal" class="agenda-modal-overlay" aria-hidden="true">
    <div class="agenda-modal-shell" role="dialog" aria-modal="true" aria-labelledby="agendaEditModalTitle" style="width: min(920px, 100%);">
        <div class="agenda-modal-header">
            <div style="min-width: 0;">
                <p class="agenda-modal-kicker">Edit status agenda</p>
                <h3 id="agendaEditModalTitle" class="agenda-modal-title">Edit Status Agenda</h3>
                <p id="agendaEditModalSubtitle" class="agenda-modal-subtitle"></p>
                <div id="agendaEditModalBadges" class="agenda-modal-badges"></div>
            </div>
            <button type="button" class="agenda-modal-close" data-close-agenda-edit-modal aria-label="Tutup modal">
                &times;
            </button>
        </div>

        <form id="agendaEditForm" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="agenda-modal-body">
                <div class="agenda-edit-layout">
                    <div class="agenda-edit-panel">
                        <div id="agendaEditSummary" class="agenda-edit-summary-grid"></div>

                        <div class="agenda-edit-note">
                            <i class="bi bi-lock-fill" style="font-size: 18px; margin-top: 2px;"></i>
                            <div>
                                <strong>Perubahan yang diizinkan</strong>
                                <div>Hanya status persetujuan, penilaian harian, status verifikasi, dan catatan verifikator yang bisa diubah. Konten agenda tetap terkunci.</div>
                            </div>
                        </div>

                        <div class="agenda-card agenda-card-wide" style="margin-bottom: 0; border-left: 4px solid #0284c7;">
                            <h4 style="margin-top: 0; color: #0369a1;">
                                <i class="bi bi-check2-all" style="color: #0284c7;"></i>
                                <span>Ringkasan Status</span>
                            </h4>
                            <p class="agenda-empty" style="margin-bottom: 0; font-style: normal;">
                                Gunakan panel kanan untuk mengubah status. Panel ini hanya menampilkan konteks singkat.
                            </p>
                        </div>
                    </div>

                    <div class="agenda-edit-panel">
                        <div class="agenda-edit-field">
                            <label class="agenda-edit-label">
                                Persetujuan Pembimbing Perusahaan
                            </label>
                            <label class="agenda-edit-toggle">
                                <input type="hidden" name="company_mentor_approved" value="0">
                                <input type="checkbox" id="agendaEditCompanyMentorApproved" name="company_mentor_approved" value="1">
                                <span class="agenda-edit-toggle-text">
                                    Tandai sebagai disetujui oleh pembimbing perusahaan
                                </span>
                            </label>
                        </div>

                        <div class="agenda-edit-field">
                            <label class="agenda-edit-label">
                                Persetujuan Guru Pembimbing Sekolah
                            </label>
                            <label class="agenda-edit-toggle">
                                <input type="hidden" name="school_teacher_approved" value="0">
                                <input type="checkbox" id="agendaEditSchoolTeacherApproved" name="school_teacher_approved" value="1">
                                <span class="agenda-edit-toggle-text">
                                    Tandai sebagai disetujui oleh guru pembimbing sekolah
                                </span>
                            </label>
                        </div>

                        <div class="agenda-edit-field">
                            <label for="agendaEditCompletionStatus" class="agenda-edit-label">
                                Status Verifikasi PKL
                            </label>
                            <select id="agendaEditCompletionStatus" name="completion_status" class="agenda-edit-select" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <p class="agenda-edit-help">
                                Gunakan `pending` jika agenda belum selesai diverifikasi.
                            </p>
                        </div>

                        <div class="agenda-edit-field" style="margin-bottom: 0;">
                            <label for="agendaEditInstructorNotes" class="agenda-edit-label">
                                Catatan Verifikator
                            </label>
                            <textarea id="agendaEditInstructorNotes" name="instructor_notes" class="agenda-edit-textarea" maxlength="1000" placeholder="Masukkan catatan verifikasi, revisi, atau keterangan persetujuan"></textarea>
                            <p class="agenda-edit-help">Opsional, maksimal 1000 karakter.</p>
                        </div>

                        <div class="agenda-assessment-section" id="agendaEditAssessment" style="margin-top: 14px;"></div>
                    </div>
                </div>
            </div>

            <div class="agenda-modal-footer" style="gap: 10px;">
                <button type="button" class="btn" data-close-agenda-edit-modal style="gap: 8px; display: inline-flex; align-items: center; background: #6b7280; color: white;">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" class="btn" style="gap: 8px; display: inline-flex; align-items: center;">
                    <i class="bi bi-check2-square"></i> Simpan Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('agendaModal');
        const editModal = document.getElementById('agendaEditModal');

        if (!modal && !editModal) {
            return;
        }

        const titleEl = document.getElementById('agendaModalTitle');
        const subtitleEl = document.getElementById('agendaModalSubtitle');
        const badgesEl = document.getElementById('agendaModalBadges');
        const summaryEl = document.getElementById('agendaModalSummary');
        const contentEl = document.getElementById('agendaModalContent');
        const closeButtons = modal ? modal.querySelectorAll('[data-close-agenda-modal]') : [];
        const editTitleEl = document.getElementById('agendaEditModalTitle');
        const editSubtitleEl = document.getElementById('agendaEditModalSubtitle');
        const editBadgesEl = document.getElementById('agendaEditModalBadges');
        const editSummaryEl = document.getElementById('agendaEditSummary');
        const editFormEl = document.getElementById('agendaEditForm');
        const editCompanyMentorEl = document.getElementById('agendaEditCompanyMentorApproved');
        const editSchoolTeacherEl = document.getElementById('agendaEditSchoolTeacherApproved');
        const editCompletionStatusEl = document.getElementById('agendaEditCompletionStatus');
        const editInstructorNotesEl = document.getElementById('agendaEditInstructorNotes');
        const editAssessmentEl = document.getElementById('agendaEditAssessment');
        const editCloseButtons = editModal ? editModal.querySelectorAll('[data-close-agenda-edit-modal]') : [];

        const toneMap = {
            success: {
                bg: '#dcfce7',
                border: '#10b981',
                text: '#166534',
                icon: '#10b981',
            },
            warning: {
                bg: '#fef3c7',
                border: '#f59e0b',
                text: '#92400e',
                icon: '#f59e0b',
            },
            danger: {
                bg: '#fee2e2',
                border: '#dc2626',
                text: '#991b1b',
                icon: '#dc2626',
            },
            neutral: {
                bg: '#e2e8f0',
                border: '#94a3b8',
                text: '#334155',
                icon: '#64748b',
            },
            blue: {
                bg: '#dbeafe',
                border: '#3b82f6',
                text: '#1d4ed8',
                icon: '#3b82f6',
            },
            orange: {
                bg: '#fff7ed',
                border: '#f97316',
                text: '#9a3412',
                icon: '#f97316',
            },
            purple: {
                bg: '#eef2ff',
                border: '#6366f1',
                text: '#4338ca',
                icon: '#6366f1',
            },
        };

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function normalize(value) {
            const text = String(value ?? '').trim();
            return text === '' || text === '-' ? '' : text;
        }

        function nl2br(value) {
            return escapeHtml(value).replace(/\n/g, '<br>');
        }

        function pill(text, tone, icon) {
            const t = toneMap[tone] || toneMap.neutral;
            return `
                <span class="agenda-pill" style="background: ${t.bg}; color: ${t.text};">
                    <i class="bi bi-${icon}" style="color: ${t.icon};"></i>
                    ${escapeHtml(text)}
                </span>
            `;
        }

        function summaryCard(label, value, icon, tone) {
            const t = toneMap[tone] || toneMap.neutral;
            return `
                <div class="agenda-summary-card" style="background: ${t.bg}; border-left: 4px solid ${t.border};">
                    <p class="agenda-summary-label" style="color: ${t.text};">
                        <i class="bi bi-${icon}" style="color: ${t.icon};"></i>
                        <span>${escapeHtml(label)}</span>
                    </p>
                    <p class="agenda-summary-value">${escapeHtml(value)}</p>
                </div>
            `;
        }

        function card(title, icon, accent, body) {
            const t = toneMap[accent] || toneMap.neutral;
            return `
                <div class="agenda-card" style="border-left: 4px solid ${t.border};">
                    <h4 style="color: ${t.text};">
                        <i class="bi bi-${icon}" style="color: ${t.icon};"></i>
                        <span>${escapeHtml(title)}</span>
                    </h4>
                    ${body}
                </div>
            `;
        }

        function emptyState(text) {
            return `<p class="agenda-empty">${escapeHtml(text)}</p>`;
        }

        function renderText(value, fallback) {
            const normalized = normalize(value);
            if (!normalized) {
                return emptyState(fallback);
            }

            return `<div class="agenda-list-text">${nl2br(normalized)}</div>`;
        }

        function renderEntries(items, labelPrefix, fallback) {
            if (!Array.isArray(items) || items.length === 0) {
                return emptyState(fallback);
            }

            const renderedItems = items.map(function (item, index) {
                const normalized = normalize(item);
                if (!normalized) {
                    return '';
                }

                return `
                    <div class="agenda-list-item">
                        <p class="agenda-list-label">${escapeHtml(labelPrefix)} ${index + 1}</p>
                        <div class="agenda-list-text">${nl2br(normalized)}</div>
                    </div>
                `;
            }).filter(Boolean);

            if (renderedItems.length === 0) {
                return emptyState(fallback);
            }

            return renderedItems.join('');
        }

        function renderAssessment(items) {
            if (!Array.isArray(items) || items.length === 0) {
                return emptyState('Belum ada penilaian harian.');
            }

            return items.map(function (item, index) {
                const label = normalize(item && item.label) || `Aspek ${index + 1}`;
                const value = normalize(item && item.value);
                const tone = value === 'Baik' ? 'success' : value === 'Kurang' ? 'warning' : 'neutral';
                const icon = value === 'Baik' ? 'check-circle-fill' : value === 'Kurang' ? 'exclamation-circle-fill' : 'dash-circle';

                return `
                    <div class="agenda-list-item" style="display: flex; justify-content: space-between; gap: 12px; align-items: center;">
                        <div>
                            <p class="agenda-list-label" style="margin-bottom: 4px;">${escapeHtml(label)}</p>
                            <div class="agenda-list-text" style="font-size: 13px; color: #64748b;">Penilaian harian siswa</div>
                        </div>
                        ${pill(value || 'Belum diisi', tone, icon)}
                    </div>
                `;
            }).join('');
        }

        function approvalCard(title, subtitle, approved, timestamp, pendingText) {
            const isApproved = Boolean(approved);
            return `
                <div class="agenda-approval-card" style="background: ${isApproved ? '#f0fdf4' : '#f9fafb'}; border-color: ${isApproved ? '#bbf7d0' : '#e5e7eb'}; border-left: 4px solid ${isApproved ? '#10b981' : '#cbd5e1'};">
                    <div class="agenda-approval-head">
                        <div class="agenda-approval-icon" style="color: ${isApproved ? '#10b981' : '#94a3b8'};">
                            <i class="bi bi-${isApproved ? 'check-circle-fill' : 'circle'}"></i>
                        </div>
                        <div>
                            <p class="agenda-approval-title">${escapeHtml(title)}</p>
                            <p class="agenda-approval-subtitle">${escapeHtml(subtitle)}</p>
                        </div>
                    </div>
                    ${
                        isApproved
                            ? `<div class="agenda-approval-state success">Disetujui${timestamp ? ` pada ${escapeHtml(timestamp)}` : ''}</div>`
                            : `<div class="agenda-approval-state warning">${escapeHtml(pendingText)}</div>`
                    }
                </div>
            `;
        }

        function renderVerification(agenda) {
            const status = normalize(agenda.completion_status) || 'pending';
            const isApproved = status === 'approved';
            const isRejected = status === 'rejected';
            const tone = isApproved ? 'success' : isRejected ? 'danger' : 'warning';
            const icon = isApproved ? 'check-circle-fill' : isRejected ? 'x-circle-fill' : 'hourglass-split';
            const label = isApproved ? 'Disetujui sebagai Bukti PKL' : isRejected ? 'Ditolak - Perlu Revisi' : 'Menunggu verifikasi';
            const metaParts = [];

            if (normalize(agenda.completed_by)) {
                metaParts.push(`Oleh <strong>${escapeHtml(agenda.completed_by)}</strong>`);
            }

            if (normalize(agenda.completed_at)) {
                metaParts.push(`pada ${escapeHtml(agenda.completed_at)}`);
            }

            const meta = metaParts.length > 0 ? metaParts.join(' ') : 'Belum ada verifikasi.';

            return `
                <div class="agenda-verification" style="background: ${toneMap[tone].bg}; border-left: 4px solid ${toneMap[tone].border};">
                    <div class="agenda-verification-header">
                        <div>
                            <p class="agenda-verification-title" style="color: ${toneMap[tone].text};">
                                <i class="bi bi-${icon}" style="margin-right: 6px; color: ${toneMap[tone].icon};"></i>
                                ${escapeHtml(label)}
                            </p>
                            <p class="agenda-verification-meta">${meta}</p>
                        </div>
                        ${pill(
                            isApproved ? 'Approved' : isRejected ? 'Rejected' : 'Pending',
                            tone,
                            icon
                        )}
                    </div>
                    ${
                        normalize(agenda.instructor_notes)
                            ? `<div class="agenda-notes"><strong>Catatan verifikator:</strong><br>${nl2br(agenda.instructor_notes)}</div>`
                            : ''
                    }
                </div>
            `;
        }

        function buildSummary(agenda) {
            return [
                summaryCard('Tanggal', `${normalize(agenda.agenda_date) || '-'}${normalize(agenda.day_name) ? ` (${agenda.day_name})` : ''}`, 'calendar-event', 'orange'),
                summaryCard('Jam Datang', normalize(agenda.time_in) || '-', 'clock-history', 'blue'),
                summaryCard('Jam Pulang', normalize(agenda.time_out) || '-', 'clock', 'orange'),
                summaryCard('Dibuat', normalize(agenda.submitted_at) || 'Draft', 'calendar-check', 'purple'),
            ].join('');
        }

        function buildEditSummary(agenda) {
            const submissionTone = normalize(agenda.submitted_at) ? 'success' : 'warning';
            const submissionLabel = normalize(agenda.submitted_at) ? 'Submitted' : 'Draft';
            const completionStatus = normalize(agenda.completion_status) || 'pending';
            const completionTone = completionStatus === 'approved' ? 'success' : completionStatus === 'rejected' ? 'danger' : 'warning';
            const completionLabel = completionStatus === 'approved' ? 'Disetujui' : completionStatus === 'rejected' ? 'Ditolak' : 'Pending';

            return [
                summaryCard('Nama Siswa', normalize(agenda.student_name) || 'N/A', 'person-badge', 'orange'),
                summaryCard('NIM', normalize(agenda.nim) || '-', 'card-text', 'blue'),
                summaryCard('Tanggal', `${normalize(agenda.agenda_date) || '-'}${normalize(agenda.day_name) ? ` (${agenda.day_name})` : ''}`, 'calendar-event', 'orange'),
                summaryCard('Status', completionLabel, 'check2-circle', completionTone),
                summaryCard('Submitted', submissionLabel, 'check-circle', submissionTone),
                summaryCard('Verifikator', normalize(agenda.completed_by) || 'Belum ada', 'person-check', 'purple'),
            ].join('');
        }

        function assessmentChoice(name, value, checked) {
            const isChecked = Boolean(checked);
            const accent = value === 'Baik' ? '#10b981' : '#f97316';
            const background = isChecked ? (value === 'Baik' ? '#f0fdf4' : '#fff7ed') : '#ffffff';
            const border = isChecked ? accent : '#d1d5db';
            const textColor = value === 'Baik' ? '#166534' : '#9a3412';

            return `
                <label class="agenda-assessment-option" style="border-color: ${border}; background: ${background};">
                    <input type="radio" name="${escapeHtml(name)}" value="${escapeHtml(value)}" ${isChecked ? 'checked' : ''} required>
                    <span class="label-text" style="color: ${textColor};">${escapeHtml(value)}</span>
                </label>
            `;
        }

        function buildAssessmentEditor(agenda) {
            const labels = ['Senyum', 'Keramahan', 'Penampilan', 'Komunikasi', 'Realisasi Kerja'];
            const items = Array.isArray(agenda.daily_assessment) ? agenda.daily_assessment : [];

            return `
                <div class="agenda-assessment-header">
                    <i class="bi bi-star-fill" style="color: #0284c7;"></i>
                    <p class="agenda-assessment-title">Penilaian Harian</p>
                </div>
                <p class="agenda-assessment-description">
                    Tentukan nilai Baik atau Kurang untuk setiap aspek di bawah ini.
                </p>
                <div class="agenda-assessment-list">
                    ${labels.map(function (label, index) {
                        const selected = normalize(items[index] && items[index].value);
                        const fieldName = `daily_assessment[${index}]`;

                        return `
                            <div class="agenda-assessment-item">
                                <p class="agenda-assessment-label">${index + 1}. ${escapeHtml(label)}</p>
                                <div class="agenda-assessment-options">
                                    ${assessmentChoice(fieldName, 'Baik', selected === 'Baik')}
                                    ${assessmentChoice(fieldName, 'Kurang', selected === 'Kurang')}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        }

        function buildBody(agenda) {
            return [
                `<div class="agenda-section-grid">
                    ${card('Rencana Pekerjaan', 'clipboard-check', 'orange', renderEntries(agenda.work_plan, 'Rencana', 'Belum ada rencana pekerjaan.'))}
                    ${card('Realisasi Pekerjaan', 'check-circle', 'green', renderEntries(agenda.work_realization, 'Realisasi', 'Belum ada realisasi pekerjaan.'))}
                </div>`,
                card('Penugasan Khusus', 'briefcase', 'orange', renderText(agenda.special_assignment, 'Tidak ada penugasan khusus.')),
                card('Penemuan Masalah', 'exclamation-triangle', 'danger', renderText(agenda.problems_found, 'Tidak ada masalah ditemukan.')),
                card('Penilaian Harian', 'star', 'blue', renderAssessment(agenda.daily_assessment)),
                card('Catatan untuk Diingat', 'sticky', 'purple', renderText(agenda.notes, 'Tidak ada catatan.')),
                `
                    <div class="agenda-card agenda-card-wide" style="border-left: 4px solid #0284c7;">
                        <h4 style="color: #0369a1;">
                            <i class="bi bi-check2-all" style="color: #0284c7;"></i>
                            <span>Status Persetujuan</span>
                        </h4>
                        <div class="agenda-approval-grid">
                            ${approvalCard('Murid', 'Persetujuan dari siswa pemilik agenda', agenda.student_approved, agenda.student_approved_at, 'Menunggu persetujuan siswa.')}
                            ${approvalCard('Pembimbing Perusahaan', 'Persetujuan dari mentor perusahaan', agenda.company_mentor_approved, agenda.company_mentor_approved_at, 'Menunggu persetujuan pembimbing perusahaan.')}
                            ${approvalCard('Guru Pembimbing Sekolah', 'Persetujuan dari guru sekolah', agenda.school_teacher_approved, agenda.school_teacher_approved_at, 'Menunggu persetujuan guru pembimbing sekolah.')}
                        </div>
                        ${renderVerification(agenda)}
                    </div>
                `,
            ].join('');
        }

        function setModalData(agenda) {
            const studentName = normalize(agenda.student_name);
            const nim = normalize(agenda.nim);
            const date = normalize(agenda.agenda_date);
            const dayName = normalize(agenda.day_name);

            titleEl.textContent = studentName ? `Agenda Harian - ${studentName}` : 'Detail Agenda Harian';
            subtitleEl.textContent = [
                nim ? `NIM ${nim}` : '',
                date ? date : '',
                dayName ? dayName : '',
            ].filter(Boolean).join('  |  ');

            const submissionTone = normalize(agenda.submitted_at) ? 'success' : 'warning';
            const submissionLabel = normalize(agenda.submitted_at) ? 'Submitted' : 'Draft';
            const verificationTone = agenda.completion_status === 'approved' ? 'success' : agenda.completion_status === 'rejected' ? 'danger' : 'warning';
            const verificationLabel = agenda.completion_label || (agenda.completion_status === 'approved' ? 'Disetujui' : agenda.completion_status === 'rejected' ? 'Ditolak' : 'Pending');

            badgesEl.innerHTML = [
                pill(submissionLabel, submissionTone, normalize(agenda.submitted_at) ? 'check-circle-fill' : 'pencil-square'),
                pill(verificationLabel, verificationTone, agenda.completion_status === 'approved' ? 'check-circle-fill' : agenda.completion_status === 'rejected' ? 'x-circle-fill' : 'hourglass-split'),
            ].join('');

            summaryEl.innerHTML = buildSummary(agenda);
            contentEl.innerHTML = buildBody(agenda);
        }

        function setEditModalData(agenda) {
            const studentName = normalize(agenda.student_name);
            const nim = normalize(agenda.nim);
            const date = normalize(agenda.agenda_date);
            const dayName = normalize(agenda.day_name);
            const completionStatus = normalize(agenda.completion_status) || 'pending';

            if (editTitleEl) {
                editTitleEl.textContent = studentName ? `Edit Status - ${studentName}` : 'Edit Status Agenda';
            }

            if (editSubtitleEl) {
                editSubtitleEl.textContent = [
                    nim ? `NIM ${nim}` : '',
                    date ? date : '',
                    dayName ? dayName : '',
                ].filter(Boolean).join('  |  ');
            }

            if (editBadgesEl) {
                const statusTone = completionStatus === 'approved' ? 'success' : completionStatus === 'rejected' ? 'danger' : 'warning';
                const statusLabel = completionStatus === 'approved' ? 'Disetujui' : completionStatus === 'rejected' ? 'Ditolak' : 'Pending';
                editBadgesEl.innerHTML = [
                    pill(normalize(agenda.submitted_at) ? 'Submitted' : 'Draft', normalize(agenda.submitted_at) ? 'success' : 'warning', normalize(agenda.submitted_at) ? 'check-circle-fill' : 'pencil-square'),
                    pill(statusLabel, statusTone, completionStatus === 'approved' ? 'check-circle-fill' : completionStatus === 'rejected' ? 'x-circle-fill' : 'hourglass-split'),
                ].join('');
            }

            if (editSummaryEl) {
                editSummaryEl.innerHTML = buildEditSummary(agenda);
            }

            if (editFormEl) {
                editFormEl.action = agenda.update_url || '';
            }

            if (editCompanyMentorEl) {
                editCompanyMentorEl.checked = Boolean(agenda.company_mentor_approved);
            }

            if (editSchoolTeacherEl) {
                editSchoolTeacherEl.checked = Boolean(agenda.school_teacher_approved);
            }

            if (editCompletionStatusEl) {
                editCompletionStatusEl.value = completionStatus;
            }

            if (editInstructorNotesEl) {
                editInstructorNotesEl.value = normalize(agenda.instructor_notes);
            }

            if (editAssessmentEl) {
                editAssessmentEl.innerHTML = buildAssessmentEditor(agenda);
            }
        }

        window.openAgendaModal = function (button, event) {
            try {
                if (event && typeof event.preventDefault === 'function') {
                    event.preventDefault();
                }

                if (!modal) {
                    if (button && button.href) {
                        window.location.href = button.href;
                    }
                    return;
                }

                const agenda = JSON.parse(button.getAttribute('data-agenda') || (button.dataset ? button.dataset.agenda : '') || '{}');
                setModalData(agenda);
                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Failed to open agenda modal:', error);
            }
        };

        window.openAgendaEditModal = function (button, event) {
            try {
                if (event && typeof event.preventDefault === 'function') {
                    event.preventDefault();
                }

                if (!editModal) {
                    if (button && button.href) {
                        window.location.href = button.href;
                    }
                    return;
                }

                const agenda = JSON.parse(button.getAttribute('data-agenda') || (button.dataset ? button.dataset.agenda : '') || '{}');
                setEditModalData(agenda);
                if (modal) {
                    window.closeAgendaModal();
                }
                editModal.style.display = 'flex';
                editModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Failed to open agenda edit modal:', error);
            }
        };

        window.closeAgendaModal = function () {
            if (!modal) {
                return;
            }

            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        window.closeAgendaEditModal = function () {
            if (!editModal) {
                return;
            }

            editModal.style.display = 'none';
            editModal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        };

        if (modal) {
            closeButtons.forEach(function (button) {
                button.addEventListener('click', window.closeAgendaModal);
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    window.closeAgendaModal();
                }
            });
        }

        if (editModal) {
            editCloseButtons.forEach(function (button) {
                button.addEventListener('click', window.closeAgendaEditModal);
            });

            editModal.addEventListener('click', function (event) {
                if (event.target === editModal) {
                    window.closeAgendaEditModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (editModal && editModal.style.display === 'flex') {
                window.closeAgendaEditModal();
                return;
            }

            if (modal && modal.style.display === 'flex') {
                window.closeAgendaModal();
            }
        });
    })();
</script>

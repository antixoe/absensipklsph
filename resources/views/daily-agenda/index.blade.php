@extends('layouts.app')

@section('content')
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
            <a href="{{ route('daily-agenda.create') }}" class="btn" style="gap: 8px; display: flex; align-items: center;">
                <i class="bi bi-plus-circle"></i>Buat Agenda Baru
            </a>
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
                            <th style="padding: 12px; text-align: center; font-weight: 600; font-size: 14px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agendas as $key => $agenda)
                            <tr style="border-bottom: 1px solid #e5e7eb; transition: background 0.2s;">
                                <td style="padding: 12px; font-size: 14px;">
                                    <span style="background: #f97316; color: white; padding: 4px 8px; border-radius: 4px; font-weight: 600;">
                                        {{ $agendas->firstItem() + $key }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-size: 14px;">
                                    <strong>{{ $agenda->agenda_date->format('d/m/Y') }}</strong>
                                    <br><small style="color: #999;">{{ $agenda->agenda_date->format('l') }}</small>
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
                                <td style="padding: 12px; text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('daily-agenda.show', $agenda->id) }}" 
                                           style="padding: 6px 12px; background: #f97316; color: white; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#ea580c'"
                                           onmouseout="this.style.background='#f97316'">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                        <a href="{{ route('daily-agenda.edit', $agenda->id) }}" 
                                           style="padding: 6px 12px; background: #f97316; color: white; border: none; border-radius: 4px; text-decoration: none; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#ea580c'"
                                           onmouseout="this.style.background='#f97316'">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button onclick="showDeleteConfirm('{{ route('daily-agenda.destroy', $agenda->id) }}')" 
                                                style="padding: 6px 12px; background: #dc2626; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; transition: background 0.2s;"
                                                onmouseover="this.style.background='#b91c1c'"
                                                onmouseout="this.style.background='#dc2626'">
                                            <i class="bi bi-trash"></i> Hapus
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
                <a href="{{ route('daily-agenda.create') }}" class="btn" style="gap: 8px; display: inline-flex; align-items: center;">
                    <i class="bi bi-plus-circle"></i>Buat Agenda Baru
                </a>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.6); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2); max-width: 400px; width: 90%;">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 48px; color: #dc2626;"></i>
            </div>
            <h3 style="margin: 0 0 10px 0; color: #333; font-size: 18px; font-weight: 600; text-align: center;">Konfirmasi Penghapusan</h3>
            <p style="margin: 0 0 20px 0; color: #666; text-align: center; font-size: 14px;">Apakah Anda yakin ingin menghapus agenda harian ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div style="display: flex; gap: 10px;">
                <button onclick="closeDeleteModal()" style="flex: 1; padding: 10px; background: #e5e7eb; color: #333; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Batal
                </button>
                <form id="deleteForm" method="POST" style="flex: 1;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 100%; padding: 10px; background: #dc2626; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirm(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection



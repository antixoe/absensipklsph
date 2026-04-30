@extends('layouts.app')

@section('content')
    @php
        $editRole = session('open_edit_role_id') ? $roles->firstWhere('id', session('open_edit_role_id')) : null;
    @endphp

    <div class="page-header">
        <h1><i class="bi bi-shield-lock" style="margin-right: 8px;"></i>Manage Roles & Features</h1>
        <p>Atur hak akses peran pengguna</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px;">
            <h4 style="margin-top: 0;"><i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>Validation Errors</h4>
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span><i class="bi bi-people" style="margin-right: 8px;"></i>Peran & Izin</span>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn btn-primary" style="padding: 8px 16px; font-size: 12px;" onclick="openCreateModal()">
                    <i class="bi bi-plus-circle" style="margin-right: 5px;"></i>Tambah Peran
                </button>
                <a href="{{ route('admin.features') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 12px;">
                    <i class="bi bi-sliders" style="margin-right: 5px;"></i>Kelola Fitur
                </a>
            </div>
        </div>

        <div style="overflow-x: auto; margin-top: 20px;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                        <th style="padding: 12px; text-align: left;">Peran</th>
                        <th style="padding: 12px; text-align: left;">Deskripsi</th>
                        <th style="padding: 12px; text-align: left;">Fitur Tertaut</th>
                        <th style="padding: 12px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; font-weight: 600;">{{ \App\Models\Role::displayName($role->name) }}</td>
                            <td style="padding: 12px; color: #666;">{{ $role->description ?? 'N/A' }}</td>
                            <td style="padding: 12px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    @forelse ($role->features as $feature)
                                        <span style="display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-size: 12px;">
                                            {{ $feature->name }}
                                        </span>
                                    @empty
                                        <span style="color: #999;">Belum ada fitur</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: inline-flex; gap: 8px; align-items: center; justify-content: center; flex-wrap: wrap; white-space: nowrap;">
                                    <button
                                        type="button"
                                        class="btn"
                                        style="padding: 6px 12px; font-size: 12px;"
                                        data-role-id="{{ $role->id }}"
                                        data-role-name="{{ e($role->name) }}"
                                        data-role-description="{{ e($role->description ?? '') }}"
                                        data-role-update-url="{{ route('admin.roles.update', $role) }}"
                                        data-role-features='@json($role->features->pluck("id")->values())'
                                        onclick="openEditModal(this)"
                                    >
                                        <i class="bi bi-pencil-square" style="margin-right: 5px;"></i>Edit
                                    </button>
                                    <button type="button" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;" onclick="confirmDelete('{{ \App\Models\Role::displayName($role->name) }}', '{{ route('admin.roles.destroy', $role) }}')">
                                        <i class="bi bi-trash" style="margin-right: 5px;"></i>Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
            <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Hapus Peran</h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;" onclick="closeDeleteModal()">&times;</button>
            </div>

            <div style="padding: 25px;">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="bi bi-exclamation-circle" style="font-size: 48px; color: #991b1b;"></i>
                </div>
                <p style="margin: 0 0 10px 0; font-size: 16px; color: #1f2937; text-align: center;">
                    Apakah Anda yakin ingin menghapus peran <strong id="deleteRoleName"></strong>?
                </p>
                <p style="font-size: 13px; color: #6b7280; margin: 10px 0; text-align: center;">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeDeleteModal()">Batal</button>
                <button type="button" id="deleteConfirmBtn" style="padding: 10px 16px; background: #991b1b; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="submitDelete()">Hapus Peran</button>
            </div>
        </div>
    </div>

    <script>
        let deleteFormAction = null;

        function confirmDelete(roleName, deleteUrl) {
            document.getElementById('deleteRoleName').textContent = roleName;
            deleteFormAction = deleteUrl;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteFormAction = null;
        }

        function submitDelete() {
            if (!deleteFormAction) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteFormAction;

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const token = document.createElement('input');
                token.type = 'hidden';
                token.name = '_token';
                token.value = csrfToken.getAttribute('content');
                form.appendChild(token);
            }

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            document.body.appendChild(form);
            form.submit();
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>

    <!-- Edit Role Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 960px; margin: 20px auto; max-height: 90vh; overflow-y: auto;">
            <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; color: #1f2937;">
                    <i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Peran
                </h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;" onclick="closeEditModal()">&times;</button>
            </div>

            @if ($errors->any())
                <div style="padding: 20px 25px 0 25px;">
                    <div style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 12px 15px; border-radius: 8px; font-size: 13px;">
                        <strong>Periksa kembali data yang Anda masukkan.</strong>
                        <ul style="margin: 8px 0 0 18px; padding: 0;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="editRoleForm" action="" method="POST" style="padding: 25px;">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_role_id" name="role_id" value="">

                <div style="margin-bottom: 20px;">
                    <label for="edit_name" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Nama Peran <span style="color: red;">*</span>
                    </label>
                    <input type="text" id="edit_name" name="name" placeholder="contoh: pembimbing_pkl, kepala_jurusan"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;"
                           required>
                    <span style="color: #999; font-size: 12px; display: block; margin-top: 5px;">Gunakan huruf kecil dan garis bawah, misalnya `pembimbing_pkl`.</span>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="edit_description" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Deskripsi <span style="color: #999; font-size: 11px;">(opsional)</span>
                    </label>
                    <textarea id="edit_description" name="description" rows="3" placeholder="Masukkan deskripsi peran..."
                              style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-family: inherit;"></textarea>
                </div>

                <div style="background: #f9fafb; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #93c5fd;">
                    <p style="margin: 0; color: #666; font-size: 12px;">
                        <i class="bi bi-lightbulb" style="margin-right: 5px;"></i>
                        Ubah nama, deskripsi, dan fitur yang dapat diakses peran ini.
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 25px;">
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px;">
                            <i class="bi bi-mortarboard" style="margin-right: 8px; color: #166534;"></i>Fitur Siswa
                        </h4>
                        @foreach ($features->whereIn('slug', ['checkin_checkout', 'fill_logbook', 'view_guidance']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                    id="edit_student_feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="edit_student_feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 10px;">
                            <i class="bi bi-person-check" style="margin-right: 8px; color: #92400e;"></i>Fitur Pembimbing
                        </h4>
                        @foreach ($features->whereIn('slug', ['validate_attendance', 'validate_logbook', 'provide_guidance']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                    id="edit_supervisor_feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="edit_supervisor_feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #e0e7ff; padding-bottom: 10px;">
                            <i class="bi bi-shield-lock" style="margin-right: 8px; color: #4338ca;"></i>Fitur Manajemen
                        </h4>
                        @foreach ($features->whereIn('slug', ['manage_roles', 'manage_users', 'manage_activities']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                    id="edit_management_feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="edit_management_feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #ddd6fe; padding-bottom: 10px;">
                            <i class="bi bi-graph-up" style="margin-right: 8px; color: #5b21b6;"></i>Fitur Administratif
                        </h4>
                        @foreach ($features->whereIn('slug', ['view_all_data', 'view_reports', 'weekly_review']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                    id="edit_admin_feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="edit_admin_feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fee2e2; padding-bottom: 10px;">
                            <i class="bi bi-funnel" style="margin-right: 8px; color: #991b1b;"></i>Fitur Penyaring
                        </h4>
                        @foreach ($features->whereIn('slug', ['department_filter', 'class_filter']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                                    id="edit_filter_feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="edit_filter_feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeEditModal()">Batal</button>
                    <button type="submit" style="padding: 10px 16px; background: #0369a1; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                        <i class="bi bi-check-lg" style="margin-right: 5px;"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($editRole)
        <script>
            window.__openEditRoleData = {
                id: {{ $editRole->id }},
                name: @json(old('name', $editRole->name)),
                description: @json(old('description', $editRole->description ?? '')),
                updateUrl: @json(route('admin.roles.update', $editRole)),
                features: @json(old('features', $editRole->features->pluck('id')->values()->all())),
            };
        </script>
    @endif

    <!-- Create Role Modal -->
    <div id="createModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; overflow-y: auto;">
        <div style="background: white; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); width: 90%; max-width: 600px; margin: 20px auto;">
            <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 18px; color: #1f2937;">
                    <i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Tambah Peran Baru
                </h3>
                <button type="button" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6b7280;" onclick="closeCreateModal()">&times;</button>
            </div>

            <form id="createRoleForm" action="{{ route('admin.roles.store') }}" method="POST" style="padding: 25px;">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="modal_name" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Nama Peran <span style="color: red;">*</span>
                    </label>
                    <input type="text" id="modal_name" name="name" placeholder="contoh: pembimbing_pkl, kepala_jurusan" 
                           style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" 
                           required>
                    <span style="color: #999; font-size: 12px; display: block; margin-top: 5px;">Gunakan huruf kecil dan garis bawah, misalnya `pembimbing_pkl`.</span>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="modal_description" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Deskripsi <span style="color: #999; font-size: 11px;">(opsional)</span>
                    </label>
                    <textarea id="modal_description" name="description" rows="3" placeholder="Masukkan deskripsi peran..." 
                              style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-family: inherit;"></textarea>
                </div>

                <div style="background: #f9fafb; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #93c5fd;">
                    <p style="margin: 0; color: #666; font-size: 12px;">
                        <i class="bi bi-lightbulb" style="margin-right: 5px;"></i>
                        Fitur bisa ditentukan sekarang atau nanti saat mengubah peran.
                    </p>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <button type="button" style="padding: 10px 16px; background: #e5e7eb; color: #374151; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;" onclick="closeCreateModal()">Batal</button>
                    <button type="submit" style="padding: 10px 16px; background: #0369a1; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 13px;">
                        <i class="bi bi-check-lg" style="margin-right: 5px;"></i>Simpan Peran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(button) {
            const selectedFeatures = button.dataset.roleFeatures ? JSON.parse(button.dataset.roleFeatures) : [];

            openEditModalFromData({
                id: button.dataset.roleId,
                name: button.dataset.roleName || '',
                description: button.dataset.roleDescription || '',
                updateUrl: button.dataset.roleUpdateUrl || '',
                features: selectedFeatures,
            });
        }

        function openEditModalFromData(roleData) {
            document.getElementById('edit_role_id').value = roleData.id || '';
            document.getElementById('editRoleForm').action = roleData.updateUrl || '';
            document.getElementById('edit_name').value = roleData.name || '';
            document.getElementById('edit_description').value = roleData.description || '';

            const selectedFeatures = new Set((roleData.features || []).map(String));
            document.querySelectorAll('#editModal input[name="features[]"]').forEach(function(checkbox) {
                checkbox.checked = selectedFeatures.has(checkbox.value);
            });

            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'flex';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
            document.getElementById('createRoleForm').reset();
        }

        // Close create modal when clicking outside
        document.getElementById('createModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateModal();
            }
        });

        // Close create modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateModal();
                closeEditModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        if (window.__openEditRoleData) {
            openEditModalFromData(window.__openEditRoleData);
        }
    </script>

    <div style="margin-top: 30px;">
        <h2 style="margin-bottom: 20px;"><i class="bi bi-info-circle" style="margin-right: 8px;"></i>Definisi Peran</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Student -->
            <div class="card" style="background: #f0fdf4;">
                <h3 style="color: #166534; margin-bottom: 10px;">
                    <i class="bi bi-mortarboard" style="margin-right: 5px;"></i>Siswa
                </h3>
                <p style="color: #166534; font-size: 14px; line-height: 1.6;">
                    • Check-in/Check-out functionality<br>
                    • Fill daily logbook entries<br>
                    • View guidance notes from supervisors
                </p>
            </div>

            <!-- Industry Supervisor -->
            <div class="card" style="background: #fef3c7;">
                <h3 style="color: #92400e; margin-bottom: 10px;">
                    <i class="bi bi-person-check" style="margin-right: 5px;"></i>Pembimbing PKL
                </h3>
                <p style="color: #92400e; font-size: 14px; line-height: 1.6;">
                    • Validate student attendance<br>
                    • Review and approve logbooks<br>
                    • Provide guidance notes
                </p>
            </div>

            <!-- Head of Department -->
            <div class="card" style="background: #ddd6fe;">
                <h3 style="color: #5b21b6; margin-bottom: 10px;">
                    <i class="bi bi-briefcase" style="margin-right: 5px;"></i>Kepala Jurusan
                </h3>
                <p style="color: #5b21b6; font-size: 14px; line-height: 1.6;">
                    • Weekly logbook review<br>
                    • Filter by department<br>
                    • View department reports
                </p>
            </div>

            <!-- Homeroom Teacher -->
            <div class="card" style="background: #fee2e2;">
                <h3 style="color: #991b1b; margin-bottom: 10px;">
                    <i class="bi bi-book" style="margin-right: 5px;"></i>Wali Kelas
                </h3>
                <p style="color: #991b1b; font-size: 14px; line-height: 1.6;">
                    • View student class data<br>
                    • Filter by class only<br>
                    • Monitor class attendance
                </p>
            </div>

            <!-- Principal -->
            <div class="card" style="background: #f0f9ff;">
                <h3 style="color: #0c4a6e; margin-bottom: 10px;">
                    <i class="bi bi-building" style="margin-right: 5px;"></i>Kepala Sekolah
                </h3>
                <p style="color: #0c4a6e; font-size: 14px; line-height: 1.6;">
                    • View all school data<br>
                    • Access all reports<br>
                    • Full system visibility
                </p>
            </div>

            <!-- Admin -->
            <div class="card" style="background: #f3e8ff;">
                <h3 style="color: #6b21a8; margin-bottom: 10px;">
                    <i class="bi bi-shield-lock" style="margin-right: 5px;"></i>Admin
                </h3>
                <p style="color: #6b21a8; font-size: 14px; line-height: 1.6;">
                    • Manage all roles<br>
                    • Manage users<br>
                    • Hierarchical filtering
                </p>
            </div>
        </div>
    </div>
@endsection

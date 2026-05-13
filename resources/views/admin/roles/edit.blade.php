@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Role</h1>
        <p>Edit detail peran: <strong>{{ \App\Models\Role::displayName($role->name) }}</strong></p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h4><i class="bi bi-exclamation-triangle-fill" style="margin-right: 8px;"></i>Validation Errors</h4>
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-title">
            <i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Informasi Peran
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('PUT')

            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    Ubah nama dan deskripsi peran sesuai kebutuhan.
                </p>
            </div>

            <div style="margin-bottom: 25px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                    Nama Peran <span style="color: red;">*</span>
                </label>
                <input type="text" id="name" name="name" placeholder="contoh: pembimbing_pkl, kepala_jurusan"
                       value="{{ old('name', $role->name) }}"
                       style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; @error('name') border-color: #dc2626; @enderror"
                       required>
                @error('name')
                    <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                @enderror
                <span style="color: #999; font-size: 12px; display: block; margin-top: 5px;">Gunakan huruf kecil dan garis bawah, misalnya `pembimbing_pkl`.</span>
            </div>

            <div style="margin-bottom: 25px;">
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                    Deskripsi <span style="color: #999; font-size: 11px;">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="4" placeholder="Masukkan deskripsi peran..."
                          style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-family: inherit; @error('description') border-color: #dc2626; @enderror">{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                    <i class="bi bi-check-circle" style="margin-right: 8px;"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.roles') }}" class="btn btn-secondary" style="padding: 10px 30px;">
                    <i class="bi bi-x-circle" style="margin-right: 8px;"></i>Batal
                </a>
            </div>
        </form>
    </div>
@endsection

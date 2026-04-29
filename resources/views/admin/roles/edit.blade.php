@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Role</h1>
        <p>Atur fitur untuk: <strong>{{ \App\Models\Role::displayName($role->name) }}</strong></p>
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
            <i class="bi bi-list-check" style="margin-right: 8px;"></i>Assign Features
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST" style="margin-top: 20px;">
            @csrf
            @method('PUT')

            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    Pilih fitur yang dapat diakses peran ini. Perubahan berlaku segera setelah disimpan.
                </p>
            </div>

            <!-- Feature Groups by Category -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 25px;">
                
                <!-- Student Features -->
                <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px;">
                        <i class="bi bi-mortarboard" style="margin-right: 8px; color: #166534;"></i>Fitur Siswa
                    </h3>
                    @foreach ($features->whereIn('slug', ['checkin_checkout', 'fill_logbook', 'view_guidance']) as $feature)
                        <div style="margin-bottom: 12px; display: flex; align-items: center;">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                id="feature_{{ $feature->id }}"
                                @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                <strong>{{ $feature->name }}</strong>
                                <br>
                                <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Supervisor Features -->
                <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 10px;">
                        <i class="bi bi-person-check" style="margin-right: 8px; color: #92400e;"></i>Fitur Pembimbing
                    </h3>
                    @foreach ($features->whereIn('slug', ['validate_attendance', 'validate_logbook', 'provide_guidance']) as $feature)
                        <div style="margin-bottom: 12px; display: flex; align-items: center;">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                id="feature_{{ $feature->id }}"
                                @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                <strong>{{ $feature->name }}</strong>
                                <br>
                                <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Management Features -->
                <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #e0e7ff; padding-bottom: 10px;">
                        <i class="bi bi-shield-lock" style="margin-right: 8px; color: #4338ca;"></i>Fitur Manajemen
                    </h3>
                    @foreach ($features->whereIn('slug', ['manage_roles', 'manage_users', 'manage_activities']) as $feature)
                        <div style="margin-bottom: 12px; display: flex; align-items: center;">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                id="feature_{{ $feature->id }}"
                                @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                <strong>{{ $feature->name }}</strong>
                                <br>
                                <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Administrative Features -->
                <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #ddd6fe; padding-bottom: 10px;">
                        <i class="bi bi-graph-up" style="margin-right: 8px; color: #5b21b6;"></i>Fitur Administratif
                    </h3>
                    @foreach ($features->whereIn('slug', ['view_all_data', 'view_reports', 'weekly_review']) as $feature)
                        <div style="margin-bottom: 12px; display: flex; align-items: center;">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                id="feature_{{ $feature->id }}"
                                @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                <strong>{{ $feature->name }}</strong>
                                <br>
                                <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <!-- Filtering Features -->
                <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fee2e2; padding-bottom: 10px;">
                        <i class="bi bi-funnel" style="margin-right: 8px; color: #991b1b;"></i>Fitur Penyaring
                    </h3>
                    @foreach ($features->whereIn('slug', ['department_filter', 'class_filter']) as $feature)
                        <div style="margin-bottom: 12px; display: flex; align-items: center;">
                            <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                id="feature_{{ $feature->id }}"
                                @if(in_array($feature->id, $selectedFeatures)) checked @endif
                                style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                <strong>{{ $feature->name }}</strong>
                                <br>
                                <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 25px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                    <i class="bi bi-check-circle" style="margin-right: 8px;"></i>Simpan Perubahan
                </button>
                <a href="{{ route('admin.roles') }}" class="btn btn-secondary" style="padding: 10px 30px;">
                    <i class="bi bi-x-circle" style="margin-right: 8px;"></i>Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Feature Summary -->
    <div class="card" style="margin-top: 30px;">
        <h2 style="margin-top: 0;"><i class="bi bi-list" style="margin-right: 8px;"></i>All Available Features</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
            @foreach ($features as $feature)
                <div style="border-left: 3px solid {{ in_array($feature->id, $selectedFeatures) ? '#10b981' : '#d1d5db' }}; padding: 12px 15px; background: {{ in_array($feature->id, $selectedFeatures) ? '#f0fdf4' : '#f9fafb' }}; border-radius: 4px;">
                    <h4 style="margin: 0 0 5px 0; color: #1f2937; display: flex; align-items: center;">
                        @if(in_array($feature->id, $selectedFeatures))
                            <i class="bi bi-check-circle-fill" style="margin-right: 8px; color: #10b981;"></i>
                        @else
                            <i class="bi bi-circle" style="margin-right: 8px; color: #9ca3af;"></i>
                        @endif
                        {{ $feature->name }}
                    </h4>
                    <p style="margin: 0; color: #666; font-size: 13px;">{{ $feature->description }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection

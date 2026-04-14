@extends('layouts.app')


@section('content')
    <div class="page-header">
        <h1><i class="bi bi-plus-circle" style="margin-right: 8px;"></i>Create New Role</h1>
        <p>Add a new role to the system</p>
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
            <i class="bi bi-shield-plus" style="margin-right: 8px;"></i>Role Information
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST" style="margin-top: 20px;">
            @csrf

            <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <p style="margin: 0; color: #666; font-size: 14px;">
                    <i class="bi bi-info-circle" style="margin-right: 5px;"></i>
                    Create a new role and assign features to it. You can modify features later from the role edit page.
                </p>
            </div>

            <!-- Basic Information Section -->
            <div style="margin-bottom: 25px;">
                <h3 style="margin-bottom: 15px; color: #1f2937; font-size: 16px;">
                    <i class="bi bi-info-circle-fill" style="margin-right: 8px; color: #0369a1;"></i>Basic Information
                </h3>

                <div>
                    <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Role Name <span style="color: red;">*</span>
                    </label>
                    <input type="text" id="name" name="name" placeholder="e.g., supervisor, coordinator" 
                           value="{{ old('name') }}"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; @error('name') border-color: #dc2626; @enderror" 
                           required>
                    @error('name')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                    <span style="color: #999; font-size: 12px; display: block; margin-top: 5px;">Use lowercase and underscores (e.g., field_supervisor)</span>
                </div>

                <div style="margin-top: 20px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px;">
                        Description <span style="color: #999; font-size: 11px;">(optional)</span>
                    </label>
                    <textarea id="description" name="description" rows="4" placeholder="Enter role description..." 
                              style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; font-family: inherit; @error('description') border-color: #dc2626; @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <span style="color: #dc2626; font-size: 12px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Features Assignment Section -->
            <div>
                <h3 style="margin-bottom: 15px; color: #1f2937; font-size: 16px;">
                    <i class="bi bi-list-check" style="margin-right: 8px; color: #10b981;"></i>Assign Features (Optional)
                </h3>

                <div style="background: #f9fafb; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; border-left: 3px solid #93c5fd;">
                    <p style="margin: 0; color: #666; font-size: 12px;">
                        <i class="bi bi-lightbulb" style="margin-right: 5px;"></i>
                        You can assign features now or later when editing the role. Features must be enabled in the Features Management page to be active.
                    </p>
                </div>

                <!-- Feature Groups by Category -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 25px;">
                    
                    <!-- Student Features -->
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #f0fdf4; padding-bottom: 10px;">
                            <i class="bi bi-mortarboard" style="margin-right: 8px; color: #166534;"></i>Student Features
                        </h4>
                        @foreach ($features->whereIn('slug', ['checkin_checkout', 'fill_logbook', 'view_guidance']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                    id="feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Supervisor Features -->
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fef3c7; padding-bottom: 10px;">
                            <i class="bi bi-person-check" style="margin-right: 8px; color: #92400e;"></i>Supervisor Features
                        </h4>
                        @foreach ($features->whereIn('slug', ['validate_attendance', 'validate_logbook', 'provide_guidance']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                    id="feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Management Features -->
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #e0e7ff; padding-bottom: 10px;">
                            <i class="bi bi-shield-lock" style="margin-right: 8px; color: #4338ca;"></i>Management Features
                        </h4>
                        @foreach ($features->whereIn('slug', ['manage_roles', 'manage_users', 'manage_activities']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                    id="feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Administrative Features -->
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #ddd6fe; padding-bottom: 10px;">
                            <i class="bi bi-graph-up" style="margin-right: 8px; color: #5b21b6;"></i>Administrative Features
                        </h4>
                        @foreach ($features->whereIn('slug', ['view_all_data', 'view_reports', 'weekly_review']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                    id="feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <!-- Filtering Features -->
                    <div style="border: 1px solid #e5e7eb; padding: 20px; border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #1f2937; border-bottom: 2px solid #fee2e2; padding-bottom: 10px;">
                            <i class="bi bi-funnel" style="margin-right: 8px; color: #991b1b;"></i>Filtering Features
                        </h4>
                        @foreach ($features->whereIn('slug', ['department_filter', 'class_filter']) as $feature)
                            <div style="margin-bottom: 12px; display: flex; align-items: flex-start;">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                                    id="feature_{{ $feature->id }}"
                                    style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;">
                                <label for="feature_{{ $feature->id }}" style="margin-left: 10px; cursor: pointer; flex: 1;">
                                    <strong style="display: block; margin-bottom: 3px;">{{ $feature->name }}</strong>
                                    <span style="color: #999; font-size: 12px;">{{ $feature->description }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 30px;">
                    <i class="bi bi-check-circle" style="margin-right: 8px;"></i>Create Role
                </button>
                <a href="{{ route('admin.roles') }}" class="btn btn-secondary" style="padding: 10px 30px;">
                    <i class="bi bi-x-circle" style="margin-right: 8px;"></i>Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

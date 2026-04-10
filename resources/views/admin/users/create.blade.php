@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-person-plus" style="margin-right: 8px;"></i>Create New User</h1>
        <p>Add a new user to the system</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill" style="margin-right: 8px;"></i>
            <strong>Validation errors:</strong>
            <ul style="margin-top: 10px; margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="margin-bottom: 20px;">User Information</h2>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <!-- Name -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Name <span style="color: red;">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" 
                    class="form-control" placeholder="Enter full name" required>
            </div>

            <!-- Email -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Email <span style="color: red;">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                    class="form-control" placeholder="Enter email address" required>
            </div>

            <!-- Password -->
            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Password <span style="color: red;">*</span>
                </label>
                <input type="password" id="password" name="password" 
                    class="form-control" placeholder="Enter password (min 8 characters)" required>
            </div>

            <!-- Confirm Password -->
            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Confirm Password <span style="color: red;">*</span>
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" 
                    class="form-control" placeholder="Confirm password" required>
            </div>

            <!-- Phone -->
            <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Phone <span style="color: #999;">(optional)</span>
                </label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                    class="form-control" placeholder="Enter phone number">
            </div>

            <!-- Address -->
            <div style="margin-bottom: 20px;">
                <label for="address" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Address <span style="color: #999;">(optional)</span>
                </label>
                <textarea id="address" name="address" rows="3" class="form-control" 
                    placeholder="Enter address">{{ old('address') }}</textarea>
            </div>

            <!-- Role -->
            <div style="margin-bottom: 20px;">
                <label for="role_id" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Role <span style="color: red;">*</span>
                </label>
                <select id="role_id" name="role_id" class="form-control" required>
                    <option value="">Select a role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div style="margin-bottom: 20px;">
                <label for="status" style="display: block; margin-bottom: 8px; font-weight: 600;">
                    Status <span style="color: red;">*</span>
                </label>
                <select id="status" name="status" class="form-control" required>
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1; padding: 10px;">
                    <i class="bi bi-check-circle" style="margin-right: 5px;"></i>Create User
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary" style="flex: 1; padding: 10px; text-align: center;">
                    <i class="bi bi-x-circle" style="margin-right: 5px;"></i>Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

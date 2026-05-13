@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <h1 style="margin: 0; color: #1f2937; font-size: 28px;">
            <i class="bi bi-pencil-square" style="margin-right: 10px; color: #f97316;"></i>Edit Level
        </h1>
        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Update level details</p>
    </div>

    @if ($errors->any())
        <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.levels.update', $level) }}" method="POST" style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px;">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #1f2937;">
                Level Name <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" name="name" placeholder="e.g., Kesiswaan" value="{{ old('name', $level->name) }}" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #1f2937;">
                Description
            </label>
            <textarea name="description" placeholder="Description for this level" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; resize: vertical; min-height: 100px;">{{ old('description', $level->description) }}</textarea>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; color: #1f2937;">
                Status <span style="color: #ef4444;">*</span>
            </label>
            <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;" required>
                <option value="active" {{ old('status', $level->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $level->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <a href="{{ route('admin.levels.index') }}" class="btn" style="background: #e5e7eb; color: #1f2937; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="bi bi-arrow-left"></i>Back
            </a>
            <button type="submit" class="btn" style="background: #f97316; color: white; padding: 10px 20px; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="bi bi-check-circle"></i>Update Level
            </button>
        </div>
    </form>
</div>
@endsection

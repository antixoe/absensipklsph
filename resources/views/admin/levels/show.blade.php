@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <h1 style="margin: 0; color: #1f2937; font-size: 28px;">
            <i class="bi bi-layers" style="margin-right: 10px; color: #f97316;"></i>{{ $level->name }}
        </h1>
        <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 14px;">Level Details</p>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Name</label>
                <p style="margin: 0; font-size: 14px; font-weight: 500;">{{ $level->name }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Status</label>
                <span style="display: inline-block; background: {{ $level->status === 'active' ? '#dcfce7' : '#fee2e2' }}; color: {{ $level->status === 'active' ? '#166534' : '#991b1b' }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    {{ ucfirst($level->status) }}
                </span>
            </div>
            <div style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Description</label>
                <p style="margin: 0; font-size: 14px; font-weight: 500; white-space: pre-wrap;">{{ $level->description ?? '-' }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Created</label>
                <p style="margin: 0; font-size: 14px; font-weight: 500;">{{ $level->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; color: #666; font-size: 12px; font-weight: 600;">Updated</label>
                <p style="margin: 0; font-size: 14px; font-weight: 500;">{{ $level->updated_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.levels.edit', $level) }}" class="btn" style="background: #fef3c7; color: #b45309; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="bi bi-pencil-square"></i>Edit
            </a>
            <a href="{{ route('admin.levels.index') }}" class="btn" style="background: #e5e7eb; color: #1f2937; padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="bi bi-arrow-left"></i>Back
            </a>
        </div>
    </div>
</div>
@endsection

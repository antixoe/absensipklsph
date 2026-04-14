@extends('layouts.app')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil-square" style="margin-right: 8px;"></i>Edit Document</h1>
    </div>

    <form method="POST" action="{{ route('documents.update', $document) }}" class="form-container">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Title <span style="color: #dc2626;">*</span></label>
            <input type="text" name="title" value="{{ old('title', $document->document_name) }}" required style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
            @error('title')
                <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px; resize: vertical;" rows="4">{{ old('description', $document->description) }}</textarea>
            @error('description')
                <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label>Document Type</label>
            <select name="document_type" style="width: 100%; padding: 10px; border: 2px solid #e5e7eb; border-radius: 6px; font-size: 14px;">
                <option value="">-- Select Type --</option>
                <option value="report" @selected(old('document_type', $document->document_type) === 'report')>Report</option>
                <option value="certificate" @selected(old('document_type', $document->document_type) === 'certificate')>Certificate</option>
                <option value="letter" @selected(old('document_type', $document->document_type) === 'letter')>Letter</option>
                <option value="assignment" @selected(old('document_type', $document->document_type) === 'assignment')>Assignment</option>
                <option value="other" @selected(old('document_type', $document->document_type) === 'other')>Other</option>
            </select>
            @error('document_type')
                <p style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="padding: 12px; background: #f0f9ff; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #3b82f6;">
            <p style="color: #666; font-size: 12px; margin-bottom: 4px;">Current File</p>
            <p style="font-weight: 600;">{{ $document->file_name }}</p>
            <p style="color: #666; font-size: 12px;">{{ number_format($document->file_size / 1024, 2) }} KB • {{ $document->created_at->format('M d, Y') }}</p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" style="flex: 1; padding: 12px 20px; background: #f97316; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Update Document</button>
            <a href="{{ route('documents.index') }}" style="flex: 1; padding: 12px 20px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none;">Cancel</a>
        </div>
    </form>
@endsection

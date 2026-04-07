@extends('layouts.app')

@section('title', 'Document Details')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-text" style="margin-right: 8px;"></i>{{ $document->document_name }}</h1>
        <p>{{ $document->document_type ? ucfirst($document->document_type) : 'General Document' }} • Uploaded {{ $document->created_at->format('F d, Y') }}</p>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">Status</p>
                <p style="font-weight: 600; font-size: 18px; color: 
                    @if ($document->status === 'approved') #10b981 @else #f59e0b @endif;">
                    {{ ucfirst($document->status) }}
                </p>
            </div>
            <div>
                <p style="color: #666; font-size: 14px; margin-bottom: 4px;">File Size</p>
                <p style="font-weight: 600; font-size: 18px;">{{ number_format($document->file_size / 1024, 2) }} KB</p>
            </div>
        </div>

        <div style="margin-bottom: 20px; padding: 15px; background: #f0f9ff; border-radius: 6px;">
            <p style="color: #666; font-size: 14px; margin-bottom: 8px;">File Information</p>
            <p><strong>Name:</strong> {{ $document->file_name }}</p>
            <p style="margin-top: 8px;"><strong>Type:</strong> {{ $document->mime_type ?? 'Unknown' }}</p>
            <p style="margin-top: 8px;"><strong>Uploaded:</strong> {{ $document->created_at->format('F d, Y \a\t g:i A') }}</p>
        </div>

        @if ($document->description)
            <div style="margin-bottom: 20px; padding: 15px; background: #f0fdf4; border-radius: 6px;">
                <p style="color: #666; font-size: 14px; margin-bottom: 8px;">Description</p>
                <p style="line-height: 1.6;">{{ $document->description }}</p>
            </div>
        @endif

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <a href="{{ route('documents.download', $document) }}" class="btn" style="flex: 1; text-align: center; background: #3b82f6;"><i class="bi bi-download" style="margin-right: 5px;"></i>Download File</a>
            <a href="{{ route('documents.edit', $document) }}" class="btn" style="flex: 1; text-align: center;">Edit</a>
            <form method="POST" action="{{ route('documents.destroy', $document) }}" style="flex: 1;" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Delete</button>
            </form>
            <a href="{{ route('documents.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Back to List</a>
        </div>
    </div>
@endsection

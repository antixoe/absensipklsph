@extends('layouts.app')

@section('title', 'Documents')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-text" style="margin-right: 8px;"></i>Documents</h1>
        <a href="{{ route('documents.create') }}" class="btn">+ Upload New</a>
    </div>

    @if ($documents->count() > 0)
        <table class="list-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Status</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td>{{ $document->document_name }}</td>
                        <td>{{ $document->document_type ?? 'General' }}</td>
                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                        <td>
                            <span style="
                                display: inline-block;
                                padding: 4px 12px;
                                border-radius: 20px;
                                font-size: 12px;
                                font-weight: 600;
                                background: @if ($document->status === 'approved')
                                    #dcfce7; color: #166534;
                                @elseif ($document->status === 'pending')
                                    #fef3c7; color: #92400e;
                                @else
                                    #fee2e2; color: #991b1b;
                                @endif
                            ">
                                {{ ucfirst($document->status) }}
                            </span>
                        </td>
                        <td>{{ $document->created_at->format('M d, Y') }}</td>
                        <td style="text-align: center; gap: 8px; display: flex; justify-content: center;">
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-sm" style="padding: 6px 12px; font-size: 12px;">View</a>
                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm" style="padding: 6px 12px; font-size: 12px; background: #3b82f6;">Download</a>
                            <form method="POST" action="{{ route('documents.destroy', $document) }}" style="display: inline;" onsubmit="return confirm('Delete this document?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="padding: 6px 12px; font-size: 12px; background: #dc2626;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $documents->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>No documents uploaded yet.</p>
            <a href="{{ route('documents.create') }}" class="btn">Upload First Document</a>
        </div>
    @endif
@endsection

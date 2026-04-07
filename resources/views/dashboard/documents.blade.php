@extends('layouts.app')

@section('title', 'Documents')

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-text" style="margin-right: 8px;"></i>Documents</h1>
        <p>Upload and manage your documents</p>
    </div>

    <div class="card">
        <div class="card-title">Upload Document</div>
        <form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
            @csrf
            <div>
                <label>Document Title</label>
                <input type="text" placeholder="e.g., Final Report" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <div>
                <label>Select File</label>
                <input type="file" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>
            <button type="submit" class="btn">Upload</button>
        </form>
    </div>

    <div class="card">
        <div class="card-title">My Documents</div>
        <p>Your uploaded documents will appear here...</p>
    </div>
@endsection

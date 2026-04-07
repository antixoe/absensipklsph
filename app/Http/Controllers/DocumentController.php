<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $documents = Document::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'file' => ['required', 'file', 'max:10240'], // 10MB max
            'document_type' => ['required', 'string', 'max:100'],
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        Document::create([
            'student_id' => $student->id,
            'document_name' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'document_type' => $validated['document_type'],
            'status' => 'pending',
            'upload_date' => now()->toDateString(),
        ]);

        return redirect('/documents')->with('success', 'Document uploaded successfully!');
    }

    public function show(Document $document)
    {
        $student = Auth::user()->student;
        if ($document->student_id !== $student->id) {
            abort(403);
        }
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $student = Auth::user()->student;
        if ($document->student_id !== $student->id) {
            abort(403);
        }
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $student = Auth::user()->student;
        if ($document->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'document_type' => ['required', 'string', 'max:100'],
        ]);

        $document->update([
            'document_name' => $validated['title'],
            'description' => $validated['description'],
            'document_type' => $validated['document_type'],
        ]);

        return redirect('/documents')->with('success', 'Document updated successfully!');
    }

    public function destroy(Document $document)
    {
        $student = Auth::user()->student;
        if ($document->student_id !== $student->id) {
            abort(403);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        return redirect('/documents')->with('success', 'Document deleted successfully!');
    }

    public function download(Document $document)
    {
        $student = Auth::user()->student;
        if ($document->student_id !== $student->id) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}

    public function download(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect('/documents')->with('error', 'File not found!');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}

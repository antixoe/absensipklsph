<?php

namespace App\Http\Controllers\Api;

use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends \Illuminate\Routing\Controller
{
    /**
     * Get all documents for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Document::query();

        // Filter by student if the user is a student
        if ($user->role_id == 2) { // Student role
            $student = $user->student;
            $query->where('student_id', $student->id);
        }

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('type')) {
            $query->where('document_type', $request->type);
        }

        $documents = $query->with('student.user', 'reviewedBy.user')
            ->orderBy('upload_date', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json($documents);
    }

    /**
     * Store a new document.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        $request->validate([
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_type' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,gif,jpeg|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('documents', 'public');

        $document = Document::create([
            'student_id' => $student->id,
            'document_name' => $request->document_name,
            'description' => $request->description,
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'upload_date' => today(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => $document,
        ], 201);
    }

    /**
     * Get a specific document.
     */
    public function show(Document $document)
    {
        return response()->json($document->load('student.user', 'reviewedBy.user'));
    }

    /**
     * Update a document.
     */
    public function update(Request $request, Document $document)
    {
        // Only allow updating if status is pending
        if ($document->status !== 'pending') {
            return response()->json([
                'message' => 'Can only edit pending documents',
            ], 403);
        }

        $request->validate([
            'document_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,png,gif,jpeg|max:10240',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file_path);

            $file = $request->file('file');
            $filePath = $file->store('documents', 'public');

            $document->update([
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        $document->update($request->only(['document_name', 'description']));

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => $document,
        ]);
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        // Only allow deleting if status is pending
        if ($document->status !== 'pending') {
            return response()->json([
                'message' => 'Can only delete pending documents',
            ], 403);
        }

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }

    /**
     * Approve a document (instructor only).
     */
    public function approve(Request $request, Document $document)
    {
        $user = $request->user();
        if ($user->role_id != 3) { // Instructor role
            return response()->json([
                'message' => 'Only instructors can approve documents',
            ], 403);
        }

        $request->validate([
            'review_notes' => 'nullable|string',
        ]);

        $document->update([
            'status' => 'approved',
            'reviewed_by' => $user->instructor->id,
            'review_notes' => $request->review_notes,
            'review_date' => today(),
        ]);

        // TODO: Send notification to student

        return response()->json([
            'message' => 'Document approved successfully',
            'data' => $document,
        ]);
    }

    /**
     * Reject a document (instructor only).
     */
    public function reject(Request $request, Document $document)
    {
        $user = $request->user();
        if ($user->role_id != 3) { // Instructor role
            return response()->json([
                'message' => 'Only instructors can reject documents',
            ], 403);
        }

        $request->validate([
            'review_notes' => 'required|string',
        ]);

        $document->update([
            'status' => 'rejected',
            'reviewed_by' => $user->instructor->id,
            'review_notes' => $request->review_notes,
            'review_date' => today(),
        ]);

        // TODO: Send notification to student

        return response()->json([
            'message' => 'Document rejected',
            'data' => $document,
        ]);
    }

    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'message' => 'File not found',
            ], 404);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->file_name
        );
    }
}

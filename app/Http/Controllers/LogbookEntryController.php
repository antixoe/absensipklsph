<?php

namespace App\Http\Controllers;

use App\Models\LogbookEntry;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookEntryController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $entries = LogbookEntry::where('student_id', $student->id)
            ->orderBy('entry_date', 'desc')
            ->paginate(10);
        
        return view('logbook.index', compact('entries'));
    }

    public function create()
    {
        return view('logbook.create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;
        if (!$student) {
            abort(403, 'Student profile not found');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'achievements' => ['nullable', 'string', 'max:1000'],
            'challenges' => ['nullable', 'string', 'max:1000'],
            'learning_outcomes' => ['nullable', 'string', 'max:1000'],
            'hours_worked' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        $entry = LogbookEntry::create([
            'student_id' => $student->id,
            'entry_date' => $validated['date'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'achievements' => $validated['achievements'],
            'challenges' => $validated['challenges'],
            'learning_outcomes' => $validated['learning_outcomes'],
            'hours_worked' => $validated['hours_worked'],
            'status' => 'draft',
        ]);

        // Log the activity
        ActivityLoggerService::log(
            'created_logbook_entry',
            'logbook_entry',
            $entry->id,
            "Created logbook entry '{$validated['title']}' for {$validated['date']}"
        );

        return redirect('/logbook')->with('success', 'Logbook entry created successfully!');
    }

    public function show(LogbookEntry $entry)
    {
        $student = Auth::user()->student;
        if ($entry->student_id !== $student->id) {
            abort(403);
        }
        return view('logbook.show', compact('entry'));
    }

    public function edit(LogbookEntry $entry)
    {
        $student = Auth::user()->student;
        if ($entry->student_id !== $student->id) {
            abort(403);
        }
        return view('logbook.edit', compact('entry'));
    }

    public function update(Request $request, LogbookEntry $entry)
    {
        $student = Auth::user()->student;
        if ($entry->student_id !== $student->id) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'achievements' => ['nullable', 'string', 'max:1000'],
            'challenges' => ['nullable', 'string', 'max:1000'],
            'learning_outcomes' => ['nullable', 'string', 'max:1000'],
            'hours_worked' => ['required', 'numeric', 'min:0', 'max:24'],
        ]);

        $oldData = [
            'entry_date' => $entry->entry_date->toDateString(),
            'title' => $entry->title,
            'description' => $entry->description,
            'achievements' => $entry->achievements,
            'challenges' => $entry->challenges,
            'learning_outcomes' => $entry->learning_outcomes,
            'hours_worked' => $entry->hours_worked,
        ];

        $entry->update([
            'entry_date' => $validated['date'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'achievements' => $validated['achievements'],
            'challenges' => $validated['challenges'],
            'learning_outcomes' => $validated['learning_outcomes'],
            'hours_worked' => $validated['hours_worked'],
        ]);

        // Log the activity
        ActivityLoggerService::logChange(
            'updated_logbook_entry',
            'logbook_entry',
            $entry->id,
            $oldData,
            $validated,
            "Updated logbook entry '{$validated['title']}'"
        );

        return redirect('/logbook')->with('success', 'Logbook entry updated successfully!');
    }

    public function destroy(LogbookEntry $entry)
    {
        $student = Auth::user()->student;
        if ($entry->student_id !== $student->id) {
            abort(403);
        }

        $entryTitle = $entry->title;
        $entryId = $entry->id;

        $entry->delete();

        // Log the activity
        ActivityLoggerService::log(
            'deleted_logbook_entry',
            'logbook_entry',
            $entryId,
            "Deleted logbook entry '{$entryTitle}'"
        );

        return redirect('/logbook')->with('success', 'Logbook entry deleted successfully!');
    }

    public function submit(LogbookEntry $entry)
    {
        $student = Auth::user()->student;
        if ($entry->student_id !== $student->id) {
            abort(403);
        }

        $entry->update(['status' => 'submitted']);

        // Log the activity
        ActivityLoggerService::log(
            'submitted_logbook_entry',
            'logbook_entry',
            $entry->id,
            "Submitted logbook entry '{$entry->title}' for approval"
        );

        return redirect('/logbook')->with('success', 'Logbook entry submitted for approval!');
    }
}

<?php

namespace App\Http\Controllers\Examples;

use App\Models\Absence;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;

/**
 * EXAMPLE: How to use ActivityLoggerService in Controllers
 * 
 * This file demonstrates best practices for logging activities
 * in your application. Copy these patterns to your own controllers.
 */
class ExampleActivityLoggingController
{
    /**
     * Example 1: Log a simple view/access
     */
    public function viewAbsence(Absence $absence)
    {
        ActivityLoggerService::logView('absence.show', $absence->id);
        
        return view('absence.show', compact('absence'));
    }

    /**
     * Example 2: Log a create action with captured data
     */
    public function storeAbsence(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'absence_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $absence = Absence::create($validated);

        // Log the creation with all the data that was created
        ActivityLoggerService::logCreate(
            'absence',
            $absence->id,
            $validated, // The data that was created
            "Created absence record for student {$absence->student->user->name}"
        );

        return redirect()->route('absence.show', $absence)->with('success', 'Absence recorded');
    }

    /**
     * Example 3: Log an update action with before/after data
     */
    public function updateAbsence(Request $request, Absence $absence)
    {
        $oldData = [
            'status' => $absence->status,
            'notes' => $absence->notes,
        ];

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $absence->update($validated);

        // Log the update with old and new values
        ActivityLoggerService::logUpdate(
            'absence',
            $absence->id,
            $oldData,
            $validated,
            "Updated absence record - Status changed from {$oldData['status']} to {$validated['status']}"
        );

        return redirect()->back()->with('success', 'Absence updated');
    }

    /**
     * Example 4: Log an approval action
     */
    public function approveAbsence(Absence $absence)
    {
        $oldStatus = $absence->status;
        $absence->update(['status' => 'approved']);

        ActivityLoggerService::logApproved(
            'absence',
            $absence->id,
            "Approved absence for {$absence->student->user->name} (was $oldStatus)"
        );

        return redirect()->back()->with('success', 'Absence approved');
    }

    /**
     * Example 5: Log a rejection action
     */
    public function rejectAbsence(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'reject_reason' => 'required|string|max:500',
        ]);

        $oldStatus = $absence->status;
        $absence->update(['status' => 'rejected']);

        ActivityLoggerService::logRejected(
            'absence',
            $absence->id,
            "Rejected absence for {$absence->student->user->name} - Reason: {$validated['reject_reason']}"
        );

        return redirect()->back()->with('success', 'Absence rejected');
    }

    /**
     * Example 6: Log a delete action (soft delete in model)
     */
    public function deleteAbsence(Absence $absence)
    {
        $deletedData = [
            'id' => $absence->id,
            'student_id' => $absence->student_id,
            'status' => $absence->status,
            'notes' => $absence->notes,
        ];

        $absence->delete(); // Soft delete

        ActivityLoggerService::logDelete(
            'absence',
            $absence->id,
            $deletedData,
            "Deleted absence record for {$absence->student->user->name}"
        );

        return redirect()->back()->with('success', 'Absence deleted');
    }

    /**
     * Example 7: Log a bulk action
     */
    public function bulkApproveAbsences(Request $request)
    {
        $validated = $request->validate([
            'absence_ids' => 'required|array',
            'absence_ids.*' => 'exists:absences,id',
        ]);

        $count = 0;
        foreach ($validated['absence_ids'] as $absenceId) {
            $absence = Absence::find($absenceId);
            if ($absence) {
                $absence->update(['status' => 'approved']);
                $count++;

                // Log each approval separately for audit trail
                ActivityLoggerService::logApproved(
                    'absence',
                    $absence->id,
                    "Bulk approved: {$absence->student->user->name}"
                );
            }
        }

        return redirect()->back()->with('success', "$count absences approved");
    }

    /**
     * Example 8: Log custom action with arbitrary data
     */
    public function exportAbsenceReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $absences = Absence::whereBetween('absence_date', [$startDate, $endDate])->get();

        // Log the export action with details
        ActivityLoggerService::log(
            'exported_report',
            'absence',
            null,
            "Exported absence report from $startDate to $endDate ({$absences->count()} records)"
        );

        // Generate and return the report...
        return response()->json(['count' => $absences->count()]);
    }

    /**
     * Example 9: Log action with location data
     * 
     * If you want to capture location when submitting,
     * send it with the request:
     */
    public function storeWithLocation(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'absence_date' => 'required|date',
            // Location fields (optional)
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_name' => 'nullable|string',
            'location_city' => 'nullable|string',
            'location_country' => 'nullable|string',
        ]);

        $absence = Absence::create($validated);

        // The ActivityLog::log() method will automatically capture
        // location_name, location_city, location_country from request input
        ActivityLoggerService::logCreate(
            'absence',
            $absence->id,
            $validated,
            "Created absence from {$validated['location_name'] ?? 'unknown location'}"
        );

        return redirect()->back()->with('success', 'Absence recorded with location');
    }
}

/**
 * USAGE IN JAVASCRIPT (for location data)
 * 
 * Use geolocation API to send location:
 * 
 * if (navigator.geolocation) {
 *     navigator.geolocation.getCurrentPosition(function(position) {
 *         document.querySelector('[name="latitude"]').value = position.coords.latitude;
 *         document.querySelector('[name="longitude"]').value = position.coords.longitude;
 *         
 *         // Optional: Reverse geocode to get city/country
 *         // You'll need an API for this (Google Geocoding API, etc)
 *     });
 * }
 */

/**
 * BEST PRACTICES
 * 
 * 1. Always log before/after on updates
 *    - Helps track exactly what changed
 *    - Useful for audits and compliance
 * 
 * 2. Include meaningful descriptions
 *    - Include user names, dates, reasons
 *    - Make it easy to understand what happened
 * 
 * 3. Use appropriate log methods
 *    - logCreate() for creates
 *    - logUpdate() for updates
 *    - logDelete() for deletes
 *    - logApproved() for approvals
 *    - log() for generic actions
 * 
 * 4. Log after successful action
 *    - Only log if the operation succeeded
 *    - Use try/catch or check for success
 * 
 * 5. Don't log sensitive data
 *    - Skip passwords, API keys, etc.
 *    - Be mindful of PII (Personally Identifiable Information)
 * 
 * 6. Group related operations
 *    - Log bulk operations individually
 *    - Makes audit trail clearer
 * 
 * 7. Use consistent naming
 *    - Use snake_case for action names
 *    - Use singular for subject names
 *    - Examples: 'created', 'updated', 'deleted', 'approved', 'rejected'
 */

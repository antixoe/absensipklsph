# Activity Logging System Documentation

## Overview

The Activity Logging System provides comprehensive tracking of all user actions in the Absensi PKL application. It includes soft delete functionality to allow recovery of deleted logs, and captures detailed information including:

- **User Information**: Who performed the action
- **Action Details**: What action was performed (create, update, delete, approve, reject, login, logout, etc.)
- **Timestamp**: Exact date and time of the action
- **IP Address**: User's IP address for security tracking
- **Location Data**: Geographic location details (city, country, coordinates if available)
- **Device Information**: Device type (mobile, tablet, desktop), browser, and operating system
- **Data Changes**: Old values and new values for updates (tracks what changed)
- **HTTP Method**: GET, POST, PUT, DELETE, etc.
- **URL Path**: Which page/endpoint was accessed

## Database Schema

The `activity_logs` table contains the following columns:

```
id                  - Primary key
user_id             - Foreign key to users table
action              - Type of action performed
subject             - What was affected (absence, user, document, etc.)
subject_id          - ID of the affected record
description         - Human-readable description
ip_address          - User's IP address
user_agent          - Browser user agent string
latitude            - Geographic latitude (if available)
longitude           - Geographic longitude (if available)
location_name       - Specific location name
location_city       - City name
location_country    - Country name
device_type         - mobile|tablet|desktop
browser             - Chrome|Firefox|Safari|Edge|Opera|Other
operating_system    - Windows|macOS|Linux|iOS|Android|Other
method              - HTTP method (GET, POST, PUT, DELETE, etc.)
url_path            - Requested URL path
old_values          - JSON of old data (for updates)
new_values          - JSON of new data (for updates/creates)
created_at          - When the action occurred
updated_at          - Last update timestamp
deleted_at          - When soft-deleted (NULL if not deleted)
```

## Using the Activity Logger

### Method 1: Using the Static ActivityLog::log() Method

The simplest way to log an activity:

```php
use App\Models\ActivityLog;

ActivityLog::log(
    'action_name',           // required: what happened
    'subject_type',          // optional: what was affected
    $subjectId,              // optional: ID of affected record
    'Human readable message' // optional: description
);
```

**Example:**
```php
ActivityLog::log(
    'approved',
    'absence',
    $absence->id,
    "Approved absence for student {$absence->student->user->name}"
);
```

### Method 2: Using the ActivityLoggerService

For more organized and maintainable code, use the `ActivityLoggerService`:

```php
use App\Services\ActivityLoggerService;

// Simple log
ActivityLoggerService::log('viewed_page', 'page', null, 'Visited dashboard');

// Log view access
ActivityLoggerService::logView('dashboard');

// Log create with data
$data = ['name' => 'John', 'email' => 'john@example.com'];
ActivityLoggerService::logCreate('user', $userId, $data);

// Log update with before/after data
$oldData = ['status' => 'pending'];
$newData = ['status' => 'approved'];
ActivityLoggerService::logUpdate('absence', $absenceId, $oldData, $newData);

// Log delete
$deletedData = ['status' => 'active'];
ActivityLoggerService::logDelete('user', $userId, $deletedData);

// Approve/Reject
ActivityLoggerService::logApproved('absence', $absenceId);
ActivityLoggerService::logRejected('absence', $absenceId);

// Login/Logout (automatic, but available if needed)
ActivityLoggerService::logLogin();
ActivityLoggerService::logLogout();
```

## Capturing Location Data

To capture location information, send location data with your requests:

```javascript
// JavaScript example
fetch('/api/absence/submit', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        // ... other data
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        location_name: 'PKL Office - Building A',
        location_city: 'Jakarta',
        location_country: 'Indonesia'
    })
});
```

Or in HTML forms:
```html
<input type="hidden" name="latitude" value="">
<input type="hidden" name="longitude" value="">
<input type="hidden" name="location_name" value="">
<input type="hidden" name="location_city" value="">
<input type="hidden" name="location_country" value="">
```

## Automatic Logging Features

### Login/Logout Tracking
Login and logout events are automatically logged via the `AppServiceProvider`:
- Captures when users log in
- Captures when users log out
- Includes IP address, device type, browser, and OS

### Request Method Tracking
The system automatically captures:
- HTTP method (GET, POST, PUT, DELETE, PATCH)
- URL path accessed
- User agent and device information

## Viewing Activity Logs

### Admin Dashboard
Navigate to **Settings > Activity Log** to view:
- All user activities with detailed information
- Statistics: Total actions, today's actions, your actions
- Filter, search, and export functionality
- Soft delete individual logs
- Clear all logs (moves to trash)

### Trash Management
Navigate to **Settings > Trash** (admin only) to:
- View all soft-deleted logs
- Restore accidentally deleted logs
- Permanently delete logs (hard delete)
- Empty entire trash
- View deletion timestamp and who performed the action

## Soft Delete vs Hard Delete

### Soft Delete
- Logs are marked as deleted (sets `deleted_at` timestamp)
- Logs are hidden from main activity log view
- Logs can be restored anytime
- Original timestamps preserved
- Used in "Clear All" and individual log deletion

### Hard Delete
- Logs are permanently removed from database
- Cannot be recovered
- Use "Empty Trash" or "Permanently Delete" on trash page
- Final action with data loss

## Querying Activity Logs

```php
use App\Models\ActivityLog;

// Get all active logs (not deleted)
$logs = ActivityLog::active()->get();

// Get deleted logs (trash)
$logs = ActivityLog::deleted()->get();

// Get logs for specific user
$logs = ActivityLog::forUser($userId)->get();

// Get logs for specific date
$logs = ActivityLog::forDate(now())->get();

// Get logs for specific action
$logs = ActivityLog::forAction('approved')->get();

// Get all including deleted
$logs = ActivityLog::withTrashed()->get();

// Get only deleted
$logs = ActivityLog::onlyTrashed()->get();

// Restore a soft-deleted log
$log = ActivityLog::withTrashed()->find($id);
$log->restore();

// Permanently delete
$log = ActivityLog::withTrashed()->find($id);
$log->forceDelete();
```

## Scopes Available

- `active()` - Only active (non-deleted) logs
- `deleted()` - Only soft-deleted logs
- `forDate($date)` - Logs from specific date
- `forUser($userId)` - Logs for specific user
- `forAction($action)` - Logs for specific action
- `withTrashed()` - Include deleted logs in query
- `onlyTrashed()` - Only deleted logs

## Attributes Available on ActivityLog Model

```php
$log->user               // Related User model
$log->full_location      // Formatted location string
$log->device_info        // Formatted device/browser/OS string
$log->created_at         // Timestamp of action
$log->deleted_at         // Timestamp of deletion (if soft-deleted)
$log->ip_address         // IP address
$log->latitude           // Geographic latitude
$log->longitude          // Geographic longitude
$log->old_values         // JSON of previous values
$log->new_values         // JSON of new values
```

## Example: Log an Absence Approval

```php
// In AbsenceController
$oldData = ['status' => $absence->status];
$newData = ['status' => 'approved'];

$absence->update(['status' => 'approved']);

ActivityLoggerService::logUpdate(
    'absence',
    $absence->id,
    $oldData,
    $newData,
    "Approved absence for {$absence->student->user->name} on {$absence->absence_date->format('Y-m-d')}"
);
```

## CSV Export

Admins can export all activity logs as CSV from Settings page:
- Includes all columns: date, time, user, action, location, device, browser, OS, IP
- Clickable from the Settings > Activity Log page
- Named with timestamp: `activity-logs-2026-04-15-120530.csv`

## Security Considerations

1. **Access Control**: Only admins can view/manage activity logs
2. **IP Tracking**: All actions include IP addresses for audit trails
3. **Data Changes**: Track what changed (old_values vs new_values)
4. **Timestamps**: Precise timestamps for accountability
5. **Soft Deletes**: Recovery capability prevents accidental permanent loss
6. **Location Tracking**: Geographic information for security monitoring

## Best Practices

1. **Always log important actions**: Create, update, delete, approve, reject
2. **Provide meaningful descriptions**: Help admins understand what happened
3. **Include old/new values**: For updates, always log before and after values
4. **Use the right method**: 
   - `ActivityLog::log()` for simple cases
   - `ActivityLoggerService::logUpdate()` for updates
   - `ActivityLoggerService::logCreate()` for creates
   - `ActivityLoggerService::logDelete()` for deletes
5. **Clean up trash regularly**: Empty trash to save database space
6. **Review logs regularly**: Check for suspicious activities

## Troubleshooting

### Logs not appearing
- Check if `activity_logs` table exists: Run migrations with `php artisan migrate`
- Verify user is authenticated: Logs require `auth()->id()` to be set
- Check database connection and user_id foreign key constraint

### Location data not captured
- Ensure location fields are sent in request: `latitude`, `longitude`, `location_name`, etc.
- Verify fields are in request input: `request()->input('latitude')`
- Check browser geolocation permissions (send from JavaScript)

### Device info shows "Other"
- User agent string may be empty or not recognized
- Add more patterns to `detectBrowser()`, `detectOS()`, `detectDeviceType()` methods in ActivityLog model

### Trash not working
- Ensure `deleted_at` column exists: Check migration was applied
- Use `onlyTrashed()` scope to query soft-deleted logs
- Use `forceDelete()` for permanent deletion

## Routes

- `GET /settings` - View activity logs
- `GET /settings/trash` - View trash (admin only)
- `POST /activity-logs/{id}/delete` - Soft delete a log (admin only)
- `POST /activity-logs/{id}/restore` - Restore a log (admin only)
- `POST /activity-logs/{id}/force-delete` - Permanently delete a log (admin only)
- `POST /settings/clear-logs` - Clear all logs to trash (admin only)
- `POST /settings/empty-trash` - Empty trash permanently (admin only)
- `GET /settings/export-logs` - Export logs as CSV (admin only)

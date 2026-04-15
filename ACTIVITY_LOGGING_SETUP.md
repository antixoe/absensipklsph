# Activity Logging System - Setup Complete

## What's New ✨

The Activity Logging System has been successfully implemented with the following features:

### 1. **Soft Deletes & Hard Deletes**
- Logs can be soft-deleted (moved to trash) and restored
- Administrators can permanently delete logs from trash
- Original timestamps and data preserved on soft delete

### 2. **Comprehensive Activity Tracking**
Captures the following information for every action:

- **User Identity**: Who performed the action
- **Date & Time**: Exact timestamp with millisecond precision
- **IP Address**: User's IP address for security auditing
- **Device Information**:
  - Device type (mobile, tablet, desktop)
  - Browser (Chrome, Firefox, Safari, Edge, Opera)
  - Operating System (Windows, macOS, Linux, iOS, Android)
- **Location Details**:
  - Geographic coordinates (latitude, longitude)
  - Location name / Building
  - City and Country
- **Action Details**:
  - HTTP method (GET, POST, PUT, DELETE)
  - URL path accessed
  - Action type (create, update, delete, approve, reject, login, logout, etc.)
- **Data Changes** (for updates):
  - Old values (before update)
  - New values (after update)
  - Helps track exactly what changed

### 3. **Automatic Logging**
- ✅ Login events automatically logged
- ✅ Logout events automatically logged
- ✅ Device/browser info auto-detected
- ✅ IP address auto-captured

### 4. **Admin Dashboard**
- **Settings > Activity Log**: View all activities with filters
- **Settings > Trash**: Manage soft-deleted logs
- Export logs as CSV with all details

### 5. **Activity Scopes**
Query logs efficiently using Laravel scopes:
```php
ActivityLog::active()              // Not deleted
ActivityLog::deleted()             // Soft deleted
ActivityLog::forUser($id)          // For specific user
ActivityLog::forDate(now())        // For specific date
ActivityLog::forAction('approved') // For specific action
```

## Database Migration

Migration file: `database/migrations/2026_04_15_000000_add_soft_delete_and_location_to_activity_logs.php`

New columns added to `activity_logs` table:
- `deleted_at` - Soft delete timestamp
- `latitude`, `longitude` - GPS coordinates
- `location_name`, `location_city`, `location_country` - Location info
- `device_type`, `browser`, `operating_system` - Device details
- `method` - HTTP method
- `url_path` - Accessed URL
- `old_values`, `new_values` - JSON data changes

## Routes Added

```
GET    /settings/trash                    - View trash (admin)
POST   /activity-logs/{id}/delete         - Soft delete (admin)
POST   /activity-logs/{id}/restore        - Restore (admin)
POST   /activity-logs/{id}/force-delete   - Permanent delete (admin)
POST   /settings/empty-trash              - Empty trash (admin)
```

## Usage Examples

### Quick Log
```php
use App\Models\ActivityLog;

ActivityLog::log('action', 'subject', $id, 'Description');
```

### Detailed Log
```php
use App\Services\ActivityLoggerService;

ActivityLoggerService::logUpdate(
    'absence',
    $absenceId,
    ['status' => 'pending'],
    ['status' => 'approved'],
    'Admin approved absence'
);
```

### Query Logs
```php
// Get today's activities
$logs = ActivityLog::active()->forDate(now())->get();

// Get user's actions
$logs = ActivityLog::active()->forUser(auth()->id())->get();

// Get deleted logs
$trash = ActivityLog::onlyTrashed()->get();

// Restore a log
$log = ActivityLog::withTrashed()->find($id);
$log->restore();
```

## Files Created/Modified

### New Files
- `app/Services/ActivityLoggerService.php` - Helper service for logging
- `resources/views/settings/trash.blade.php` - Trash management view
- `ACTIVITY_LOGGING.md` - Comprehensive documentation

### Modified Files
- `app/Models/ActivityLog.php` - Added SoftDeletes, new columns, scopes
- `app/Http/Controllers/SettingsController.php` - Added trash/restore/delete methods
- `app/Providers/AppServiceProvider.php` - Added login/logout listeners
- `routes/web.php` - Added new routes
- `resources/views/settings/index.blade.php` - Enhanced display with location/device info
- `database/migrations/2026_04_15_000000_add_soft_delete_and_location_to_activity_logs.php` - Schema updates

## Next Steps

1. **Test the System**: 
   - Perform some actions (create, update, delete)
   - Check Settings > Activity Log to see logs

2. **Send Location Data** (Optional):
   - When making requests, include location fields:
   ```javascript
   latitude, longitude, location_name, location_city, location_country
   ```

3. **Review Documentation**:
   - See `ACTIVITY_LOGGING.md` for complete API reference

4. **Update Controllers**:
   - Replace `ActivityLog::log()` with `ActivityLoggerService` for better code organization
   - This is optional but recommended for maintainability

## Troubleshooting

**Q: Activities not showing up?**
- A: Run migrations: `php artisan migrate`
- A: Check user is authenticated when logging

**Q: Soft delete not working?**
- A: Ensure `deleted_at` column exists in database
- A: Use `onlyTrashed()` or `withTrashed()` scopes

**Q: Location data not captured?**
- A: Send location fields with requests
- A: Check request has latitude, longitude, location_name, etc.

**Q: Trash link not showing?**
- A: Only admins can see Settings > Trash
- A: Ensure user has admin role

## Security Notes

✅ All activities are audited with IP addresses  
✅ Only admins can manage logs and trash  
✅ Soft deletes preserve data for recovery  
✅ Hard deletes are permanent (use carefully)  
✅ User device/browser info tracked for security  

---

For more information, see `ACTIVITY_LOGGING.md` in the project root.

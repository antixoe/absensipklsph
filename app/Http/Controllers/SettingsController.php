<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    /**
     * Show settings page with activity logs
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(Role::KESISWAAN);

        // For non-admin users, show the enhanced student settings page
        if (!$isAdmin) {
            $userLogs = ActivityLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('settings.student-settings', compact('user', 'userLogs'));
        }

        // For admin users, show the admin activity logs page
        $activityLogs = collect();
        $totalActions = 0;
        $todayActions = 0;
        $userActions = 0;
        $availableActions = collect();
        $availableUsers = collect();

        try {
            // Get per page value from request, default to 50
            $perPage = (int) $request->get('per_page', 50);
            $perPage = in_array($perPage, [25, 50, 100]) ? $perPage : 50;

            // Build query with search and filters
            $query = ActivityLog::with('user')
                ->active();

            // Search by user name or email
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            }

            // Filter by action type
            if ($request->filled('action_filter')) {
                $query->where('action', $request->get('action_filter'));
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            }

            // Filter by user
            if ($request->filled('user_filter')) {
                $query->where('user_id', $request->get('user_filter'));
            }

            // Get stats
            $totalActions = ActivityLog::active()->count();
            $todayActions = ActivityLog::active()->whereDate('created_at', today())->count();
            $userActions = ActivityLog::active()->where('user_id', auth()->id())->count();

            // Get distinct actions for filter dropdown
            $availableActions = ActivityLog::active()
                ->distinct()
                ->pluck('action')
                ->map(fn($action) => str_replace('_', ' ', ucfirst($action)))
                ->sort();

            // Get distinct users for filter dropdown
            $availableUsers = \App\Models\User::where(function ($q) {
                $q->has('activityLogs');
            })->get();

            // Get active activity logs with pagination
            $activityLogs = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        } catch (\Exception $e) {
            // Table might not exist yet - gracefully handle
            \Log::warning('Activity logs table error: ' . $e->getMessage());
        }

        return view('settings.index', compact(
            'activityLogs', 
            'totalActions', 
            'todayActions', 
            'userActions',
            'availableActions',
            'availableUsers'
        ));
    }

    /**
     * Show trash (deleted activity logs)
     */
    public function trash()
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $deletedLogs = collect();
        $totalDeleted = 0;

        try {
            // Get deleted activity logs - 50 per page
            $deletedLogs = ActivityLog::with('user')
                ->onlyTrashed()
                ->orderBy('deleted_at', 'desc')
                ->paginate(50);

            $totalDeleted = ActivityLog::onlyTrashed()->count();
        } catch (\Exception $e) {
            \Log::warning('Activity logs trash error: ' . $e->getMessage());
        }

        return view('settings.trash', compact('deletedLogs', 'totalDeleted'));
    }

    /**
     * Soft delete an activity log
     */
    public function delete($id)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::findOrFail($id);
            $log->delete();

            ActivityLog::log(
                'deleted_activity_log',
                'activity_log',
                $id,
                'Soft deleted activity log for user ' . ($log->user->name ?? 'Unknown')
            );

            return redirect()->back()->with('success', 'Activity log moved to trash.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete activity log: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted activity log
     */
    public function restore($id)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::withTrashed()->findOrFail($id);
            $log->restore();

            ActivityLog::log(
                'restored_activity_log',
                'activity_log',
                $id,
                'Restored activity log for user ' . ($log->user->name ?? 'Unknown')
            );

            return redirect()->back()->with('success', 'Activity log restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore activity log: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete an activity log
     */
    public function forceDelete($id)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $log = ActivityLog::withTrashed()->findOrFail($id);
            $userName = $log->user->name ?? 'Unknown';
            $log->forceDelete();

            ActivityLog::log(
                'permanently_deleted_activity_log',
                'activity_log',
                $id,
                'Permanently deleted activity log for user ' . $userName
            );

            return redirect()->back()->with('success', 'Activity log permanently deleted.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to permanently delete activity log: ' . $e->getMessage());
        }
    }

    /**
     * Clear activity logs (soft delete all active logs)
     */
    public function clearLogs(Request $request)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $count = ActivityLog::active()->count();
            ActivityLog::active()->delete();

            ActivityLog::log('cleared_activity_logs', 'activity_log', null, 'All activity logs were soft deleted');

            return redirect()->back()->with('success', "Activity logs moved to trash ($count records).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear activity logs: ' . $e->getMessage());
        }
    }

    /**
     * Permanently clear all deleted logs from trash
     */
    public function emptyTrash(Request $request)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $count = ActivityLog::onlyTrashed()->count();
            ActivityLog::onlyTrashed()->forceDelete();

            ActivityLog::log('emptied_activity_logs_trash', 'activity_log', null, "Permanently deleted $count activity logs from trash");

            return redirect()->back()->with('success', "Trash emptied permanently ($count records).");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to empty trash: ' . $e->getMessage());
        }
    }

    /**
     * Export activity logs as CSV
     */
    public function exportLogs()
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $logs = ActivityLog::with('user')->active()->orderBy('created_at', 'desc')->get();

        $csvContent = "Date,Time,User,Action,Subject,Description,IP Address,Location,Device,Browser,OS\n";
        
        foreach ($logs as $log) {
            $csvContent .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $log->created_at->format('Y-m-d'),
                $log->created_at->format('H:i:s'),
                $log->user->name ?? 'Unknown',
                $log->action,
                $log->subject ?? '',
                str_replace('"', '""', $log->description ?? ''),
                $log->ip_address ?? '',
                str_replace('"', '""', $log->full_location ?? ''),
                $log->device_type ?? '',
                $log->browser ?? '',
                $log->operating_system ?? ''
            );
        }

        return response()
            ->streamDownload(fn() => print($csvContent), 'activity-logs-' . now()->format('Y-m-d-His') . '.csv');
    }

    /**
     * Export the current database as a downloadable backup file.
     */
    public function exportDatabase()
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $exportPath = $this->createDatabaseDump('database-export', false);

            return response()->download($exportPath, basename($exportPath))
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to export database: ' . $e->getMessage());
        }
    }

    /**
     * Create a persistent backup copy of the current database and download it.
     */
    public function backupDatabase()
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        try {
            $backupPath = $this->createDatabaseDump('database-backup', true);

            return response()->download($backupPath, basename($backupPath));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to create database backup: ' . $e->getMessage());
        }
    }

    /**
     * Import/restore the database from an uploaded backup file.
     */
    public function importDatabase(Request $request)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $driver = $this->getDatabaseDriver();
        $allowedExtensions = $driver === 'sqlite'
            ? ['sqlite', 'sqlite3', 'db']
            : ['sql'];

        $validated = $request->validate([
            'database_file' => [
                'required',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) use ($allowedExtensions) {
                    $extension = strtolower($value->getClientOriginalExtension());

                    if (!in_array($extension, $allowedExtensions, true)) {
                        $fail('The database file must be a valid backup file.');
                    }
                },
            ],
        ]);

        try {
            $this->createDatabaseDump('pre-import-backup', true);

            $uploadedFile = $validated['database_file'];

            if ($driver === 'sqlite') {
                $databasePath = $this->getDatabasePath();

                if (!file_exists($databasePath)) {
                    return redirect()->back()->with('error', 'Database file not found.');
                }

                if (!copy($uploadedFile->getRealPath(), $databasePath)) {
                    return redirect()->back()->with('error', 'Failed to import database file.');
                }

                clearstatcache(true, $databasePath);
            } elseif ($driver === 'mysql') {
                $sql = file_get_contents($uploadedFile->getRealPath());

                if ($sql === false || trim($sql) === '') {
                    return redirect()->back()->with('error', 'Uploaded SQL file is empty or unreadable.');
                }

                $this->restoreDatabaseFromSql($sql);
            } else {
                return redirect()->back()->with('error', 'Database import is only supported for MySQL and SQLite.');
            }

            return redirect()->route('settings.index')
                ->with('success', 'Database imported successfully. A backup was created before the import.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to import database: ' . $e->getMessage());
        }
    }

    /**
     * Get the configured SQLite database file path.
     */
    private function getDatabasePath(): string
    {
        $databasePath = config('database.connections.sqlite.database', database_path('database.sqlite'));

        return is_string($databasePath) && $databasePath !== ''
            ? $databasePath
            : database_path('database.sqlite');
    }

    /**
     * Get the active database driver.
     */
    private function getDatabaseDriver(): string
    {
        return DB::connection()->getDriverName();
    }

    /**
     * Create a timestamped database dump file.
     */
    private function createDatabaseDump(string $prefix, bool $persistent = false): string
    {
        $driver = $this->getDatabaseDriver();
        $directory = $persistent
            ? storage_path('app/database-backups')
            : storage_path('app/database-exports');

        File::ensureDirectoryExists($directory);

        $timestamp = now()->format('Ymd_His');

        if ($driver === 'sqlite') {
            $databasePath = $this->getDatabasePath();

            if (!file_exists($databasePath)) {
                throw new \RuntimeException('Database file not found.');
            }

            $dumpPath = $directory . DIRECTORY_SEPARATOR . $prefix . '-' . $timestamp . '.sqlite';

            if (!copy($databasePath, $dumpPath)) {
                throw new \RuntimeException('Failed to create database backup file.');
            }

            return $dumpPath;
        }

        if ($driver !== 'mysql') {
            throw new \RuntimeException('Database export is only supported for MySQL and SQLite.');
        }

        $dumpPath = $directory . DIRECTORY_SEPARATOR . $prefix . '-' . $timestamp . '.sql';
        File::put($dumpPath, $this->buildMysqlDatabaseDump());

        return $dumpPath;
    }

    /**
     * Build a SQL dump for the current MySQL database.
     */
    private function buildMysqlDatabaseDump(): string
    {
        $tables = $this->getMysqlTables();
        $pdo = DB::connection()->getPdo();
        $dump = [];
        $dump[] = 'SET FOREIGN_KEY_CHECKS=0;';

        foreach ($tables as $table) {
            $quotedTable = $this->quoteIdentifier($table);
            $createRow = DB::selectOne('SHOW CREATE TABLE ' . $quotedTable);
            $createData = (array) $createRow;
            $createSql = $createData['Create Table'] ?? array_values($createData)[1] ?? null;

            if (!$createSql) {
                continue;
            }

            $dump[] = 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
            $dump[] = $createSql . ';';

            $columns = Schema::getColumnListing($table);
            $batch = [];

            foreach (DB::table($table)->cursor() as $row) {
                $batch[] = (array) $row;

                if (count($batch) >= 500) {
                    $insertSql = $this->buildInsertStatement($table, $columns, $batch, $pdo);
                    if ($insertSql !== '') {
                        $dump[] = $insertSql;
                    }
                    $batch = [];
                }
            }

            if ($batch !== []) {
                $insertSql = $this->buildInsertStatement($table, $columns, $batch, $pdo);
                if ($insertSql !== '') {
                    $dump[] = $insertSql;
                }
            }
        }

        $dump[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n\n", $dump) . "\n";
    }

    /**
     * Get all base tables for the active MySQL database.
     *
     * @return array<int, string>
     */
    private function getMysqlTables(): array
    {
        $rows = DB::select('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');

        return array_values(array_filter(array_map(function ($row) {
            $values = array_values((array) $row);
            return $values[0] ?? null;
        }, $rows)));
    }

    /**
     * Build INSERT statements for a batch of rows.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    private function buildInsertStatement(string $table, array $columns, array $rows, \PDO $pdo): string
    {
        if ($rows === [] || $columns === []) {
            return '';
        }

        $quotedTable = $this->quoteIdentifier($table);
        $quotedColumns = implode(', ', array_map(fn (string $column) => $this->quoteIdentifier($column), $columns));
        $values = [];

        foreach ($rows as $row) {
            $rowValues = [];

            foreach ($columns as $column) {
                $rowValues[] = $this->quoteDumpValue($row[$column] ?? null, $pdo);
            }

            $values[] = '(' . implode(', ', $rowValues) . ')';
        }

        return 'INSERT INTO ' . $quotedTable . ' (' . $quotedColumns . ") VALUES\n"
            . implode(",\n", $values) . ';';
    }

    /**
     * Quote a database identifier for SQL output.
     */
    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    /**
     * Quote a database value for use in a SQL dump.
     */
    private function quoteDumpValue(mixed $value, \PDO $pdo): string
    {
        if ($value === null) {
            return 'NULL';
        }

        if ($value instanceof \DateTimeInterface) {
            return $pdo->quote($value->format('Y-m-d H:i:s'));
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $pdo->quote((string) $value);
    }

    /**
     * Restore the current MySQL database from SQL text.
     */
    private function restoreDatabaseFromSql(string $sql): void
    {
        $statements = $this->splitSqlStatements($sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($statements as $statement) {
                $trimmed = trim($statement);

                if ($trimmed === '') {
                    continue;
                }

                DB::unprepared($trimmed);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Split SQL text into executable statements.
     *
     * @return array<int, string>
     */
    private function splitSqlStatements(string $sql): array
    {
        $sql = preg_replace('/^\xEF\xBB\xBF/', '', $sql) ?? $sql;
        $statements = [];
        $buffer = '';
        $inSingleQuote = false;
        $inDoubleQuote = false;
        $escapeNext = false;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $buffer .= $char;

            if ($escapeNext) {
                $escapeNext = false;
                continue;
            }

            if ($char === '\\' && ($inSingleQuote || $inDoubleQuote)) {
                $escapeNext = true;
                continue;
            }

            if ($char === "'" && !$inDoubleQuote) {
                $inSingleQuote = !$inSingleQuote;
                continue;
            }

            if ($char === '"' && !$inSingleQuote) {
                $inDoubleQuote = !$inDoubleQuote;
                continue;
            }

            if ($char === ';' && !$inSingleQuote && !$inDoubleQuote) {
                $statement = trim(rtrim($buffer, ';'));

                if ($statement !== '') {
                    $statements[] = $statement;
                }

                $buffer = '';
            }
        }

        $statement = trim(rtrim($buffer, ';'));

        if ($statement !== '') {
            $statements[] = $statement;
        }

        return $statements;
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . auth()->id()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        auth()->user()->update($validated);

        ActivityLog::log(
            'updated_profile',
            'user',
            auth()->id(),
            'Updated profile information'
        );

        return redirect()->route('settings.index')->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        ActivityLog::log(
            'changed_password',
            'user',
            auth()->id(),
            'Changed password'
        );

        return redirect()->route('settings.index')->with('success', 'Password changed successfully!');
    }

    /**
     * Update language preference.
     */
    public function updateLanguage(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:id,en,zh'],
        ]);

        $locale = $validated['locale'];

        session(['locale' => $locale]);

        if (auth()->check() && Schema::hasColumn('users', 'locale')) {
            auth()->user()->update(['locale' => $locale]);
        }

        App::setLocale($locale);

        ActivityLog::log(
            'updated_language',
            'user',
            auth()->id(),
            'Updated language preference to ' . $locale
        );

        return redirect()->route('settings.index')->with('success', __('settings.language_updated'));
    }

    /**
     * Get user's activity logs (personal logs only, not admin-only)
     */
    public function getUserActivityLogs()
    {
        $userLogs = ActivityLog::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('settings.user-activity', compact('userLogs'));
    }

    /**
     * Show access control page
     */
    public function accessControl()
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $users = \App\Models\User::with('role', 'roleFeatures')->get();
        $features = \App\Models\Feature::all();

        return view('settings.access-control', compact('users', 'features'));
    }

    /**
     * Update user access to features
     */
    public function updateUserAccess(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'features' => ['array'],
            'features.*' => ['exists:features,id'],
        ]);

        // Sync features for the user
        $user->roleFeatures()->sync($validated['features'] ?? []);

        ActivityLog::log(
            'updated_user_access',
            'user',
            $user->id,
            'Updated feature access for user ' . $user->name
        );

        return response()->json(['success' => true, 'message' => 'User access updated successfully']);
    }
}


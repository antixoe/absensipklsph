<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Level;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // Only Kesiswaan (admin) can manage users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $query = User::with('role');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->get('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Order by newest users first
        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::all();
        $availableLevels = Level::where('status', 'active')->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'availableLevels'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Only Kesiswaan (admin) can create users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $roles = Role::all();
        $availableLevels = Level::where('status', 'active')->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'availableLevels'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store(Request $request)
    {
        // Only Kesiswaan (admin) can create users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized access');
        }

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:255'],
                'role_id' => ['required', 'exists:roles,id'],
                'level' => [
                    'nullable',
                    'string',
                    'max:100',
                    Rule::in(Level::where('status', 'active')->pluck('name')->all()),
                ],
                'status' => ['required', 'in:active,inactive'],
            ]);

            $validated['password'] = Hash::make($validated['password']);

            User::create($validated);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully!'
                ]);
            }

            return redirect()->route('admin.users')
                ->with('success', 'User created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON response for AJAX requests with validation errors
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Only Kesiswaan (admin) can view user details
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Only Kesiswaan (admin) can edit users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $roles = Role::all();
        $availableLevels = Level::where('status', 'active')->orderBy('name')->get();
        $user->load('role');
        return view('admin.users.edit', compact('user', 'roles', 'availableLevels'));
    }

    /**
     * Update the specified user in database.
     */
    public function update(Request $request, User $user)
    {
        // Only Kesiswaan (admin) can update users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'level' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(Level::where('status', 'active')->pluck('name')->all()),
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the specified user from database (soft delete).
     */
    public function destroy(User $user)
    {
        // Only Kesiswaan (admin) can delete users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully! You can restore this user within 30 days.');
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore($userId)
    {
        // Only Kesiswaan (admin) can restore users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user = User::onlyTrashed()->findOrFail($userId);
        $user->restore();

        return redirect()->route('admin.users')
            ->with('success', 'User ' . $user->name . ' has been restored successfully!');
    }

    /**
     * Permanently delete a soft-deleted user.
     */
    public function forceDelete($userId)
    {
        // Only Kesiswaan (admin) can permanently delete users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user = User::onlyTrashed()->findOrFail($userId);
        $userName = $user->name;
        $user->forceDelete();

        return redirect()->route('admin.users')
            ->with('success', 'User ' . $userName . ' has been permanently deleted.');
    }

    /**
     * Show deleted (trashed) users.
     */
    public function trash(Request $request)
    {
        // Only Kesiswaan (admin) can view trash
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $query = User::onlyTrashed()->with('role');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Order by most recently deleted first
        $trashedUsers = $query->orderBy('deleted_at', 'desc')->paginate(10)->withQueryString();
        $roles = Role::all();

        return view('admin.users.trash', compact('trashedUsers', 'roles'));
    }

    /**
     * Show import form for Excel users.
     */
    public function importForm()
    {
        // Only Kesiswaan (admin) can import users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.users.import');
    }

    /**
     * Handle Excel file upload for bulk user import.
     */
    public function import(Request $request)
    {
        // Only Kesiswaan (admin) can import users
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'file' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['xlsx', 'xls', 'csv'];
                    $fileExtension = strtolower($value->getClientOriginalExtension());
                    
                    if (!in_array($fileExtension, $allowedExtensions)) {
                        $fail('The file must be a CSV, XLS, or XLSX file. Uploaded file type: .' . $fileExtension);
                    }
                }
            ],
        ]);

        try {
            $result = UsersImport::fromFile($request->file('file')->getRealPath());

            $message = 'Users imported successfully! Created: ' . $result['created'] . ' user(s)';
            if ($result['skipped'] > 0) {
                $message .= ', Skipped: ' . $result['skipped'] . ' user(s)';
            }
            if (!empty($result['errors'])) {
                $message .= '. Errors: ' . implode('; ', array_slice($result['errors'], 0, 3));
            }

            return redirect()->route('admin.users')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Get user details as JSON for modal view.
     */
    public function getDetails(User $user)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user->load('role', 'student.qrCode');

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? '-',
            'address' => $user->address,
            'role' => Role::displayName($user->role->name ?? 'N/A'),
            'level' => $user->level ? Level::displayName($user->level) : '-',
            'status' => $user->status,
            'created_at' => $user->created_at->format('M d, Y H:i'),
            'updated_at' => $user->updated_at->format('M d, Y H:i'),
            'student_qr_code' => null,
            'student_qr_image_url' => null,
            'student_qr_status' => null,
            'student_qr_created_at' => null,
            'student_id' => null,
            'nim' => null,
            'school' => null,
            'has_student' => false,
        ];

        // Add student QR code info if user has a student profile
        if ($user->student && $user->student->qrCode) {
            $userData['has_student'] = true;
            $userData['student_id'] = $user->student->id;
            $userData['student_qr_code'] = $user->student->qrCode->code;
            $userData['student_qr_image_url'] = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($user->student->qrCode->code);
            $userData['student_qr_status'] = $user->student->qrCode->status;
            $userData['student_qr_created_at'] = optional($user->student->qrCode->created_at)->format('M d, Y H:i');
            $userData['nim'] = $user->student->nim;
            $userData['school'] = $user->student->school;
        }

        return response()->json([
            'success' => true,
            'user' => $userData
        ]);
    }

    /**
     * Get user edit form data as JSON.
     */
    public function getEditData(User $user)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            abort(403, 'Unauthorized access');
        }

        $user->load('role', 'student.qrCode');
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'role_id' => $user->role_id,
                'level' => $user->level,
                'status' => $user->status,
            ],
            'roles' => $roles->map(fn($role) => [
                'id' => $role->id,
                'name' => Role::displayName($role->name)
            ])
        ]);
    }

    /**
     * Get roles list as JSON for modal form.
     */
    public function getRoles()
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $roles = Role::all();

        return response()->json([
            'success' => true,
            'roles' => $roles->map(fn($role) => [
                'id' => $role->id,
                'name' => Role::displayName($role->name)
            ])
        ]);
    }

    /**
     * Update user via API for modal form.
     */
    public function updateViaModal(Request $request, User $user)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'level' => [
                'nullable',
                'string',
                'max:100',
                Rule::in(Level::where('status', 'active')->pluck('name')->all()),
            ],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }

    /**
     * Perform bulk actions on multiple users.
     */
    public function bulkAction(Request $request)
    {
        if (!Auth::user() || !Auth::user()->hasRole(Role::KESISWAAN)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'action' => ['required', 'in:delete,activate,deactivate,change_role,change_level'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role_id' => ['required_if:action,change_role', 'nullable', 'exists:roles,id'],
            'level' => [
                'required_if:action,change_level',
                'nullable',
                'string',
                'max:100',
                Rule::in(Level::where('status', 'active')->pluck('name')->all()),
            ],
        ]);

        try {
            $users = User::whereIn('id', $validated['user_ids'])->get();
            $action = $validated['action'];
            $count = 0;

            foreach ($users as $user) {
                // Prevent deleting the current admin user
                if ($action === 'delete' && $user->id === Auth::id()) {
                    continue;
                }

                switch ($action) {
                    case 'delete':
                        $user->delete();
                        $count++;
                        break;

                    case 'activate':
                        if ($user->status !== 'active') {
                            $user->update(['status' => 'active']);
                            $count++;
                        }
                        break;

                    case 'deactivate':
                        if ($user->id !== Auth::id() && $user->status !== 'inactive') {
                            $user->update(['status' => 'inactive']);
                            $count++;
                        }
                        break;

                    case 'change_role':
                        if (isset($validated['role_id'])) {
                            $user->update(['role_id' => $validated['role_id']]);
                            $count++;
                        }
                        break;

                    case 'change_level':
                        $user->update(['level' => $validated['level'] ?? null]);
                        $count++;
                        break;
                }
            }

            $actionLabel = match($action) {
                'delete' => 'deleted',
                'activate' => 'activated',
                'deactivate' => 'deactivated',
                'change_role' => 'updated',
                'change_level' => 'updated',
                default => 'processed'
            };

            return response()->json([
                'success' => true,
                'message' => "{$count} user(s) successfully {$actionLabel}!",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}

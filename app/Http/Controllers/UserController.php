<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        // Only admin can manage users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $users = User::with('role')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Only admin can create users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in database.
     */
    public function store(Request $request)
    {
        // Only admin can create users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Only admin can view user details
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
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
        // Only admin can edit users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $roles = Role::all();
        $user->load('role');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in database.
     */
    public function update(Request $request, User $user)
    {
        // Only admin can update users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
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
     * Remove the specified user from database.
     */
    public function destroy(User $user)
    {
        // Only admin can delete users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Show import form for Excel users.
     */
    public function importForm()
    {
        // Only admin can import users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.users.import');
    }

    /**
     * Handle Excel file upload for bulk user import.
     */
    public function import(Request $request)
    {
        // Only admin can import users
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Show role and feature management page.
     */
    public function index()
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $roles = Role::with('features')->get();
        $features = Feature::all();

        return view('admin.roles.index', compact('roles', 'features'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $features = Feature::all();
        return view('admin.roles.create', compact('features'));
    }

    /**
     * Store a newly created role in database.
     */
    public function store(Request $request)
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'features' => ['array'],
            'features.*' => ['exists:features,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach selected features
        if (!empty($validated['features'])) {
            $role->features()->sync($validated['features']);
        }

        return redirect()->route('admin.roles')
            ->with('success', 'Role created successfully!');
    }

    /**
     * Edit role permissions.
     */
    public function edit(Role $role)
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return redirect()->route('admin.roles')
            ->with('open_edit_role_id', $role->id);
    }

    /**
     * Update role permissions.
     */
    public function update(Request $request, Role $role)
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:500'],
            'features' => ['array'],
            'features.*' => ['exists:features,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.roles')
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_role_id', $role->id);
        }

        $validated = $validator->validated();

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync features (attach new ones, detach removed ones)
        $role->features()->sync($validated['features'] ?? []);

        return redirect()->route('admin.roles')
            ->with('success', 'Role updated successfully!');
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role)
    {
        // Only admin can manage roles
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        // Prevent deletion if role has users
        if ($role->users()->exists()) {
            return redirect()->route('admin.roles')
                ->with('error', 'Cannot delete role. ' . $role->users()->count() . ' user(s) are assigned to this role.');
        }

        $roleName = Role::displayName($role->name);
        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Peran "' . $roleName . '" berhasil dihapus!');
    }

    /**
     * Show features configuration page.
     */
    public function features()
    {
        // Only admin can manage features
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $features = Feature::all();
        return view('admin.features.index', compact('features'));
    }

    /**
     * Toggle feature active status.
     */
    public function toggleFeature(Feature $feature)
    {
        // Only admin can manage features
        if (!Auth::user() || !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $feature->update(['is_active' => !$feature->is_active]);

        return back()->with('success', 'Feature status updated!');
    }
}

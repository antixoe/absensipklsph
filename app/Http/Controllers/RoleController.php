<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $features = Feature::all();
        $selectedFeatures = $role->features()->pluck('features.id')->toArray();

        return view('admin.roles.edit', compact('role', 'features', 'selectedFeatures'));
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

        $featureIds = $request->input('features', []);
        
        // Sync features (attach new ones, detach removed ones)
        $role->features()->sync($featureIds);

        return redirect()->route('admin.roles')
            ->with('success', 'Role permissions updated successfully!');
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

        // Prevent deletion of predefined system roles
        $systemRoles = ['student', 'industry_supervisor', 'head_of_department', 'homeroom_teacher', 'school_principal', 'admin'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->route('admin.roles')
                ->with('error', 'Cannot delete system roles. This role is essential to the application.');
        }

        // Prevent deletion if role has users
        if ($role->users()->exists()) {
            return redirect()->route('admin.roles')
                ->with('error', 'Cannot delete role. ' . $role->users()->count() . ' user(s) are assigned to this role.');
        }

        $roleName = ucfirst(str_replace('_', ' ', $role->name));
        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Role "' . $roleName . '" deleted successfully!');
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

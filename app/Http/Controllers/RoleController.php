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

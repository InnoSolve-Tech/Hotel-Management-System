<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorizeRoleAccess();

        return view('role.index', [
            'roles' => Role::query()
                ->with('permissions')
                ->withCount('users')
                ->orderByDesc('is_system')
                ->orderBy('name')
                ->get(),
            'permissionGroups' => PermissionRegistry::groupedPermissions(),
            'grantablePermissions' => $this->grantablePermissionNames(request()->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->authorizeRoleAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $permissionNames = $this->validatedPermissionNames($user, $validated['permissions'] ?? []);

        $role = Role::query()->create([
            'name' => $validated['name'],
            'is_system' => false,
        ]);

        $role->permissions()->sync(
            Permission::query()->whereIn('name', $permissionNames)->pluck('id')
        );

        return back()->with('Success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $user = $request->user();

        $this->authorizeRoleAccess();
        $this->authorizeRoleMutation($user, $role);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        if (! $role->is_system && filled($validated['name'] ?? null)) {
            $role->update(['name' => $validated['name']]);
        }

        $permissionNames = $this->validatedPermissionNames($user, $validated['permissions'] ?? []);

        $role->permissions()->sync(
            Permission::query()->whereIn('name', $permissionNames)->pluck('id')
        );

        return back()->with('Success', 'Role updated successfully.');
    }

    protected function authorizeRoleAccess(): void
    {
        abort_unless(request()->user()?->hasRole(['SuperAdmin', 'Admin']), 403);
    }

    protected function authorizeRoleMutation($user, Role $role): void
    {
        if ($user->hasRole('Admin') && $role->name === 'SuperAdmin') {
            abort(403, 'Admins cannot edit the SuperAdmin role.');
        }
    }

    protected function validatedPermissionNames($user, array $permissionNames): array
    {
        $grantablePermissions = $this->grantablePermissionNames($user);
        $requestedPermissions = array_values(array_unique($permissionNames));
        $forbiddenPermissions = array_diff($requestedPermissions, $grantablePermissions);

        abort_if(! empty($forbiddenPermissions), 403, 'You cannot grant permissions you do not already have.');

        return $requestedPermissions;
    }

    protected function grantablePermissionNames($user): array
    {
        if ($user->hasRole('SuperAdmin')) {
            return Permission::query()->pluck('name')->all();
        }

        $user->loadMissing('role.permissions');

        return $user->role?->permissions->pluck('name')->all() ?? [];
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Models\Role;
use App\Support\Permissions\PermissionRegistry;
use App\Support\Sidebar\SidebarMenuBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        return view('backend.pages.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.roles.create', [
            'role' => null,
            'modules' => PermissionRegistry::modules(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        Role::query()->create([
            'name' => $request->validated('name'),
            'slug' => $this->uniqueSlug($request->validated('name')),
            'permissions' => PermissionRegistry::normalize($request->validated('permissions', [])),
            'status' => $request->boolean('status'),
        ]);
        SidebarMenuBuilder::bumpVersion();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('backend.pages.roles.edit', [
            'role' => $role,
            'modules' => PermissionRegistry::modules(),
            'selectedPermissions' => $role->permissions ?: [],
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update([
            'name' => $request->validated('name'),
            'permissions' => PermissionRegistry::normalize($request->validated('permissions', [])),
            'status' => $request->boolean('status'),
        ]);
        SidebarMenuBuilder::bumpVersion();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return redirect()
                ->route('backend.roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        if ($role->users()->exists()) {
            return redirect()
                ->route('backend.roles.index')
                ->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        $role->delete();
        SidebarMenuBuilder::bumpVersion();

        return redirect()
            ->route('backend.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $index = 2;

        while (Role::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$index;
            $index++;
        }

        return $slug;
    }
}

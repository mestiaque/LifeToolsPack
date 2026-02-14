<?php

namespace EmCore\Http\Controllers;

use ME\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ME\Models\RolePermission;
use Illuminate\Support\Collection;
use ME\Http\Controllers\Controller;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('authorization:role.view')->only(['index', 'show']);
        $this->middleware('authorization:role.create')->only(['create', 'store']);
        $this->middleware('authorization:role.edit')->only(['edit', 'update']);
        $this->middleware('authorization:role.delete')->only('destroy');
    }

    public function index()
    {
        $roles = Role::withCount('users')
                ->where('slug', '!=', 'super_admin')
                ->latest()
                ->paginate(get_setting('pagination', 10));
        return view('em_core::roles.index', compact('roles'));
    }

    public function create()
    {
        $configPermissions = config('permissions') ?? [];
        $permissions = $this->getPermissionsFromConfig($configPermissions);

        return view('em_core::roles.create', compact('permissions'));
    }

    private function getPermissionsFromConfig(array $configPermissions): Collection
    {
        $permissions = collect();
        $id = 1;

        foreach ($configPermissions as $module => $config) {
            $actions = explode(',', $config['actions']);

            foreach ($actions as $action) {
                $slug = $module . '.' . trim($action);
                $name = ucfirst(trim($action)) . ' ' . $config['title'];

                $permissions->push((object)[
                    'id' => $id++,
                    'name' => $name,
                    'slug' => $slug,
                ]);
            }
        }

        return $permissions;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $configPermissions = config('permissions') ?? [];
            $allPermissions = $this->getPermissionsFromConfig($configPermissions);

            $selectedPermissions = collect($request->permissions)
                ->map(function ($id) use ($allPermissions) {
                    $permission = $allPermissions->firstWhere('id', $id);
                    return $permission ? $permission->slug : null;
                })
                ->filter()
                ->toArray();

            RolePermission::create([
                'role_id' => $role->id,
                'permissions' => $selectedPermissions,
            ]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show(Role $role)
    {
        $role->load('users', 'rolePermission');
        return view('em_core::roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $configPermissions = config('permissions') ?? [];
        $permissions = $this->getPermissionsFromConfig($configPermissions);

        $rolePermissions = $role->rolePermission ? $role->rolePermission->permissions : [];

        $selectedPermissionIds = $permissions
            ->filter(function ($permission) use ($rolePermissions) {
                return in_array($permission->slug, $rolePermissions);
            })
            ->pluck('id')
            ->toArray();

        return view('em_core::roles.edit', compact('role', 'permissions', 'selectedPermissionIds'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        if ($role->name !== $request->name) {
            $role->slug = Str::slug($request->name);
        }

        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();

        $configPermissions = config('permissions') ?? [];
        $allPermissions = $this->getPermissionsFromConfig($configPermissions);

        $selectedPermissions = collect($request->permissions ?? [])
            ->map(function ($id) use ($allPermissions) {
                $permission = $allPermissions->firstWhere('id', $id);
                return $permission ? $permission->slug : null;
            })
            ->filter()
            ->toArray();

        RolePermission::updateOrCreate(
            ['role_id' => $role->id],
            ['permissions' => $selectedPermissions]
        );

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully');
    }


    public function destroy(Role $role)
    {
        if ($role->slug === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete the Admin role');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully');
    }
}

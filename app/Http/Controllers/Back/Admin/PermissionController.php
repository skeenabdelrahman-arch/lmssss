<?php

namespace App\Http\Controllers\Back\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('permission:manage_roles');
    }

    /**
     * Show the form for editing the specified role permissions.
     */
    public function edit($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('back.admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role permissions.
     */
    public function update(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'تم تحديث صلاحيات الدور بنجاح');
    }
}


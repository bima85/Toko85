<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class PermissionMatrix extends Component
{
    public $name;

    public $search = '';

    public function mount()
    {
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('permissions.view'))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);
    }

    public function toggle($roleId, $permId)
    {
        $role = Role::findOrFail($roleId);
        $perm = Permission::findOrFail($permId);

        if ($role->hasPermissionTo($perm)) {
            $role->revokePermissionTo($perm);
        } else {
            $role->givePermissionTo($perm);
        }
    }

    public function toggleGroup($roleId, $group)
    {
        $role = Role::findOrFail($roleId);

        // Find permissions in the group (prefix before dot)
        $perms = Permission::where('name', 'LIKE', $group.'.%')->get();

        if ($perms->isEmpty()) {
            return;
        }

        // If role has all perms, revoke them; otherwise give all
        $allAssigned = $perms->every(fn ($p) => $role->hasPermissionTo($p));

        foreach ($perms as $p) {
            if ($allAssigned) {
                $role->revokePermissionTo($p);
            } else {
                $role->givePermissionTo($p);
            }
        }
    }

    public function createPermission($name)
    {
        $name = trim($name);
        if (empty($name)) {
            session()->flash('error', 'Permission name is required.');

            return;
        }
        $current = Auth::user();
        abort_unless($current && ((method_exists($current, 'hasPermissionTo') && $current->hasPermissionTo('permissions.create')) || (method_exists($current, 'hasAnyRole') && $current->hasAnyRole(['admin', 'superadmin']))), 403);
        Permission::firstOrCreate(['name' => $name]);
        session()->flash('message', "Permission '{$name}' created.");
    }

    public function deletePermission($id)
    {
        $current = Auth::user();
        abort_unless($current && ((method_exists($current, 'hasPermissionTo') && $current->hasPermissionTo('permissions.delete')) || (method_exists($current, 'hasAnyRole') && $current->hasAnyRole(['admin', 'superadmin']))), 403);
        $p = Permission::findOrFail($id);
        $p->delete();
        session()->flash('message', 'Permission deleted.');
    }

    public function render()
    {
        $roles = Role::with('permissions')->orderBy('id')->get();

        $permissions = Permission::orderBy('name');

        if (! empty($this->search)) {
            $permissions = $permissions->where('name', 'LIKE', "%{$this->search}%");
        }

        $permissions = $permissions->get();

        // Group permissions by prefix before first dot
        $grouped = $permissions->groupBy(function ($p) {
            if (str_contains($p->name, '.')) {
                return explode('.', $p->name)[0];
            }

            return $p->name;
        });

        return view('livewire.admin.users.permission-matrix', [
            'roles' => $roles,
            'groupedPermissions' => $grouped,
        ]);
    }
}

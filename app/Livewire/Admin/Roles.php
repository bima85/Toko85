<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class Roles extends Component
{
    public $name;

    public $editingRoleId = null;

    public function mount()
    {
        $user = Auth::user();
        abort_unless($user && (
            (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo('roles.view'))
            || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['admin', 'superadmin']))
        ), 403);
    }

    public function create()
    {
        $this->resetForm();
    }

    public function edit($id)
    {
        $r = Role::findOrFail($id);
        $this->editingRoleId = $r->id;
        $this->name = $r->name;
    }

    public function save()
    {
        $this->validate(['name' => 'required|string|max:255']);

        if ($this->editingRoleId) {
            $current = Auth::user();
            abort_unless($current && ((method_exists($current, 'hasPermissionTo') && $current->hasPermissionTo('roles.update')) || (method_exists($current, 'hasAnyRole') && $current->hasAnyRole(['admin', 'superadmin']))), 403);
            $r = Role::findOrFail($this->editingRoleId);
            $r->name = $this->name;
            $r->save();
            session()->flash('message', 'Role updated.');
        } else {
            $current = Auth::user();
            abort_unless($current && ((method_exists($current, 'hasPermissionTo') && $current->hasPermissionTo('roles.create')) || (method_exists($current, 'hasAnyRole') && $current->hasAnyRole(['admin', 'superadmin']))), 403);
            Role::firstOrCreate(['name' => $this->name]);
            session()->flash('message', 'Role created.');
        }

        $this->resetForm();
    }

    public function delete($id)
    {
        $r = Role::findOrFail($id);
        $r->delete();
        session()->flash('message', 'Role deleted.');
    }

    public function resetForm()
    {
        $this->name = null;
        $this->editingRoleId = null;
    }

    public function render()
    {
        $roles = Role::orderBy('id')->get();

        return view('livewire.admin.users.roles', ['roles' => $roles]);
    }
}

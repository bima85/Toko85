<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Roles extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name;
    public $permissions = [];
    public $editingRoleId = null;
    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:50',
        'permissions' => 'array',
    ];

    public function mount()
    {
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasAnyRole(['admin', 'superadmin']), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $r = Role::findOrFail($id);
        $this->editingRoleId = $r->id;
        $this->name = $r->name;
        $this->permissions = $r->permissions->pluck('name')->toArray();
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingRoleId) {
            $r = Role::findOrFail($this->editingRoleId);
            $r->name = $this->name;
            $r->save();
            $r->syncPermissions($this->permissions ?: []);
            session()->flash('message', 'Role updated.');
        } else {
            $r = Role::create(['name' => $this->name]);
            if (! empty($this->permissions)) {
                $r->givePermissionTo($this->permissions);
            }
            session()->flash('message', 'Role created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $r = Role::findOrFail($id);
        // prevent deleting superadmin role
        if ($r->name === 'superadmin') {
            session()->flash('message', 'Cannot delete superadmin role.');
            return;
        }
        $r->delete();
        session()->flash('message', 'Role deleted.');
    }

    public function resetForm()
    {
        $this->name = null;
        $this->permissions = [];
        $this->editingRoleId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = Role::query();
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $roles = $query->orderBy('id', 'desc')->paginate(10);
        $allPermissions = Permission::orderBy('name')->pluck('name');

        return view('livewire.admin.roles', [
            'roles' => $roles,
            'allPermissions' => $allPermissions,
        ])->layout('layouts.admin');
    }
}

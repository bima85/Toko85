<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class Permissions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name;
    public $editingPermissionId = null;
    public $showForm = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:100',
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
        $p = Permission::findOrFail($id);
        $this->editingPermissionId = $p->id;
        $this->name = $p->name;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingPermissionId) {
            $p = Permission::findOrFail($this->editingPermissionId);
            $p->name = $this->name;
            $p->save();
            session()->flash('message', 'Permission updated.');
        } else {
            Permission::create(['name' => $this->name]);
            session()->flash('message', 'Permission created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $p = Permission::findOrFail($id);
        $p->delete();
        session()->flash('message', 'Permission deleted.');
    }

    public function resetForm()
    {
        $this->name = null;
        $this->editingPermissionId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = Permission::query();
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $permissions = $query->orderBy('id', 'desc')->paginate(15);

        return view('livewire.admin.permissions', [
            'permissions' => $permissions,
        ])->layout('layouts.admin');
    }
}

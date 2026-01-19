<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use App\Models\RoleMenuAccess;
use Illuminate\Support\Facades\Auth;

class AccessMatrix extends Component
{
    public $roles = [];
    public $menuItems = [];
    public $selected = []; // role => [route => true]
    public $currentRole = null;

    public function mount()
    {
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('superadmin'), 403);

        $this->roles = Role::orderBy('name')->get()->pluck('name')->toArray();
        $this->menuItems = config('menu_items');

        foreach ($this->roles as $role) {
            $this->selected[$role] = [];
            $existing = RoleMenuAccess::where('role_name', $role)->pluck('route_name')->toArray();
            foreach ($existing as $route) {
                $this->selected[$role][$route] = true;
            }
        }

        // default current role
        $this->currentRole = count($this->roles) ? $this->roles[0] : null;
    }

    public function toggle($role, $route)
    {
        if (isset($this->selected[$role][$route])) {
            unset($this->selected[$role][$route]);
        } else {
            $this->selected[$role][$route] = true;
        }
    }

    public function setCurrentRole($role)
    {
        if (in_array($role, $this->roles)) {
            $this->currentRole = $role;
        }
    }

    public function selectAllForCurrent()
    {
        if (! $this->currentRole) {
            return;
        }
        foreach (array_keys($this->menuItems) as $route) {
            $this->selected[$this->currentRole][$route] = true;
        }
    }

    public function clearAllForCurrent()
    {
        if (! $this->currentRole) {
            return;
        }
        $this->selected[$this->currentRole] = [];
    }

    public function save()
    {
        // write selections to DB
        RoleMenuAccess::truncate();
        foreach ($this->selected as $role => $routes) {
            foreach (array_keys($routes) as $route) {
                RoleMenuAccess::create(['role_name' => $role, 'route_name' => $route]);
            }
        }
        session()->flash('message', 'Access matrix saved.');
    }

    public function render()
    {
        return view('livewire.admin.access-matrix')->layout('layouts.admin');
    }
}

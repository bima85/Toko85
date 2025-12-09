<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $name;
    public $username;
    public $email;
    public $password;
    public $roles = [];
    public $editingUserId = null;
    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'username' => 'nullable|string|max:50',
        'email' => 'required|email|max:255',
        'password' => 'nullable|string|min:6',
        'roles' => 'array'
    ];

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->editingUserId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $u = User::findOrFail($id);
        $this->editingUserId = $u->id;
        $this->name = $u->name;
        $this->username = $u->username;
        $this->email = $u->email;
        $this->roles = $u->roles->pluck('name')->toArray();
        $this->password = null;
        $this->showForm = true;
    }

    public function save()
    {
        // dynamic validation rules (email unique, password required on create)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255' . ($this->editingUserId ? (',users,email,' . $this->editingUserId) : ''),
            'password' => $this->editingUserId ? 'nullable|string|min:6' : 'required|string|min:6',
            'roles' => 'array'
        ];

        $this->validate($rules);

        if ($this->editingUserId) {
            $u = User::findOrFail($this->editingUserId);
            $u->name = $this->name;
            // allow updating username if provided
            if ($this->username) {
                $u->username = $this->username;
            }
            $u->email = $this->email;
            if ($this->password) {
                $u->password = $this->password;
            }
            $u->save();
            $u->syncRoles($this->roles ?: []);
            session()->flash('message', 'User updated.');
        } else {
            // generate a username from name or email local part, ensure uniqueness
            $base = $this->name ? Str::slug($this->name, '') : explode('@', $this->email)[0];
            // if admin provided a username in the form, use that (ensure uniqueness)
            if ($this->username) {
                $username = $this->username;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $this->username . $i;
                    $i++;
                }
            } else {
                $username = $base;
                $i = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $base . $i;
                    $i++;
                }
            }

            $u = User::create([
                'name' => $this->name,
                'username' => $username,
                'email' => $this->email,
                'password' => $this->password ?: Str::random(10),
            ]);
            if (!empty($this->roles)) {
                $u->assignRole($this->roles);
            }
            session()->flash('message', 'User created.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $u = User::findOrFail($id);
        // Prevent deleting yourself
        if (Auth::id() === $u->id) {
            session()->flash('message', 'You cannot delete yourself.');
            return;
        }
        $u->delete();
        session()->flash('message', 'User deleted.');
    }

    public function resetForm()
    {
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->roles = [];
        $this->editingUserId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = User::query();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(10);

        $allRoles = \Spatie\Permission\Models\Role::pluck('name');

        return view('livewire.admin.users', [
            'users' => $users,
            'allRoles' => $allRoles,
        ])->layout('layouts.admin');
    }
}

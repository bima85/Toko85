<div>
  <div class="row mb-3">
    <div class="col-8">
      <h4>Users</h4>
    </div>
    <div class="col-4 text-right">
      <button wire:click="create" class="btn btn-primary">Create User</button>
    </div>
  </div>

  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="mb-3">
        <input
          wire:model.debounce.300ms="search"
          class="form-control"
          placeholder="Search by name or email..."
        />
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">{{ $editingUserId ? 'Edit User' : 'Create User' }}</h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Username</label>
              <input wire:model.defer="username" class="form-control" />
              <small class="form-text text-muted">
                Optional — generated automatically if left blank.
              </small>
              @error('username')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Name</label>
              <input wire:model.defer="name" class="form-control" />
              @error('name')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Email</label>
              <input wire:model.defer="email" class="form-control" />
              @error('email')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Password</label>
              <input wire:model.defer="password" type="password" class="form-control" />
              <small class="form-text text-muted">Leave blank to keep current password.</small>
              @error('password')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Roles</label>
              <div>
                @foreach ($allRoles as $r)
                  <label class="mr-2">
                    <input type="checkbox" wire:model="roles" value="{{ $r }}" />
                    {{ $r }}
                  </label>
                @endforeach
              </div>
            </div>

            <div class="form-group">
              <button wire:click.prevent="save" class="btn btn-success">Save</button>
              <button wire:click.prevent="resetForm" class="btn btn-default">Cancel</button>
            </div>
          </div>
        </div>
      @endif

      <table class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->username }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
              <td class="text-right">
                <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-primary">
                  Edit
                </button>
                <button
                  wire:click="delete({{ $user->id }})"
                  onclick="return confirm('Delete user?')"
                  class="btn btn-sm btn-danger"
                >
                  Delete
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div>
        {{ $users->links() }}
      </div>
    </div>
  </div>
</div>

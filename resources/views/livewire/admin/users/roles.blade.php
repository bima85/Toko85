<div class="container-fluid">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h3 class="card-title">Roles</h3>
      <div>
        @can('roles.create')
          <button wire:click="create" class="btn btn-sm btn-primary">New Role</button>
        @endcan
      </div>
    </div>
    <div class="card-body">
      @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
      @endif

      <div class="mb-3">
        <form wire:submit.prevent="save" class="form-inline">
          <div class="form-group mr-2">
            <input
              wire:model.defer="name"
              type="text"
              class="form-control"
              placeholder="Role name"
              required
            />
          </div>
          <button class="btn btn-success">Save</button>
          <button type="button" wire:click="resetForm" class="btn btn-secondary ml-2">
            Cancel
          </button>
        </form>
      </div>

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($roles as $role)
            <tr>
              <td>{{ $role->id }}</td>
              <td>{{ $role->name }}</td>
              <td>
                @can('roles.update')
                  <button wire:click="edit({{ $role->id }})" class="btn btn-sm btn-primary">
                    Edit
                  </button>
                @endcan

                @can('roles.delete')
                  <button wire:click="delete({{ $role->id }})" class="btn btn-sm btn-danger">
                    Delete
                  </button>
                @endcan
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div>
  <div class="row mb-3">
    <div class="col-8">
      <h4>Permissions</h4>
    </div>
    <div class="col-4 text-right">
      <button wire:click="create" class="btn btn-primary">Create Permission</button>
    </div>
  </div>

  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card">
    <div class="card-body">
      <div class="mb-3 row">
        <div class="col-md-6">
          <input
            wire:model.debounce.300ms="search"
            class="form-control"
            placeholder="Search permissions..."
          />
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">
              {{ $editingPermissionId ? 'Edit Permission' : 'Create Permission' }}
            </h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Name</label>
              <input wire:model.defer="name" class="form-control" />
              @error('name')
                <span class="text-danger">{{ $message }}</span>
              @enderror
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
            <th>Name</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($permissions as $p)
            <tr>
              <td>{{ $p->id }}</td>
              <td>{{ $p->name }}</td>
              <td class="text-right">
                <button wire:click="edit({{ $p->id }})" class="btn btn-sm btn-primary">
                  Edit
                </button>
                <button
                  wire:click="delete({{ $p->id }})"
                  onclick="return confirm('Delete permission?')"
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
        {{ $permissions->links() }}
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Permission Matrix</h3>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <input
          wire:model.debounce.300ms="search"
          class="form-control"
          placeholder="Search permissions (e.g. sales.create)"
        />
      </div>
      @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
      @endif

      @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      @can('permissions.create')
        <form wire:submit.prevent="createPermission(name)">
          <div class="input-group mb-3">
            <input wire:model.defer="name" class="form-control" placeholder="new.permission" />
            <div class="input-group-append">
              <button class="btn btn-primary">Add</button>
            </div>
          </div>
        </form>
      @endcan
    </div>
    <div class="card-body">
      @foreach ($groupedPermissions as $group => $perms)
        <div class="mb-4">
          <h5 class="mb-2">
            {{ ucfirst($group) }}
            <small class="text-muted">({{ $perms->count() }} permissions)</small>
          </h5>

          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead>
                <tr>
                  <th>Permission</th>
                  @foreach ($roles as $role)
                    <th class="text-center">
                      {{ $role->name }}
                      <div>
                        <button
                          wire:click.prevent="toggleGroup({{ $role->id }}, '{{ $group }}')"
                          class="btn btn-sm btn-link"
                        >
                          Toggle group
                        </button>
                      </div>
                    </th>
                  @endforeach

                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($perms as $perm)
                  <tr>
                    <td>{{ $perm->name }}</td>
                    @foreach ($roles as $role)
                      <td class="text-center">
                        <input
                          type="checkbox"
                          wire:click="toggle({{ $role->id }}, {{ $perm->id }})"
                          {{ $role->hasPermissionTo($perm) ? 'checked' : '' }}
                        />
                      </td>
                    @endforeach

                    <td>
                      @can('permissions.delete')
                        <button
                          wire:click.prevent="deletePermission({{ $perm->id }})"
                          class="btn btn-sm btn-danger"
                        >
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
      @endforeach
    </div>
  </div>
</div>

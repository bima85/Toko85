<div>
  <h4>Access Matrix</h4>

  <div>
    <h4>Access Matrix</h4>

    @if (session()->has('message'))
      <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <div class="d-flex align-items-start">
      <div style="min-width: 200px; max-width: 240px" class="me-4">
        <div class="list-group mb-3">
          @foreach ($roles as $role)
            <button
              type="button"
              wire:click="setCurrentRole('{{ $role }}')"
              class="list-group-item list-group-item-action text-start @if($currentRole === $role) active @endif"
              style="width: 100%"
            >
              {{ $role }}
            </button>
          @endforeach
        </div>

        <div class="d-grid gap-2">
          <button wire:click="selectAllForCurrent" class="btn btn-sm btn-outline-primary">
            Select all for role
          </button>
          <button wire:click="clearAllForCurrent" class="btn btn-sm btn-outline-secondary">
            Clear
          </button>
        </div>
      </div>

      <div class="flex-fill">
        <div class="card">
          <div class="card-body p-3" style="max-height: 70vh; overflow: auto; overflow-x: hidden">
            <div class="row g-3">
              @foreach ($menuItems as $route => $label)
                <div class="col-12 col-sm-6">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="matrix-{{ $currentRole }}-{{ $route }}"
                      wire:key="matrix-{{ $currentRole }}-{{ $route }}"
                      wire:click="toggle('{{ $currentRole }}','{{ $route }}')"
                      @if(isset($selected[$currentRole][$route])) checked @endif
                    />
                    <label
                      class="form-check-label ms-2"
                      for="matrix-{{ $currentRole }}-{{ $route }}"
                    >
                      {{ $label }}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="mt-3 d-flex justify-content-between align-items-center">
              <div>
                <small class="text-muted">
                  Current role:
                  <strong>{{ $currentRole }}</strong>
                </small>
              </div>
              <div>
                <button wire:click="save" class="btn btn-success">Save all</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

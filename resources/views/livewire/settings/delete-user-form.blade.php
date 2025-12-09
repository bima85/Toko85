<div>
  <!-- Delete Account Section -->
  <div class="card card-danger card-outline mt-3">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-trash-alt mr-2"></i>{{ __('Delete Account') }}
      </h3>
    </div>
    <div class="card-body">
      <div class="alert alert-danger" role="alert">
        <strong>{{ __('Danger Zone!') }}</strong><br />
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
      </div>
      <p class="text-muted mb-4">
        {{ __('Please be certain before proceeding as this action cannot be undone.') }}
      </p>
    </div>
    <div class="card-footer">
      <button
        type="button"
        class="btn btn-danger"
        data-toggle="modal"
        data-target="#deleteAccountModal"
      >
        <i class="fas fa-trash-alt mr-2"></i>{{ __('Delete My Account') }}
      </button>
    </div>
  </div>

  <!-- Delete Account Modal -->
  <div
    class="modal fade"
    id="deleteAccountModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="deleteAccountLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content border-danger">
        <div class="modal-header border-danger">
          <h5 class="modal-title text-danger" id="deleteAccountLabel">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('Delete Account') }}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <form wire:submit="deleteUser" class="modal-body">
          <div class="alert alert-danger">
            <strong>{{ __('Are you sure?') }}</strong><br />
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
          </div>

          <div class="form-group">
            <label for="password">
              <strong>{{ __('Password') }} <span class="text-danger">*</span></strong>
            </label>
            <input
              wire:model="password"
              type="password"
              class="form-control @error('password') is-invalid @enderror"
              id="password"
              placeholder="{{ __('Enter your password') }}"
              required
            />
            @error('password')
              <span class="invalid-feedback d-block">{{ $message }}</span>
            @enderror
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              {{ __('Cancel') }}
            </button>
            <button
              type="submit"
              class="btn btn-danger"
              wire:loading.attr="disabled"
              wire:loading.class="opacity-50"
            >
              <i class="fas fa-trash-alt mr-2" wire:loading.remove></i>
              <span wire:loading.remove>{{ __('Delete Account') }}</span>
              <span wire:loading>
                <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Deleting...') }}
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

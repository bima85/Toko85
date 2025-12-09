<div class="row">
  <div class="col-md-8">
    <!-- Update Password Card -->
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-lock mr-2"></i>{{ __('Update Password') }}
        </h3>
      </div>

      <form wire:submit="updatePassword" class="card-body">
        <!-- Alert Messages -->
        @if (session('status') === 'password-updated')
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
              &times;
            </button>
            <i class="icon fas fa-check"></i>
            {{ __('Password updated successfully!') }}
          </div>
        @endif

        <div class="alert alert-info">
          <i class="icon fas fa-info-circle"></i>
          {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </div>

        <!-- Current Password Field -->
        <div class="form-group">
          <label for="current_password">
            <strong>{{ __('Current Password') }} <span class="text-danger">*</span></strong>
          </label>
          <input
            wire:model="current_password"
            type="password"
            class="form-control @error('current_password') is-invalid @enderror"
            id="current_password"
            placeholder="{{ __('Enter your current password') }}"
            required
            autocomplete="current-password"
          />
          @error('current_password')
            <span class="invalid-feedback d-block">{{ $message }}</span>
          @enderror
        </div>

        <!-- New Password Field -->
        <div class="form-group">
          <label for="password">
            <strong>{{ __('New Password') }} <span class="text-danger">*</span></strong>
          </label>
          <input
            wire:model="password"
            type="password"
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            placeholder="{{ __('Enter your new password') }}"
            required
            autocomplete="new-password"
          />
          @error('password')
            <span class="invalid-feedback d-block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="form-group">
          <label for="password_confirmation">
            <strong>{{ __('Confirm Password') }} <span class="text-danger">*</span></strong>
          </label>
          <input
            wire:model="password_confirmation"
            type="password"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            id="password_confirmation"
            placeholder="{{ __('Confirm your new password') }}"
            required
            autocomplete="new-password"
          />
          @error('password_confirmation')
            <span class="invalid-feedback d-block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Submit Button -->
        <div class="form-group mt-4">
          <button
            type="submit"
            class="btn btn-primary"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
          >
            <i class="fas fa-save mr-2" wire:loading.remove></i>
            <span wire:loading.remove>{{ __('Update Password') }}</span>
            <span wire:loading>
              <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Updating...') }}
            </span>
          </button>
          <button type="reset" class="btn btn-secondary ml-2">
            <i class="fas fa-redo mr-2"></i>{{ __('Reset') }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Password Tips Sidebar -->
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-shield-alt mr-2"></i>{{ __('Password Tips') }}
        </h3>
      </div>
      <div class="card-body">
        <h5 class="mb-3">{{ __('Create a Strong Password:') }}</h5>
        <ul class="list-unstyled text-sm">
          <li class="mb-2">
            <i class="fas fa-check text-success mr-2"></i>
            {{ __('At least 8 characters long') }}
          </li>
          <li class="mb-2">
            <i class="fas fa-check text-success mr-2"></i>
            {{ __('Mix uppercase and lowercase letters') }}
          </li>
          <li class="mb-2">
            <i class="fas fa-check text-success mr-2"></i>
            {{ __('Include numbers (0-9)') }}
          </li>
          <li class="mb-2">
            <i class="fas fa-check text-success mr-2"></i>
            {{ __('Add special characters (!@#$%^&*)') }}
          </li>
          <li>
            <i class="fas fa-check text-success mr-2"></i>
            {{ __('Avoid common words and patterns') }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

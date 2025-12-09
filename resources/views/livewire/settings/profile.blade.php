<div class="row">
  <div class="col-md-8">
    <!-- Profile Card -->
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-user-circle mr-2"></i>{{ __('Edit Profile') }}
        </h3>
      </div>

      <form wire:submit="updateProfileInformation" class="card-body">
        <!-- Alert Messages -->
        @if (session('status') === 'profile-updated')
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
              &times;
            </button>
            <i class="icon fas fa-check"></i>
            {{ __('Profile updated successfully!') }}
          </div>
        @endif

        @if (session('status') === 'verification-link-sent')
          <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
              &times;
            </button>
            <i class="icon fas fa-info-circle"></i>
            {{ __('A new verification link has been sent to your email address.') }}
          </div>
        @endif

        <!-- Name Field -->
        <div class="form-group">
          <label for="name">
            <strong>{{ __('Full Name') }} <span class="text-danger">*</span></strong>
          </label>
          <input
            wire:model="name"
            type="text"
            class="form-control @error('name') is-invalid @enderror"
            id="name"
            placeholder="{{ __('Enter your full name') }}"
            required
            autofocus
            autocomplete="name"
          />
          @error('name')
            <span class="invalid-feedback d-block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Email Field -->
        <div class="form-group">
          <label for="email">
            <strong>{{ __('Email Address') }} <span class="text-danger">*</span></strong>
          </label>
          <input
            wire:model="email"
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            placeholder="{{ __('Enter your email address') }}"
            required
            autocomplete="email"
          />
          @error('email')
            <span class="invalid-feedback d-block">{{ $message }}</span>
          @enderror
        </div>

        <!-- Email Verification Status -->
        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
          <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
              &times;
            </button>
            <i class="icon fas fa-exclamation-triangle"></i>
            <strong>{{ __('Email Not Verified!') }}</strong><br />
            {{ __('Your email address is not verified yet.') }}
            <a
              href="#"
              wire:click.prevent="resendVerificationNotification"
              class="btn btn-sm btn-warning mt-2"
            >
              <i class="fas fa-envelope mr-1"></i>{{ __('Resend Verification Email') }}
            </a>
          </div>
        @endif

        <!-- Submit Button -->
        <div class="form-group mt-4">
          <button
            type="submit"
            class="btn btn-primary"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
          >
            <i class="fas fa-save mr-2" wire:loading.remove></i>
            <span wire:loading.remove>{{ __('Save Changes') }}</span>
            <span wire:loading>
              <i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Saving...') }}
            </span>
          </button>
          <button type="reset" class="btn btn-secondary ml-2">
            <i class="fas fa-redo mr-2"></i>{{ __('Reset') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Delete Account Section -->
    <livewire:settings.delete-user-form />
  </div>

  <!-- Profile Info Sidebar -->
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-info-circle mr-2"></i>{{ __('Account Information') }}
        </h3>
      </div>
      <div class="card-body">
        <div class="info-box">
          <span class="info-box-icon bg-info">
            <i class="fas fa-envelope"></i>
          </span>
          <div class="info-box-content">
            <span class="info-box-text">{{ __('Email Status') }}</span>
            <span class="info-box-number">
              @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                @if (auth()->user()->hasVerifiedEmail())
                  <span class="badge badge-success">{{ __('Verified') }}</span>
                @else
                  <span class="badge badge-warning">{{ __('Pending') }}</span>
                @endif
              @else
                <span class="badge badge-success">{{ __('N/A') }}</span>
              @endif
            </span>
          </div>
        </div>

        <hr />

        <div class="user-profile-info">
          <p class="text-muted mb-2">
            <strong>{{ __('Member Since:') }}</strong><br />
            <small>{{ auth()->user()->created_at->format('d M Y') }}</small>
          </p>
          <p class="text-muted mb-2">
            <strong>{{ __('Last Updated:') }}</strong><br />
            <small>{{ auth()->user()->updated_at->format('d M Y H:i') }}</small>
          </p>
        </div>
      </div>
    </div>

    <!-- Settings Links -->
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-cog mr-2"></i>{{ __('Other Settings') }}
        </h3>
      </div>
      <div class="card-body p-0">
        <ul class="list-unstyled">
          <li class="border-bottom">
            <a href="{{ route('user-password.edit') }}" class="d-block p-3 text-dark">
              <i class="fas fa-lock mr-2 text-primary"></i>{{ __('Change Password') }}
            </a>
          </li>
          <li class="border-bottom">
            <a href="{{ route('two-factor.show') }}" class="d-block p-3 text-dark">
              <i class="fas fa-shield-alt mr-2 text-primary"></i>{{ __('Two-Factor Authentication') }}
            </a>
          </li>
          <li>
            <a href="{{ route('appearance.edit') }}" class="d-block p-3 text-dark">
              <i class="fas fa-palette mr-2 text-primary"></i>{{ __('Appearance') }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

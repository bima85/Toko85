<div class="row">
  <div class="col-md-3">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{ __('Settings') }}</h3>
      </div>
      <div class="card-body p-0">
        <ul class="nav nav-pills flex-column">
          <li class="nav-item">
            <a href="{{ route('profile.edit') }}" class="nav-link" wire:navigate>
              <i class="fas fa-user mr-2"></i>
              {{ __('Profile') }}
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('user-password.edit') }}" class="nav-link" wire:navigate>
              <i class="fas fa-key mr-2"></i>
              {{ __('Password') }}
            </a>
          </li>
          @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <li class="nav-item">
              <a href="{{ route('two-factor.show') }}" class="nav-link" wire:navigate>
                <i class="fas fa-shield-alt mr-2"></i>
                {{ __('Two-Factor Auth') }}
              </a>
            </li>
          @endif

          <li class="nav-item">
            <a href="{{ route('appearance.edit') }}" class="nav-link" wire:navigate>
              <i class="fas fa-palette mr-2"></i>
              {{ __('Appearance') }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-md-9">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">{{ $heading ?? '' }}</h3>
        @if ($subheading ?? false)
          <p class="card-text">{{ $subheading }}</p>
        @endif
      </div>
      <div class="card-body">
        {{ $slot }}
      </div>
    </div>
  </div>
</div>

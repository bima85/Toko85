<x-layouts.auth>
  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">{{ __('Create an account') }}</p>
      <p class="text-center text-muted">
        {{ __('Enter your details below to create your account') }}
      </p>

      <!-- Session Status -->
      <x-auth-session-status class="text-center" :status="session('status')" />

      <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <!-- Name -->
        <div class="input-group mb-3">
          <input
            type="text"
            name="name"
            class="form-control"
            placeholder="{{ __('Full name') }}"
            required
            autofocus
            autocomplete="name"
            value="{{ old('name') }}"
          />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <!-- Email Address -->
        <div class="input-group mb-3">
          <input
            type="email"
            name="email"
            class="form-control"
            placeholder="email@example.com"
            required
            autocomplete="email"
            value="{{ old('email') }}"
          />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
          <input
            type="password"
            name="password"
            class="form-control"
            placeholder="{{ __('Password') }}"
            required
            autocomplete="new-password"
          />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="input-group mb-3">
          <input
            type="password"
            name="password_confirmation"
            class="form-control"
            placeholder="{{ __('Confirm password') }}"
            required
            autocomplete="new-password"
          />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">
              {{ __('Create account') }}
            </button>
          </div>
        </div>
      </form>

      <p class="mb-1 text-center">
        <a href="{{ route('login') }}" wire:navigate>
          {{ __('Already have an account? Log in') }}
        </a>
      </p>
    </div>
  </div>
</x-layouts.auth>

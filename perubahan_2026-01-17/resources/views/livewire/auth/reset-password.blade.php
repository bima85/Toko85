<x-layouts.auth>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">{{ __('Reset password') }}</p>
      <p class="text-center text-muted">{{ __('Please enter your new password below') }}</p>

      <!-- Session Status -->
      <x-auth-session-status class="text-center" :status="session('status')" />

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <!-- Token -->
        <input type="hidden" name="token" value="{{ request()->route('token') }}" />

        <!-- Email Address -->
        <div class="input-group mb-3">
          <input
            type="email"
            name="email"
            value="{{ request('email') }}"
            class="form-control"
            placeholder="{{ __('Email') }}"
            required
            autocomplete="email"
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
            <button
              type="submit"
              class="btn btn-primary btn-block"
              data-test="reset-password-button"
            >
              {{ __('Reset password') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-layouts.auth>

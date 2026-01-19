<x-layouts.auth>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">{{ __('Confirm password') }}</p>
      <p class="text-center text-muted">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
      </p>

      <x-auth-session-status class="text-center" :status="session('status')" />

      <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf

        <div class="input-group mb-3">
          <input
            type="password"
            name="password"
            class="form-control"
            placeholder="{{ __('Password') }}"
            required
            autocomplete="current-password"
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
              data-test="confirm-password-button"
            >
              {{ __('Confirm') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</x-layouts.auth>

<x-layouts.auth>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">{{ __('Forgot password') }}</p>
      <p class="text-center text-muted">
        {{ __('Enter your email to receive a password reset link') }}
      </p>

      <!-- Session Status -->
      <x-auth-session-status class="text-center" :status="session('status')" />

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-3">
          <input
            type="email"
            name="email"
            class="form-control"
            placeholder="email@example.com"
            required
            autofocus
          />
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <button
              type="submit"
              class="btn btn-primary btn-block"
              data-test="email-password-reset-link-button"
            >
              {{ __('Email password reset link') }}
            </button>
          </div>
        </div>
      </form>

      <p class="mb-1 text-center">
        <a href="{{ route('login') }}" wire:navigate>{{ __('Or, return to log in') }}</a>
      </p>
    </div>
  </div>
</x-layouts.auth>

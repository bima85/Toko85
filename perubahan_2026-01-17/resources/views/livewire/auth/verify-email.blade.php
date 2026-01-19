<x-layouts.auth>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">{{ __('Email Verification') }}</p>

      <p class="text-center">
        {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
      </p>

      @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success text-center">
          {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
      @endif

      <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">
              {{ __('Resend verification email') }}
            </button>
          </div>
        </div>
      </form>

      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <p class="mb-1 text-center">
          <button type="submit" class="btn btn-link" data-test="logout-button">
            {{ __('Log out') }}
          </button>
        </p>
      </form>
    </div>
  </div>
</x-layouts.auth>

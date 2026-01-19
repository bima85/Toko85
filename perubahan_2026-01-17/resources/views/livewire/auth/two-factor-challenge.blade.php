<x-layouts.auth>
  <div
    class="card"
    x-cloak
    x-data="{
      showRecoveryInput: @js($errors->has('recovery_code')),
      code: '',
      recovery_code: '',
      toggleInput() {
        this.showRecoveryInput = ! this.showRecoveryInput
        this.code = ''
        this.recovery_code = ''
        $dispatch('clear-2fa-auth-code')
        $nextTick(() => {
          this.showRecoveryInput
            ? this.$refs.recovery_code?.focus()
            : $dispatch('focus-2fa-auth-code')
        })
      },
    }"
  >
    <div class="card-body login-card-body">
      <div x-show="!showRecoveryInput">
        <p class="login-box-msg">{{ __('Authentication Code') }}</p>
        <p class="text-center text-muted">
          {{ __('Enter the authentication code provided by your authenticator application.') }}
        </p>
      </div>

      <div x-show="showRecoveryInput">
        <p class="login-box-msg">{{ __('Recovery Code') }}</p>
        <p class="text-center text-muted">
          {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        </p>
      </div>

      <form method="POST" action="{{ route('two-factor.login.store') }}">
        @csrf

        <div x-show="!showRecoveryInput">
          <div class="form-group text-center">
            <x-input-otp name="code" digits="6" autocomplete="one-time-code" x-model="code" />
          </div>
          @error('code')
            <p class="text-danger text-center">{{ $message }}</p>
          @enderror
        </div>

        <div x-show="showRecoveryInput">
          <div class="form-group">
            <input
              type="text"
              name="recovery_code"
              class="form-control"
              x-ref="recovery_code"
              x-bind:required="showRecoveryInput"
              autocomplete="one-time-code"
              x-model="recovery_code"
              placeholder="{{ __('Recovery Code') }}"
            />
          </div>
          @error('recovery_code')
            <p class="text-danger">{{ $message }}</p>
          @enderror
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">{{ __('Continue') }}</button>
          </div>
        </div>
      </form>

      <p class="mb-1 text-center">
        <a href="#" x-show="!showRecoveryInput" @click="toggleInput()">
          {{ __('login using a recovery code') }}
        </a>
        <a href="#" x-show="showRecoveryInput" @click="toggleInput()">
          {{ __('login using an authentication code') }}
        </a>
      </p>
    </div>
  </div>
</x-layouts.auth>

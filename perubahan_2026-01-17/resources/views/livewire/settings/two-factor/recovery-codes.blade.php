<div class="card" wire:cloak x-data="{ showRecoveryCodes: false }">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-lock mr-2"></i>
      {{ __('2FA Recovery Codes') }}
    </h3>
  </div>
  <div class="card-body">
    <p class="text-muted">
      {{ __('Recovery codes let you regain access if you lose your 2FA device. Store them in a secure password manager.') }}
    </p>

    <div class="mb-3">
      <button
        x-show="!showRecoveryCodes"
        class="btn btn-primary"
        @click="showRecoveryCodes = true;"
        aria-expanded="false"
        aria-controls="recovery-codes-section"
      >
        <i class="fas fa-eye mr-1"></i>
        {{ __('View Recovery Codes') }}
      </button>

      <button
        x-show="showRecoveryCodes"
        class="btn btn-primary"
        @click="showRecoveryCodes = false"
        aria-expanded="true"
        aria-controls="recovery-codes-section"
      >
        <i class="fas fa-eye-slash mr-1"></i>
        {{ __('Hide Recovery Codes') }}
      </button>

      @if (filled($recoveryCodes))
        <button
          x-show="showRecoveryCodes"
          class="btn btn-warning ml-2"
          wire:click="regenerateRecoveryCodes"
        >
          <i class="fas fa-sync mr-1"></i>
          {{ __('Regenerate Codes') }}
        </button>
      @endif
    </div>

    <div
      x-show="showRecoveryCodes"
      x-transition
      id="recovery-codes-section"
      x-bind:aria-hidden="!showRecoveryCodes"
    >
      @error('recoveryCodes')
        <div class="alert alert-danger">{{ $message }}</div>
      @enderror

      @if (filled($recoveryCodes))
        <div class="bg-light p-3 rounded">
          <code>
            @foreach ($recoveryCodes as $code)
              <div wire:loading.class="opacity-50">{{ $code }}</div>
            @endforeach
          </code>
        </div>
        <small class="text-muted">
          {{ __('Each recovery code can be used once to access your account and will be removed after use. If you need more, click Regenerate Codes above.') }}
        </small>
      @endif
    </div>
  </div>
</div>

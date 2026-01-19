<section class="w-full">
  @include('partials.settings-heading')

  <x-settings.layout
    :heading="__('Two Factor Authentication')"
    :subheading="__('Manage your two-factor authentication settings')"
  >
    <div class="row" wire:cloak>
      @if ($twoFactorEnabled)
        <div class="col-12">
          <div class="alert alert-success">
            <h5>
              <i class="icon fas fa-check"></i>
              {{ __('Enabled') }}
            </h5>
            <p>
              {{ __('With two-factor authentication enabled, you will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
            </p>
          </div>

          <livewire:settings.two-factor.recovery-codes :$requiresConfirmation />

          <button type="button" class="btn btn-danger" wire:click="disable">
            <i class="fas fa-shield-alt mr-1"></i>
            {{ __('Disable 2FA') }}
          </button>
        </div>
      @else
        <div class="col-12">
          <div class="alert alert-warning">
            <h5>
              <i class="icon fas fa-exclamation-triangle"></i>
              {{ __('Disabled') }}
            </h5>
            <p>
              {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
            </p>
          </div>

          <button type="button" class="btn btn-primary" wire:click="enable">
            <i class="fas fa-shield-check mr-1"></i>
            {{ __('Enable 2FA') }}
          </button>
        </div>
      @endif
    </div>
  </x-settings.layout>

  <!-- Modal -->
  <div
    class="modal fade"
    id="twoFactorModal"
    tabindex="-1"
    role="dialog"
    wire:model="showModal"
    wire:model.live="showModal"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ $this->modalConfig['title'] ?? 'Setup 2FA' }}</h4>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            wire:click="closeModal"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @if ($showVerificationStep)
            <div class="text-center">
              <x-input-otp
                :digits="6"
                name="code"
                wire:model="code"
                autocomplete="one-time-code"
              />
              @error('code')
                <p class="text-danger">{{ $message }}</p>
              @enderror
            </div>
          @else
            @error('setupData')
              <div class="alert alert-danger">{{ $message }}</div>
            @enderror

            <div class="text-center mb-3">
              @if (empty($qrCodeSvg))
                <div class="spinner-border" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
              @else
                <div class="bg-white p-3 rounded border">
                  {!! $qrCodeSvg !!}
                </div>
              @endif
            </div>

            <p class="text-muted">{{ $this->modalConfig['description'] ?? '' }}</p>

            <hr />

            <p class="text-center"><strong>{{ __('or, enter the code manually') }}</strong></p>

            <div class="input-group">
              @if (empty($manualSetupKey))
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <div class="spinner-border spinner-border-sm" role="status"></div>
                  </span>
                </div>
                <input type="text" class="form-control" readonly />
              @else
                <input type="text" class="form-control" readonly value="{{ $manualSetupKey }}" />
                <div class="input-group-append">
                  <button
                    class="btn btn-outline-secondary"
                    type="button"
                    onclick="copyToClipboard('{{ $manualSetupKey }}')"
                  >
                    <i class="fas fa-copy"></i>
                  </button>
                </div>
              @endif
            </div>
          @endif
        </div>
        <div class="modal-footer">
          @if ($showVerificationStep)
            <button type="button" class="btn btn-secondary" wire:click="resetVerification">
              {{ __('Back') }}
            </button>
            <button
              type="button"
              class="btn btn-primary"
              wire:click="confirmTwoFactor"
              :disabled="strlen($code) < 6"
            >
              {{ __('Confirm') }}
            </button>
          @else
            <button
              type="button"
              class="btn btn-primary"
              wire:click="showVerificationIfNecessary"
              :disabled="$errors->has('setupData')"
            >
              {{ $this->modalConfig['buttonText'] ?? 'Continue' }}
            </button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Success
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }

    // Show modal when showModal is true
    document.addEventListener('livewire:updated', function () {
        if (@this.showModal) {
            $('#twoFactorModal').modal('show');
        } else {
            $('#twoFactorModal').modal('hide');
        }
    });
  </script>
</section>

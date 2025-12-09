<div class="row">
  <div class="col-md-8">
    <!-- Appearance Settings Card -->
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-palette mr-2"></i>{{ __('Appearance Settings') }}
        </h3>
      </div>

      <div class="card-body">
        <!-- Alert Messages -->
        @if (session('status') === 'appearance-updated')
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
              &times;
            </button>
            <i class="icon fas fa-check"></i>
            {{ __('Appearance settings updated successfully!') }}
          </div>
        @endif

        <div class="form-group">
          <label><strong>{{ __('Theme') }} <span class="text-danger">*</span></strong></label>
          <div class="custom-control custom-radio">
            <input
              type="radio"
              class="custom-control-input"
              id="themeLight"
              value="light"
              x-model="$flux.appearance"
            />
            <label class="custom-control-label" for="themeLight">
              <i class="fas fa-sun mr-2 text-warning"></i>{{ __('Light Theme') }}
            </label>
          </div>

          <div class="custom-control custom-radio mt-2">
            <input
              type="radio"
              class="custom-control-input"
              id="themeDark"
              value="dark"
              x-model="$flux.appearance"
            />
            <label class="custom-control-label" for="themeDark">
              <i class="fas fa-moon mr-2 text-info"></i>{{ __('Dark Theme') }}
            </label>
          </div>

          <div class="custom-control custom-radio mt-2">
            <input
              type="radio"
              class="custom-control-input"
              id="themeSystem"
              value="system"
              x-model="$flux.appearance"
            />
            <label class="custom-control-label" for="themeSystem">
              <i class="fas fa-desktop mr-2 text-secondary"></i>{{ __('System (Auto)') }}
            </label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Theme Preview Sidebar -->
  <div class="col-md-4">
    <div class="card card-primary card-outline">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-eye mr-2"></i>{{ __('Preview') }}
        </h3>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <h6 class="mb-2"><strong>{{ __('Light Theme:') }}</strong></h6>
          <p class="text-muted text-sm">{{ __('Clean and bright interface with light colors. Perfect for day usage.') }}</p>
        </div>
        <div class="mb-3">
          <h6 class="mb-2"><strong>{{ __('Dark Theme:') }}</strong></h6>
          <p class="text-muted text-sm">{{ __('Dark interface with comfortable contrast. Great for low-light environments.') }}</p>
        </div>
        <div>
          <h6 class="mb-2"><strong>{{ __('System Theme:') }}</strong></h6>
          <p class="text-muted text-sm">{{ __('Automatically matches your device or OS appearance settings.') }}</p>
        </div>
      </div>
    </div>
  </div>
</div>

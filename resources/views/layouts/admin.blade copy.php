<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ \App\Helpers\PageTitleHelper::getTitle() }} - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/sales-index.css') }}" />

    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/css/OverlayScrollbars.min.css"
    />
    <link rel="stylesheet" href="/plugins/icheck-bootstrap/icheck-bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/rowgroup/1.3.0/css/rowGroup.bootstrap4.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    />
    <link rel="stylesheet" href="/css/adminlte.min.css" />

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="{{ asset('manifest.json') }}" />
    <meta name="theme-color" content="#007bff" />
    <meta
      name="description"
      content="Sistem Manajemen Toko - Kelola stok, penjualan, dan pembelian dengan mudah"
    />
    <!-- FAVICON FIX (STABLE) -->
    <link rel="icon" href="/images/icon.svg" type="image/x-icon" />
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32.png" />
    <link rel="icon" type="image/png" sizes="192x192" href="/images/icon-192.png" />
    <link rel="apple-touch-icon" sizes="192x192" href="/images/icon-192.png" />

    @stack('styles')

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    @stack('styles')
    @livewireStyles

    <style>
      /* Limit overrides to livewire-rendered modals only to avoid affecting other bootstrap modals */
      .livewire-modal {
        z-index: 2147483647 !important;
        position: fixed !important;
        inset: 0 !important;
        display: block !important;
      }
      /* Pin the dialog to the viewport center to avoid layout shifts when other page elements change */
      .livewire-modal .modal-dialog {
        z-index: 2147483647 !important;
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        max-width: 90% !important;
        width: auto !important;
      }
      .livewire-modal .modal-backdrop.show,
      .livewire-modal > .modal-backdrop.show {
        z-index: 2147483646 !important;
      }
    </style>

    <!-- Disable all CSS animations/transitions site-wide (instant UI) -->
    <style>
      /* Turn off CSS animations and transitions globally */
      *,
      *::before,
      *::after {
        -webkit-transition: none !important;
        -moz-transition: none !important;
        -o-transition: none !important;
        transition: none !important;
        -webkit-animation: none !important;
        -moz-animation: none !important;
        -o-animation: none !important;
        animation: none !important;
        scroll-behavior: auto !important;
      }

      /* Make Bootstrap modal and fade classes render immediately */
      .modal,
      .modal.fade,
      .fade {
        opacity: 1 !important;
        transition: none !important;
      }

      /* Disable common animated components */
      .collapse,
      .dropdown-menu,
      .tooltip,
      .popover,
      .modal-backdrop {
        transition: none !important;
        animation: none !important;
      }
    </style>

    <!-- PWA Service Worker Registration -->
    <script>
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker
          .register('/service-worker.js')
          .then((registration) => {
            // Service Worker registered successfully
          })
          .catch((error) => {
            console.error('Service Worker registration failed:', error);
          });
      }
    </script>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      @include('layouts.partials.navbar')
      @include('layouts.partials.sidebar')

      <div class="content-wrapper">
        <section class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1>{{ \App\Helpers\PageTitleHelper::getTitle() }}</h1>
              </div>
              <div class="col-sm-6"></div>
            </div>
          </div>
        </section>

        <section class="content">
          <div class="container-fluid">
            @isset($slot)
              {{ $slot }}
            @else
              @yield('content')
            @endisset
          </div>
        </section>
      </div>

      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">Version 1.0</div>
        <strong>&copy; {{ date('Y') }} Your Company.</strong>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
      // Global delete confirmation helper
      window.confirmDelete = function (
        message = 'Apakah Anda yakin ingin menghapus data ini?',
        title = 'Konfirmasi Hapus'
      ) {
        return Swal.fire({
          title: title,
          text: message,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal',
          customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary',
          },
        }).then((result) => {
          return result.isConfirmed;
        });
      };

      // Detect session expired (419 error) and auto-reload
      document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
          fail(({ status, preventDefault }) => {
            if (status === 419) {
              // Session expired - clear storage and reload (silent)

              // Clear site data
              if (window.caches) {
                caches.keys().then((names) => {
                  names.forEach((name) => caches.delete(name));
                });
              }

              // Reload page to get fresh session
              preventDefault();
              window.location.reload();
            }
          });
        });
      });
    </script>

    @stack('scripts')
    {{-- Modals pushed from views will render here at end of body to avoid stacking-context issues --}}
    @stack('modals')
    @livewireScripts
    <script>
      // Move livewire-rendered modal elements to document.body once to avoid stacking-context issues
      function moveLivewireModals() {
        try {
          // Move wrappers only once
          document.querySelectorAll('.livewire-modal').forEach(function (el) {
            if (!el.dataset.moved) {
              document.body.appendChild(el);
              el.dataset.moved = '1';
              try {
                console.debug('[moveLivewireModals] moved element', el);
              } catch (e) {}
            }
          });

          // Move any stray backdrops that belong to livewire modals
          document
            .querySelectorAll('.livewire-modal .modal-backdrop, .modal-backdrop')
            .forEach(function (el) {
              // avoid moving backdrops that are already at body or already marked
              if (!el.dataset.moved && el.parentNode && el.parentNode !== document.body) {
                document.body.appendChild(el);
                el.dataset.moved = '1';
                try {
                  console.debug('[moveLivewireModals] moved backdrop', el);
                } catch (e) {}
              }
            });
        } catch (e) {
          console.error('moveLivewireModals error', e);
        }
      }

      // run after load and after Livewire updates, but mark moved elements so we don't re-append repeatedly
      document.addEventListener('DOMContentLoaded', function () {
        moveLivewireModals();
        // also try once slightly later in case Livewire renders after DOMContentLoaded
        setTimeout(moveLivewireModals, 250);
      });

      document.addEventListener('livewire:load', function () {
        moveLivewireModals();
        setTimeout(moveLivewireModals, 250);
      });

      document.addEventListener('livewire:update', function () {
        // on updates only attempt to move newly created modals
        setTimeout(moveLivewireModals, 50);
      });
    </script>
  </body>
</html>

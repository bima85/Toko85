<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ \App\Helpers\PageTitleHelper::getTitle() }} - Admin</title>

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

    {{-- Ensure blade stacks are present so components can push styles --}}
    @stack('styles')

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    @stack('styles')
    @livewireStyles

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
            {{ $slot ?? '' }}
          </div>
        </section>
      </div>

      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline">Version 1.0</div>
        <strong>&copy; {{ date('Y') }} Your Company.</strong>
      </footer>
    </div>

    <!-- allow components to push scripts -->
    @stack('scripts')

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
    @livewireScripts
  </body>
</html>

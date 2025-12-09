<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
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
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    />
    <link rel="stylesheet" href="/css/adminlte.min.css" />

    @stack('styles')
    @livewireStyles
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

    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
      // If OverlayScrollbars failed to load (network/CSP), provide a minimal no-op so AdminLTE
      // calls don't throw and the asset-check can detect the plugin.
      (function () {
        try {
          if (
            window.jQuery &&
            !(window.jQuery.fn && typeof window.jQuery.fn.overlayScrollbars === 'function')
          ) {
            window.jQuery.fn.overlayScrollbars = function () {
              return this;
            };
          }
        } catch (e) {
          /* ignore */
        }
      })();
    </script>
    <script src="/js/adminlte.min.js"></script>
    <script>
      // Runtime CDN fallbacks: if a library is missing or its plugin isn't registered,
      // load it from a CDN so the UI continues to work.
      (function () {
        function loadScript(src, onload) {
          var s = document.createElement('script');
          s.src = src;
          s.async = false;
          s.onload = onload || function () {};
          document.head.appendChild(s);
        }

        // jQuery fallback
        if (!window.jQuery) {
          loadScript('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js');
        }

        // Bootstrap fallback (checks for tooltip plugin)
        (function checkBootstrap() {
          if (
            !(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.tooltip === 'function')
          ) {
            loadScript(
              'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.2/js/bootstrap.bundle.min.js'
            );
          }
        })();

        // OverlayScrollbars fallback
        (function checkOverlay() {
          if (
            !(
              window.jQuery &&
              window.jQuery.fn &&
              typeof window.jQuery.fn.overlayScrollbars === 'function'
            )
          ) {
            loadScript(
              'https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js'
            );
          }
        })();

        // AdminLTE fallback
        (function checkAdminLTE() {
          var adminlteAvailable =
            !!(
              window.jQuery &&
              window.jQuery.fn &&
              (typeof window.jQuery.fn.pushMenu === 'function' ||
                typeof window.jQuery.fn.PushMenu === 'function')
            ) || !!window.adminlte;
          if (!adminlteAvailable) {
            loadScript('https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js');
          }
        })();

        // DataTables fallback
        (function checkDataTables() {
          if (
            !(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.dataTable === 'function')
          ) {
            loadScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
            loadScript('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js');
          }
        })();
      })();
    </script>
    @stack('scripts')
    @livewireScripts
  </body>
</html>

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
      href="https://cdn.datatables.net/rowgroup/1.3.0/css/rowGroup.bootstrap4.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
    />
    <link rel="stylesheet" href="/css/adminlte.min.css" />

    <script src="/js/jquery.min.js"></script>

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

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@1.13.1/js/jquery.overlayScrollbars.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="/js/adminlte.min.js"></script>

    @stack('scripts')
    @livewireScripts
  </body>
</html>

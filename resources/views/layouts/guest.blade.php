<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>@yield('title', 'AdminLTE')</title>

    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css?v={{ time() }}"
    />
    <link
      rel="stylesheet"
      href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}?v={{ time() }}"
    />
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}?v={{ time() }}" />

    @stack('styles')
    @livewireStyles
  </head>
  <body class="hold-transition login-page" style="min-height: 100vh">
    {{ $slot }}

    <script src="{{ asset('js/jquery.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/adminlte.min.js') }}?v={{ time() }}"></script>
    @stack('scripts')
    @livewireScripts
  </body>
</html>

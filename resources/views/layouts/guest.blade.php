<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'AdminLTE')</title>

    @stack('styles')
    @livewireStyles
  </head>
  <body class="hold-transition login-page">
    {{ $slot }}

    @stack('scripts')
    @livewireScripts
  </body>
</html>

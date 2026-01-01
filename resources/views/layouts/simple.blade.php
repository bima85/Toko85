<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simple</title>

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
    />

    @livewireStyles
  </head>
  <body>
    @yield('content')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @livewireScripts
  </body>
</html>

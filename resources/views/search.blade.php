<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hasil Pencarian - SHOP 85</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      rel="stylesheet"
    />
  </head>
  <body class="bg-amber-50">
    <div class="container mx-auto px-4 py-20">
      <h1 class="text-3xl font-bold text-amber-900 mb-4">Hasil Pencarian</h1>
      <p class="text-amber-700">
        Pencarian untuk:
        <strong>{{ request('q') }}</strong>
      </p>
      <p class="mt-4 text-gray-600">Fitur pencarian sedang dalam pengembangan.</p>
      <a
        href="{{ route('home') }}"
        class="mt-6 inline-block bg-amber-700 text-white px-6 py-3 rounded-full hover:bg-amber-800"
      >
        Kembali ke Beranda
      </a>
    </div>
  </body>
</html>

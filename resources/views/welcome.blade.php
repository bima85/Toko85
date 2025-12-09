<!-- prettier-ignore-start -->
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="SHOP 85 - Toko Beras Premium Nomor 1 Indonesia. Beras pilihan berkualitas dengan harga terjangkau dan pengiriman cepat."
    />
    <meta
      name="keywords"
      content="beras premium, toko beras, beras organik, beras berkualitas, belanja beras online"
    />
    <title>SHOP 85 - Toko Beras Premium Indonesia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      body {
        font-family: 'Inter', sans-serif;
      }
      .font-heading {
        font-family: 'Playfair Display', serif;
      }

      .hero-section {
        background:
          linear-gradient(135deg, rgba(120, 53, 15, 0.8), rgba(180, 83, 9, 0.8)),
          url('https://images.unsplash.com/photo-1585707032515-6f4ee3991856?q=80&w=2070')
            center/cover;
        min-height: 600px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
      }

      .hero-content {
        animation: slideInUp 0.8s ease-out;
      }

      @keyframes slideInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .feature-card {
        transition: all 0.3s ease;
      }

      .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(180, 83, 9, 0.15);
      }

      .product-card {
        transition: all 0.3s ease;
      }

      .product-card:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      }

      .gradient-text {
        background: linear-gradient(135deg, #b45309, #92400e);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
      }

      .scroll-smooth {
        scroll-behavior: smooth;
      }
    </style>
  </head>
  <body class="bg-amber-50 scroll-smooth">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
          <!-- Logo -->
          <div class="flex items-center gap-3 group">
            <div
              class="w-12 h-12 bg-gradient-to-br from-amber-600 to-amber-800 rounded-full flex items-center justify-center text-white text-xl font-bold shadow-lg group-hover:shadow-xl transition"
            >
              85
            </div>
            <div>
              <h1 class="text-2xl font-heading font-bold text-amber-900">SHOP 85</h1>
              <p class="text-xs text-amber-600 font-semibold">Beras Premium Indonesia</p>
            </div>
          </div>

          <!-- Desktop Menu -->
          <div class="hidden lg:flex items-center gap-8">
            <a
              href="#tentang"
              class="font-medium transition"
              style="color: #78350f; text-decoration: none"
            >
              Tentang
            </a>
            <a
              href="#produk"
              class="font-medium transition"
              style="color: #78350f; text-decoration: none"
            >
              Produk
            </a>
            <a
              href="#keunggulan"
              class="font-medium transition"
              style="color: #78350f; text-decoration: none"
            >
              Keunggulan
            </a>
            <a
              href="#kontak"
              class="font-medium transition"
              style="color: #78350f; text-decoration: none"
            >
              Kontak
            </a>
          </div>

          <!-- Right Section -->
          <div class="flex items-center gap-4">
            <!-- Search (hidden on mobile) -->
            <div class="hidden md:flex relative">
              <input
                type="text"
                placeholder="Cari beras..."
                class="px-4 py-2 rounded-full border-2 focus:outline-none text-sm w-32 lg:w-40 transition"
                style="border-color: #fcd34d; background: #fffbeb"
                onfocus="this.style.borderColor='#b45309'"
              />
              <i class="fas fa-search absolute right-3 top-2.5" style="color: #b45309"></i>
            </div>

            <!-- Auth Buttons -->
            <div class="hidden sm:flex gap-3">
              @auth
                <a
                  href="{{ route('dashboard') }}"
                  class="text-amber-800 hover:text-amber-600 font-medium transition"
                >
                  <i class="fas fa-user-circle text-xl"></i>
                </a>
              @else
                <a
                  href="{{ route('login') }}"
                  class="px-4 py-2 text-amber-800 border-2 border-amber-700 rounded-full font-medium hover:bg-amber-50 transition whitespace-nowrap"
                >
                  Masuk
                </a>
              @endauth
            </div>

            <!-- Cart -->
            {{--
              <a href="{{ route('cart') }}" class="relative text-amber-800 hover:text-amber-600 text-xl transition">
              <i class="fas fa-shopping-cart"></i>
              <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">5</span>
              </a>
            --}}

            <!-- Mobile Menu Toggle -->
            <button id="mobileMenuBtn" class="lg:hidden text-amber-800 text-2xl focus:outline-none">
              <i class="fas fa-bars"></i>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-amber-100 pb-4">
          <a href="#tentang" class="block px-4 py-3 text-amber-800 hover:bg-amber-50 font-medium">
            Tentang
          </a>
          <a href="#produk" class="block px-4 py-3 text-amber-800 hover:bg-amber-50 font-medium">
            Produk
          </a>
          <a
            href="#keunggulan"
            class="block px-4 py-3 text-amber-800 hover:bg-amber-50 font-medium"
          >
            Keunggulan
          </a>
          <a href="#kontak" class="block px-4 py-3 text-amber-800 hover:bg-amber-50 font-medium">
            Kontak
          </a>
          @guest
            <div class="px-4 py-3 border-t border-amber-100 mt-3 space-y-2">
              <a
                href="{{ route('login') }}"
                class="block px-4 py-2 text-center text-amber-800 border-2 border-amber-700 rounded-full font-medium hover:bg-amber-50"
              >
                Masuk
              </a>
              <a
                href="{{ route('login') }}"
                class="block px-4 py-2 text-center rounded-full font-medium"
                style="background-color: #b45309; color: white"
              >
                Daftar
              </a>
            </div>
          @endguest
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 hero-content">
        <div class="max-w-2xl">
          <h2 class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold mb-4 drop-shadow-lg">
            Beras Pilihan Nomor 1
          </h2>
          <p class="text-lg md:text-xl mb-8 text-amber-50 leading-relaxed">
            Lebih dari 85 jenis beras premium dari seluruh Indonesia. Kualitas terjamin, harga
            terjangkau, dan pengiriman cepat ke seluruh nusantara.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <a
              href="#produk"
              class="px-8 py-3 rounded-full font-bold text-lg transition shadow-xl text-center"
              style="background-color: white; color: #78350f"
            >
              Lihat Koleksi
            </a>
            <a
              href="#kontak"
              class="px-8 py-3 rounded-full font-bold text-lg transition shadow-xl text-center"
              style="border: 2px solid white; color: white"
            >
              Hubungi Kami
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Tentang Section -->
    <section id="tentang" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <h3 class="text-3xl md:text-4xl font-heading font-bold text-amber-900 mb-6">
              Tentang SHOP 85
            </h3>
            <p class="text-gray-600 mb-4 leading-relaxed">
              SHOP 85 adalah toko beras terpercaya dengan pengalaman lebih dari 25 tahun melayani
              keluarga Indonesia. Kami berkomitmen menyediakan beras berkualitas premium dengan
              harga yang terjangkau.
            </p>
            <p class="text-gray-600 mb-6 leading-relaxed">
              Setiap produk melalui quality control ketat untuk memastikan Anda mendapatkan beras
              terbaik. Kami memiliki 85 cabang di seluruh Indonesia dan terus berkembang untuk
              melayani Anda lebih baik.
            </p>
            <div class="space-y-3">
              <div class="flex items-center gap-3">
                <i class="fas fa-check text-green-500 text-xl"></i>
                <span class="text-gray-700">100% Beras Asli & Fresh</span>
              </div>
              <div class="flex items-center gap-3">
                <i class="fas fa-check text-green-500 text-xl"></i>
                <span class="text-gray-700">Harga Kompetitif Terjamin</span>
              </div>
              <div class="flex items-center gap-3">
                <i class="fas fa-check text-green-500 text-xl"></i>
                <span class="text-gray-700">Pengiriman Cepat & Aman</span>
              </div>
            </div>
          </div>
          <div>
            <img
              src="https://images.unsplash.com/photo-1586985289688-cacf15b3c3d9?q=80&w=600"
              alt="Beras Premium"
              class="rounded-2xl shadow-2xl w-full"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Produk Section -->
    <section id="produk" class="py-20 bg-amber-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h3 class="text-3xl md:text-4xl font-heading font-bold text-amber-900 mb-4">
            Koleksi Beras Kami
          </h3>
          <p class="text-gray-600 max-w-2xl mx-auto">
            Pilihan lengkap beras premium dari berbagai daerah dengan kualitas terjamin
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
          <!-- Product Card 1 -->
          <div class="product-card bg-white rounded-2xl shadow-md p-4 text-center hover:shadow-xl">
            <div
              class="w-24 h-24 md:w-28 md:h-28 mx-auto mb-4 rounded-xl bg-gradient-to-br from-amber-100 to-amber-50 flex items-center justify-center"
            >
              <img
                src="https://images.unsplash.com/photo-1586985289688-cacf15b3c3d9?q=80&w=150"
                alt="Beras Putih"
                class="w-20 h-20 object-cover rounded-lg"
              />
            </div>
            <h4 class="font-bold text-amber-900 text-sm md:text-base mb-2">Beras Putih Premium</h4>
            <p class="text-xs text-amber-600 font-semibold">Rp 8.500/kg</p>
          </div>

          <!-- Product Card 2 -->
          <div class="product-card bg-white rounded-2xl shadow-md p-4 text-center hover:shadow-xl">
            <div
              class="w-24 h-24 md:w-28 md:h-28 mx-auto mb-4 rounded-xl bg-green-100 flex items-center justify-center"
            >
              <i class="fas fa-leaf text-5xl text-green-600"></i>
            </div>
            <h4 class="font-bold text-amber-900 text-sm md:text-base mb-2">Beras Organik</h4>
            <p class="text-xs text-amber-600 font-semibold">Rp 12.000/kg</p>
          </div>

          <!-- Product Card 3 -->
          <div class="product-card bg-white rounded-2xl shadow-md p-4 text-center hover:shadow-xl">
            <div
              class="w-24 h-24 md:w-28 md:h-28 mx-auto mb-4 rounded-xl bg-red-100 flex items-center justify-center"
            >
              <i class="fas fa-heart text-5xl text-red-600"></i>
            </div>
            <h4 class="font-bold text-amber-900 text-sm md:text-base mb-2">Beras Merah & Hitam</h4>
            <p class="text-xs text-amber-600 font-semibold">Rp 15.000/kg</p>
          </div>

          <!-- Product Card 4 -->
          <div class="product-card bg-white rounded-2xl shadow-md p-4 text-center hover:shadow-xl">
            <div
              class="w-24 h-24 md:w-28 md:h-28 mx-auto mb-4 rounded-xl bg-yellow-100 flex items-center justify-center"
            >
              <i class="fas fa-seedling text-5xl text-yellow-600"></i>
            </div>
            <h4 class="font-bold text-amber-900 text-sm md:text-base mb-2">Beras Khas Daerah</h4>
            <p class="text-xs text-amber-600 font-semibold">Rp 10.000/kg</p>
          </div>

          <!-- Product Card 5 -->
          <div class="product-card bg-white rounded-2xl shadow-md p-4 text-center hover:shadow-xl">
            <div
              class="w-24 h-24 md:w-28 md:h-28 mx-auto mb-4 rounded-xl bg-purple-100 flex items-center justify-center"
            >
              <i class="fas fa-mortar-pestle text-5xl text-purple-600"></i>
            </div>
            <h4 class="font-bold text-amber-900 text-sm md:text-base mb-2">Beras Diet Khusus</h4>
            <p class="text-xs text-amber-600 font-semibold">Rp 18.000/kg</p>
          </div>
        </div>

        <div class="text-center mt-12">
          <a
            href="{{ route('search') }}"
            class="inline-block px-10 py-3 rounded-full font-bold text-lg transition shadow-lg"
            style="background-color: #b45309; color: white"
          >
            Lihat Semua Produk
          </a>
        </div>
      </div>
    </section>

    <!-- Keunggulan Section -->
    <section id="keunggulan" class="py-20 bg-gradient-to-br from-amber-900 to-amber-800 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h3 class="text-3xl md:text-4xl font-heading font-bold mb-4">Mengapa Pilih SHOP 85?</h3>
          <p class="text-amber-100 max-w-2xl mx-auto">
            Komitmen kami untuk memberikan yang terbaik bagi setiap pelanggan
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Feature 1 -->
          <div
            class="feature-card bg-amber-800/50 rounded-2xl p-8 border border-amber-700/50 backdrop-blur"
          >
            <i class="fas fa-shipping-fast text-5xl mb-4 text-amber-300"></i>
            <h4 class="text-xl font-bold mb-3">Pengiriman Cepat</h4>
            <p class="text-amber-100">
              Sampai hari yang sama untuk area Jabodetabek, 2-3 hari ke seluruh Indonesia
            </p>
          </div>

          <!-- Feature 2 -->
          <div
            class="feature-card bg-amber-800/50 rounded-2xl p-8 border border-amber-700/50 backdrop-blur"
          >
            <i class="fas fa-certificate text-5xl mb-4 text-amber-300"></i>
            <h4 class="text-xl font-bold mb-3">Kualitas Terjamin</h4>
            <p class="text-amber-100">
              Setiap produk lolos quality control ketat dan bersertifikat halal
            </p>
          </div>

          <!-- Feature 3 -->
          <div
            class="feature-card bg-amber-800/50 rounded-2xl p-8 border border-amber-700/50 backdrop-blur"
          >
            <i class="fas fa-store text-5xl mb-4 text-amber-300"></i>
            <h4 class="text-xl font-bold mb-3">85 Cabang</h4>
            <p class="text-amber-100">Ribuan pelanggan puas tersebar di 34 provinsi Indonesia</p>
          </div>

          <!-- Feature 4 -->
          <div
            class="feature-card bg-amber-800/50 rounded-2xl p-8 border border-amber-700/50 backdrop-blur"
          >
            <i class="fas fa-tags text-5xl mb-4 text-amber-300"></i>
            <h4 class="text-xl font-bold mb-3">Harga Kompetitif</h4>
            <p class="text-amber-100">
              Harga grosir untuk pembelian dalam jumlah besar dengan diskon menarik
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimoni Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h3 class="text-3xl md:text-4xl font-heading font-bold text-amber-900 mb-4">
            Testimoni Pelanggan
          </h3>
          <p class="text-gray-600">Kepuasan pelanggan adalah prioritas utama kami</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Testimoni 1 -->
          <div class="bg-amber-50 rounded-2xl p-6 shadow-md">
            <div class="flex items-center gap-1 mb-4">
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
            </div>
            <p class="text-gray-700 mb-4">
              "Beras berkualitas dan harga sangat terjangkau. Pengiriman cepat, packing rapi. Saya
              jadi pelanggan setia SHOP 85!"
            </p>
            <p class="font-bold text-amber-900">Siti Nurhaliza - Jakarta</p>
          </div>

          <!-- Testimoni 2 -->
          <div class="bg-amber-50 rounded-2xl p-6 shadow-md">
            <div class="flex items-center gap-1 mb-4">
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
            </div>
            <p class="text-gray-700 mb-4">
              "Pelayanan customer service nya luar biasa responsif. Rekomendasi produk tepat sesuai
              kebutuhan keluarga saya."
            </p>
            <p class="font-bold text-amber-900">Budi Santoso - Surabaya</p>
          </div>

          <!-- Testimoni 3 -->
          <div class="bg-amber-50 rounded-2xl p-6 shadow-md">
            <div class="flex items-center gap-1 mb-4">
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
              <i class="fas fa-star text-yellow-400"></i>
            </div>
            <p class="text-gray-700 mb-4">
              "Beras organik dari SHOP 85 benar-benar sehat. Cuaca mendung pun beras tetap fresh dan
              tidak lembab."
            </p>
            <p class="font-bold text-amber-900">Rini Wijaya - Bandung</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-amber-700 to-amber-900 text-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="text-3xl md:text-4xl font-heading font-bold mb-6">
          Siap Memesan Beras Premium?
        </h3>
        <p class="text-xl mb-8 text-amber-100">
          Dapatkan pengalaman berbelanja terbaik dengan pelayanan profesional kami
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="{{ route('search') }}"
            class="px-8 py-3 rounded-full font-bold text-lg transition shadow-xl"
            style="background-color: white; color: #b45309"
          >
            Mulai Belanja
          </a>
          <a
            href="#kontak"
            class="px-8 py-3 rounded-full font-bold text-lg transition"
            style="border: 2px solid white; color: white"
          >
            Hubungi Kami
          </a>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="bg-gray-900 text-gray-300 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
          <!-- Tentang -->
          <div>
            <h4 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
              <i class="fas fa-store text-amber-500"></i>
              SHOP 85
            </h4>
            <p class="text-sm leading-relaxed">
              Toko beras premium terpercaya dengan 25 tahun pengalaman melayani keluarga Indonesia.
            </p>
          </div>

          <!-- Produk -->
          <div>
            <h4 class="text-white font-bold text-lg mb-4">Produk</h4>
            <ul class="space-y-2 text-sm">
              <li>
                <a href="{{ route('search') }}" class="hover:text-amber-400 transition">
                  Semua Produk
                </a>
              </li>
              <li>
                <a href="{{ route('search') }}" class="hover:text-amber-400 transition">
                  Beras Premium
                </a>
              </li>
              <li>
                <a href="{{ route('search') }}" class="hover:text-amber-400 transition">
                  Beras Organik
                </a>
              </li>
              <li>
                <a href="{{ route('search') }}" class="hover:text-amber-400 transition">
                  Promo Spesial
                </a>
              </li>
            </ul>
          </div>

          <!-- Layanan -->
          <div>
            <h4 class="text-white font-bold text-lg mb-4">Layanan</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-amber-400 transition">Cara Pesan</a></li>
              <li><a href="#" class="hover:text-amber-400 transition">Pengiriman</a></li>
              <li><a href="#" class="hover:text-amber-400 transition">Grosir & Reseller</a></li>
              <li><a href="#" class="hover:text-amber-400 transition">FAQ</a></li>
            </ul>
          </div>

          <!-- Kontak -->
          <div>
            <h4 class="text-white font-bold text-lg mb-4">Hubungi Kami</h4>
            <ul class="space-y-3 text-sm">
              <li class="flex items-start gap-2">
                <i class="fas fa-phone text-amber-400 mt-1"></i>
                <span>0855-8585-8585</span>
              </li>
              <li class="flex items-start gap-2">
                <i class="fas fa-envelope text-amber-400 mt-1"></i>
                <span>info@shop85.co.id</span>
              </li>
              <li class="flex items-start gap-2">
                <i class="fas fa-map-marker-alt text-amber-400 mt-1"></i>
                <span>Jakarta Pusat, Indonesia</span>
              </li>
            </ul>
            <div class="flex gap-4 mt-4">
              <a href="#" class="text-amber-400 hover:text-amber-300 text-xl transition">
                <i class="fab fa-whatsapp"></i>
              </a>
              <a href="#" class="text-amber-400 hover:text-amber-300 text-xl transition">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="text-amber-400 hover:text-amber-300 text-xl transition">
                <i class="fab fa-facebook"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="border-t border-gray-700 pt-8 text-center text-sm">
          <p>&copy; 2025 SHOP 85 - Toko Beras Premium Indonesia. Semua Hak Cipta Dilindungi.</p>
        </div>
      </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const mobileMenu = document.getElementById('mobileMenu');

      mobileMenuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });

      // Close mobile menu when link clicked
      document.querySelectorAll('#mobileMenu a').forEach((link) => {
        link.addEventListener('click', () => {
          mobileMenu.classList.add('hidden');
        });
      });

      // Smooth scroll
      document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
          const href = this.getAttribute('href');
          if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
              target.scrollIntoView({ behavior: 'smooth' });
            }
          }
        });
      });
    </script>
  </body>
</html>
<!-- prettier-ignore-end -->

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
    <span class="brand-text font-weight-light">Toko 85</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <nav class="mt-2">
      <ul
        class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false"
      >
        <!-- Dashboard -->
        <li class="nav-item">
          <a
            href="{{ route('admin.dashboard') }}"
            class="nav-link @if(request()->routeIs('admin.dashboard')) active @endif"
          >
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Master Data Section -->
        @php
          $masterDataRoutes = ['admin.users', 'admin.categories', 'admin.subcategories', 'admin.units', 'admin.suppliers', 'admin.customers', 'admin.warehouses', 'admin.stores'];
        @endphp

        <li
          class="nav-item @if(\App\Helpers\NavHelper::isRouteActive($masterDataRoutes)) menu-open @endif"
        >
          <a
            href="#"
            class="nav-link @if(\App\Helpers\NavHelper::isRouteActive($masterDataRoutes)) active @endif"
          >
            <i class="nav-icon fas fa-cogs"></i>
            <p>
              Master Data
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a
                href="{{ route('admin.users') }}"
                class="nav-link @if(request()->routeIs('admin.users')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.categories') }}"
                class="nav-link @if(request()->routeIs('admin.categories')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Kategori</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.subcategories') }}"
                class="nav-link @if(request()->routeIs('admin.subcategories')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Subkategori</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.units') }}"
                class="nav-link @if(request()->routeIs('admin.units')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Unit</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.suppliers') }}"
                class="nav-link @if(request()->routeIs('admin.suppliers')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Pemasok</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.customers') }}"
                class="nav-link @if(request()->routeIs('admin.customers')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Pelanggan</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.warehouses') }}"
                class="nav-link @if(request()->routeIs('admin.warehouses')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Gudang</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('admin.stores') }}"
                class="nav-link @if(request()->routeIs('admin.stores')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Toko</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Products Section -->
        <li class="nav-item">
          <a
            href="{{ route('admin.products') }}"
            class="nav-link @if(request()->routeIs('admin.products')) active @endif"
          >
            <i class="nav-icon fas fa-box"></i>
            <p>Produk</p>
          </a>
        </li>

        <!-- Pembelian Section -->
        <li class="nav-item @if(request()->routeIs('admin.purchases')) menu-open @endif">
          <a href="#" class="nav-link @if(request()->routeIs('admin.purchases')) active @endif">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>
              Pembelian
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a
                href="{{ route('admin.purchases') }}"
                class="nav-link @if(request()->routeIs('admin.purchases')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Daftar Pembelian</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('admin.purchases') }}#transaksi" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Transaksi Pembelian</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Penjualan Section -->
        <li class="nav-item @if(request()->routeIs('admin.sales')) menu-open @endif">
          <a href="#" class="nav-link @if(request()->routeIs('admin.sales')) active @endif">
            <i class="nav-icon fas fa-dollar-sign"></i>
            <p>
              Penjualan
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a
                href="{{ route('admin.sales') }}"
                class="nav-link @if(request()->routeIs('admin.sales')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Daftar Penjualan</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Stock Management Section -->
        @php
          $stockRoutes = ['admin.stock-reports', 'stock-batches.index'];
        @endphp

        <li
          class="nav-item @if(\App\Helpers\NavHelper::isRouteActive($stockRoutes)) menu-open @endif"
        >
          <a
            href="#"
            class="nav-link @if(\App\Helpers\NavHelper::isRouteActive($stockRoutes)) active @endif"
          >
            <i class="nav-icon fas fa-warehouse"></i>
            <p>
              Manajemen Stok
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a
                href="{{ route('admin.stock-reports') }}"
                class="nav-link @if(request()->routeIs('admin.stock-reports')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Laporan Stok</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('stock-batches.index') }}"
                class="nav-link @if(request()->routeIs('stock-batches.index')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Stok Tumpukan</p>
              </a>
            </li>
            <li class="nav-item">
              <a
                href="{{ route('stock-card.index') }}"
                class="nav-link @if(request()->routeIs('stock-card.*')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Kartu Stok</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Reports & Analytics Section -->
        <li class="nav-item @if(request()->routeIs('admin.transactions')) menu-open @endif">
          <a href="#" class="nav-link @if(request()->routeIs('admin.transactions')) active @endif">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
              Laporan & Analitik
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a
                href="{{ route('admin.transactions') }}"
                class="nav-link @if(request()->routeIs('admin.transactions')) active @endif"
              >
                <i class="far fa-circle nav-icon"></i>
                <p>Historis Transaksi</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</aside>

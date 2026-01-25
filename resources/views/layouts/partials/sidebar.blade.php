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

        <!-- Kelola Transaksi -->
        @php
          $currentUser = auth()->user();

          // Helper checks: role OR permission helpers may not exist on simple users
          $isSuperadmin = $currentUser && method_exists($currentUser, 'hasRole') && $currentUser->hasRole('superadmin');
          $isAdmin = $currentUser && method_exists($currentUser, 'hasAnyRole') && $currentUser->hasAnyRole(['admin', 'superadmin']);

          // Transactions visibility: explicit permission or umbrella permission
          $canManageTransactions = $currentUser && ((method_exists($currentUser, 'hasPermissionTo') && $currentUser->hasPermissionTo('transactions.manage')) || (method_exists($currentUser, 'hasPermissionTo') && ($currentUser->hasPermissionTo('purchases.view') || $currentUser->hasPermissionTo('sales.view'))) || $isAdmin);

          // Master data visibility: show if any of these view permissions exist or admin role
          $masterPerms = ['users.view', 'products.view', 'categories.view', 'subcategories.view', 'units.view', 'suppliers.view', 'customers.view', 'warehouses.view', 'stores.view', 'roles.view', 'permissions.view'];
          $showMaster = false;
          if ($currentUser) {
            foreach ($masterPerms as $p) {
              if (method_exists($currentUser, 'hasPermissionTo') && $currentUser->hasPermissionTo($p)) {
                $showMaster = true;
                break;
              }
            }
            if (! $showMaster) {
              $showMaster = $isAdmin;
            }
          }
        @endphp

        @if ($canManageTransactions)
          <li class="nav-item">
            <a
              href="{{ route('admin.transactions.manage') }}"
              class="nav-link @if(request()->routeIs('admin.transactions.manage')) active @endif"
            >
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>Kelola Transaksi</p>
            </a>
          </li>
        @endif

        <!-- Master Data Section -->
        @if ($showMaster)
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
              <!-- Products Section -->
              @can('products.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.products') }}"
                    class="nav-link @if(request()->routeIs('admin.products')) active @endif"
                  >
                    <i class="nav-icon fas fa-box"></i>
                    <p>Produk</p>
                  </a>
                </li>
              @endcan

              @can('users.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.users') }}"
                    class="nav-link @if(request()->routeIs('admin.users')) active @endif"
                  >
                    <i class="far fa-user nav-icon"></i>
                    <p>Users</p>
                  </a>
                </li>
              @endcan

              @can('roles.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.roles') }}"
                    class="nav-link @if(request()->routeIs('admin.roles')) active @endif"
                  >
                    <i class="fas fa-user-shield nav-icon"></i>
                    <p>Roles</p>
                  </a>
                </li>
              @endcan

              @can('permissions.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.permissions') }}"
                    class="nav-link @if(request()->routeIs('admin.permissions')) active @endif"
                  >
                    <i class="fas fa-th-list nav-icon"></i>
                    <p>Permission Matrix</p>
                  </a>
                </li>
              @endcan

              @can('categories.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.categories') }}"
                    class="nav-link @if(request()->routeIs('admin.categories')) active @endif"
                  >
                    <i class="far fa-folder nav-icon"></i>
                    <p>Kategori</p>
                  </a>
                </li>
              @endcan

              @can('subcategories.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.subcategories') }}"
                    class="nav-link @if(request()->routeIs('admin.subcategories')) active @endif"
                  >
                    <i class="far fa-folder nav-icon"></i>
                    <p>Subkategori</p>
                  </a>
                </li>
              @endcan

              @can('units.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.units') }}"
                    class="nav-link @if(request()->routeIs('admin.units')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Unit</p>
                  </a>
                </li>
              @endcan

              @can('suppliers.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.suppliers') }}"
                    class="nav-link @if(request()->routeIs('admin.suppliers')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pemasok</p>
                  </a>
                </li>
              @endcan

              @can('customers.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.customers') }}"
                    class="nav-link @if(request()->routeIs('admin.customers')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Pelanggan</p>
                  </a>
                </li>
              @endcan

              @can('warehouses.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.warehouses') }}"
                    class="nav-link @if(request()->routeIs('admin.warehouses')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Gudang</p>
                  </a>
                </li>
              @endcan

              @can('stores.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.stores') }}"
                    class="nav-link @if(request()->routeIs('admin.stores')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Toko</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif

        <!-- Pembelian Section -->
        @if ($currentUser && ((method_exists($currentUser, 'hasPermissionTo') && $currentUser->hasPermissionTo('purchases.view')) || $isAdmin))
          <li class="nav-item @if(request()->routeIs('admin.purchases')) menu-open @endif">
            <a href="#" class="nav-link @if(request()->routeIs('admin.purchases')) active @endif">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                Pembelian
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('purchases.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.purchases') }}"
                    class="nav-link @if(request()->routeIs('admin.purchases')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Daftar Pembelian</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif

        <!-- Penjualan Section -->
        @php
          $salesRoutes = ['admin.sales', 'admin.hold-orders'];
        @endphp

        @if ($currentUser && ((method_exists($currentUser, 'hasPermissionTo') && $currentUser->hasPermissionTo('sales.view')) || $isAdmin))
          <li
            class="nav-item @if(\App\Helpers\NavHelper::isRouteActive($salesRoutes)) menu-open @endif"
          >
            <a
              href="#"
              class="nav-link @if(\App\Helpers\NavHelper::isRouteActive($salesRoutes)) active @endif"
            >
              <i class="nav-icon fas fa-dollar-sign"></i>
              <p>
                Penjualan
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('sales.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.sales') }}"
                    class="nav-link @if(request()->routeIs('admin.sales')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Daftar Penjualan</p>
                  </a>
                </li>
              @endcan

              @can('delivery-notes.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.delivery-notes') }}"
                    class="nav-link @if(request()->routeIs('admin.delivery-notes')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Surat Jalan</p>
                  </a>
                </li>
              @endcan

              @can('sales.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.hold-orders') }}"
                    class="nav-link @if(request()->routeIs('admin.hold-orders')) active @endif"
                  >
                    <i class="far fa-hand-paper nav-icon"></i>
                    <p>Hold / Keep Orders</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif

        <!-- Stock Management Section -->
        @php
          $stockRoutes = ['admin.stock-reports', 'stock-batches.index', 'stock-card.index'];
        @endphp

        @if ($currentUser && ((method_exists($currentUser, 'hasPermissionTo') && ($currentUser->hasPermissionTo('stock-reports.view') || $currentUser->hasPermissionTo('stock-batches.view') || $currentUser->hasPermissionTo('stock-cards.view'))) || $isAdmin))
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
              @can('stock-reports.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.stock-reports') }}"
                    class="nav-link @if(request()->routeIs('admin.stock-reports')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Laporan Stok</p>
                  </a>
                </li>
              @endcan

              @can('stock-batches.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.stock-batches.index') }}"
                    class="nav-link @if(request()->routeIs('admin.stock-batches.index')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Stok Tumpukan</p>
                  </a>
                </li>
              @endcan

              @can('stock-cards.view')
                <li class="nav-item">
                  <a
                    href="{{ route('stock-card.index') }}"
                    class="nav-link @if(request()->routeIs('stock-card.*')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Kartu Stok</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif

        <!-- Reports & Analytics Section -->
        @php
          $reportRoutes = ['admin.transactions', 'admin.profit-margin'];
        @endphp

        @if ($currentUser && ((method_exists($currentUser, 'hasPermissionTo') && ($currentUser->hasPermissionTo('transactions.view') || $currentUser->hasPermissionTo('profit-margin.view'))) || $isAdmin))
          <li
            class="nav-item @if(\App\Helpers\NavHelper::isRouteActive($reportRoutes)) menu-open @endif"
          >
            <a
              href="#"
              class="nav-link @if(\App\Helpers\NavHelper::isRouteActive($reportRoutes)) active @endif"
            >
              <i class="nav-icon fas fa-chart-line"></i>
              <p>
                Laporan & Analitik
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              @can('transactions.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.transactions') }}"
                    class="nav-link @if(request()->routeIs('admin.transactions')) active @endif"
                  >
                    <i class="far fa-circle nav-icon"></i>
                    <p>Historis Transaksi</p>
                  </a>
                </li>
              @endcan

              @can('profit-margin.view')
                <li class="nav-item">
                  <a
                    href="{{ route('admin.profit-margin') }}"
                    class="nav-link @if(request()->routeIs('admin.profit-margin')) active @endif"
                  >
                    <i class="fas fa-hand-holding-usd nav-icon"></i>
                    <p>Margin Profit</p>
                  </a>
                </li>
              @endcan
            </ul>
          </li>
        @endif
      </ul>
    </nav>
  </div>
</aside>

<div>
  <!-- Page Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>
            <i class="fas fa-chart-line mr-2"></i>
            Dashboard
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- KPI Cards Row 1 -->
      <div class="row">
        <!-- Total Purchases Card -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3 wire:poll.5000ms="refreshDashboard">{{ $totalPurchases }}</h3>
              <p>Total Pembelian</p>
            </div>
            <div class="icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route("admin.purchases") }}" class="small-box-footer">
              Lihat detail
              <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>Rp {{ number_format($totalRevenue, 0, ",", ".") }}</h3>
              <p>Total Pendapatan</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="{{ route("admin.purchases") }}" class="small-box-footer">
              Lihat detail
              <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>{{ $totalProducts }}</h3>
              <p>Total Produk</p>
            </div>
            <div class="icon">
              <i class="fas fa-box"></i>
            </div>
            <a href="{{ route("admin.products") }}" class="small-box-footer">
              Lihat detail
              <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>{{ $totalCustomers }}</h3>
              <p>Total Pelanggan</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="{{ route("admin.customers") }}" class="small-box-footer">
              Lihat detail
              <i class="fas fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>
      </div>

      <!-- Second Row -->
      <div class="row">
        <!-- Recent Purchases -->
        <div class="col-lg-6">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Pembelian Terbaru
              </h3>
              <div class="card-tools">
                <span class="badge badge-primary">{{ count($recentPurchases) }}</span>
                <button type="button" class="btn btn-tool" wire:click="refreshDashboard">
                  <i class="fas fa-sync-alt"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                  <thead class="bg-light">
                    <tr>
                      <th>No Invoice</th>
                      <th>Supplier</th>
                      <th>Tanggal</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($recentPurchases as $purchase)
                      <tr wire:poll.5000ms key="purchase-{{ $purchase->id }}">
                        <td>
                          <strong>{{ $purchase->no_invoice }}</strong>
                        </td>
                        <td>{{ $purchase->supplier->nama_supplier ?? "-" }}</td>
                        <td>
                          <span class="badge badge-info">
                            {{ $purchase->tanggal_pembelian->format("d/m/Y") }}
                          </span>
                        </td>
                        <td class="font-weight-bold text-success">
                          Rp
                          {{ number_format($purchase->purchaseItems->sum("total"), 0, ",", ".") }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                          <i class="fas fa-inbox"></i>
                          Belum ada data
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <a href="{{ route("admin.purchases") }}" class="btn btn-sm btn-primary">
                <i class="fas fa-list mr-1"></i>
                Lihat Semua
              </a>
            </div>
          </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-lg-6">
          <div class="card card-warning card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Produk Stok Rendah
              </h3>
              <div class="card-tools">
                <span class="badge badge-warning">{{ count($lowStockProducts) }}</span>
                <button type="button" class="btn btn-tool" wire:click="refreshDashboard">
                  <i class="fas fa-sync-alt"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                  <thead class="bg-light">
                    <tr>
                      <th>Produk</th>
                      <th>Stok</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($lowStockProducts as $stock)
                      <tr wire:poll.5000ms key="stock-{{ $stock->product_id }}">
                        <td>
                          <small>{{ $stock->product->kode_produk }}</small>
                          <br />
                          <strong>{{ $stock->product->nama_produk }}</strong>
                        </td>
                        <td class="font-weight-bold">{{ number_format($stock->total_qty, 0) }}</td>
                        <td>
                          @if ($stock->total_qty < 5)
                            <span class="badge badge-danger">Kritis</span>
                          @elseif ($stock->total_qty < 10)
                            <span class="badge badge-warning">Rendah</span>
                          @else
                            <span class="badge badge-success">Normal</span>
                          @endif
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                          <i class="fas fa-check-circle"></i>
                          Semua produk stok aman
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <a href="{{ route("admin.stock-reports") }}" class="btn btn-sm btn-warning">
                <i class="fas fa-chart-bar mr-1"></i>
                Lihat Laporan Stok
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Third Row - Top Selling Products -->
      <div class="row">
        <div class="col-lg-12">
          <div class="card card-success card-outline">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-star mr-2"></i>
                Produk Terlaris
              </h3>
              <div class="card-tools">
                <span class="badge badge-success">{{ count($topProducts) }}</span>
                <button type="button" class="btn btn-tool" wire:click="refreshDashboard">
                  <i class="fas fa-sync-alt"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-striped table-hover table-sm">
                  <thead class="bg-light">
                    <tr>
                      <th>Ranking</th>
                      <th>Produk</th>
                      <th>Kode</th>
                      <th class="text-center">Total Pembelian</th>
                      <th class="text-right">Total Qty</th>
                      <th>Progress</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($topProducts as $index => $product)
                      <tr wire:poll.5000ms key="top-{{ $product->product_id }}">
                        <td>
                          <span
                            class="badge @if ($index === 0)
                                badge-danger
                            @elseif ($index === 1)
                                badge-warning
                            @elseif ($index === 2)
                                badge-info
                            @else
                                badge-secondary
                            @endif"
                          >
                            #{{ $index + 1 }}
                          </span>
                        </td>
                        <td><strong>{{ $product->product->nama_produk }}</strong></td>
                        <td>
                          <small class="text-muted">{{ $product->product->kode_produk }}</small>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-primary">{{ $product->purchase_count }}</span>
                        </td>
                        <td class="text-right font-weight-bold">
                          {{ number_format($product->total_qty, 0) }}
                        </td>
                        <td>
                          <div class="progress progress-sm">
                            <div
                              class="progress-bar bg-success"
                              role="progressbar"
                              style="
                                width: {{ ($product->total_qty / max($topProducts->first()?->total_qty ?? 1, 1)) * 100 }}%;
                              "
                              aria-valuenow="{{ $product->total_qty }}"
                              aria-valuemin="0"
                              aria-valuemax="{{ max($topProducts->first()?->total_qty ?? 1, 1) }}"
                            ></div>
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                          <i class="fas fa-chart-line"></i>
                          Belum ada data penjualan
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <small class="text-muted">
                <i class="fas fa-info-circle mr-1"></i>
                Data diperbarui secara real-time setiap 5 detik
              </small>
            </div>
          </div>
        </div>
      </div>

      <!-- System Stats Row -->
      <div class="row mt-3">
        <div class="col-lg-12">
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-info-circle mr-2"></i>
                Statistik Sistem
              </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 text-center">
                  <div class="description-block border-right">
                    <h5 class="description-header">{{ $totalUsers }}</h5>
                    <span class="description-text">Total Pengguna</span>
                  </div>
                </div>
                <div class="col-md-3 text-center">
                  <div class="description-block border-right">
                    <h5 class="description-header">{{ $totalPurchases }}</h5>
                    <span class="description-text">Total Transaksi</span>
                  </div>
                </div>
                <div class="col-md-3 text-center">
                  <div class="description-block border-right">
                    <h5 class="description-header">{{ $totalProducts }}</h5>
                    <span class="description-text">Produk Tersedia</span>
                  </div>
                </div>
                <div class="col-md-3 text-center">
                  <div class="description-block">
                    <h5 class="description-header">{{ $totalCustomers }}</h5>
                    <span class="description-text">Data Pelanggan</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    // Auto-refresh dashboard every 5 seconds
    document.addEventListener('livewire:navigated', () => {
      console.log('Dashboard loaded - real-time monitoring active');
    });
  </script>
</div>

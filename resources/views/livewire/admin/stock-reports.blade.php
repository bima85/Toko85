<div>
  <style>
    /* Table Styling */
    .stock-table thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 0.75rem 0.5rem;
      transition: all 0.3s ease;
    }
    .stock-table thead.header-normal th {
      background-color: #007bff;
      color: #fff;
      border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    }
    .stock-table thead.header-scrolled th {
      background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
      color: #fff;
      border-bottom: 3px solid #ffc107;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
    .stock-table thead.header-normal-green th {
      background-color: #28a745;
      color: #fff;
      border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    }
    .stock-table thead.header-scrolled-green th {
      background: linear-gradient(135deg, #1e7e34 0%, #155d27 100%);
      color: #fff;
      border-bottom: 3px solid #ffc107;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
    .stock-table tbody td {
      font-size: 0.95rem;
      padding: 0.6rem;
      vertical-align: middle;
      white-space: nowrap;
    }
    .stock-table tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.08) !important;
    }
    .stock-table .badge {
      font-size: 0.85rem;
      padding: 0.35em 0.65em;
    }
    .card-tools .form-control-sm {
      height: 36px;
      font-size: 0.95rem;
    }
    .card-tools .form-control,
    .card-tools .input-group .form-control {
      font-size: 0.95rem;
    }
    .card-tools {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex-wrap: nowrap;
    }
    .nav-pills .nav-link {
      border-radius: 0;
      padding: 0.6rem 1rem;
      font-weight: 500;
    }
    .nav-pills .nav-link.active {
      background-color: #007bff;
    }
    .info-box-number {
      font-size: 1.5rem;
      font-weight: 700;
    }
    .breadcrumb-inline {
      display: inline-flex;
      align-items: center;
      margin-left: 0;
      margin-bottom: 0;
      margin-top: 0.5rem;
      padding: 0;
      background-color: transparent;
      border-radius: 0;
      font-size: 0.95rem;
    }
    .breadcrumb-inline .breadcrumb-item {
      padding: 0;
    }
    .breadcrumb-inline .breadcrumb-item a {
      color: #007bff;
      text-decoration: none;
      transition: color 0.2s ease;
    }
    .breadcrumb-inline .breadcrumb-item a:hover {
      color: #0056b3;
      text-decoration: underline;
    }
    .breadcrumb-inline .breadcrumb-item + .breadcrumb-item::before {
      display: inline-block;
      padding: 0 0.5rem;
      color: #6c757d;
      content: '/';
    }
    .breadcrumb-inline .breadcrumb-item.active {
      color: #6c757d;
    }
    .table-scroll-wrapper {
      max-height: 500px;
      overflow-y: auto;
      overflow-x: auto;
    }

    /* ================================================= */
    /* MOBILE MODE (≤ 768px) */
    /* ================================================= */
    @media (max-width: 768px) {
      .info-box-number {
        font-size: 1.4rem;
      }
      .info-box .info-box-icon {
        width: 48px;
        height: 48px;
      }

      .card-tools {
        flex-wrap: wrap;
        gap: 0.4rem;
      }
      .card-tools .input-group {
        width: 100% !important;
        margin-top: 0.25rem;
      }
      .card-tools .form-control[wire\:model],
      .card-tools select {
        width: 100% !important;
      }

      .nav-pills .nav-link {
        padding: 0.45rem 0.6rem;
        font-size: 0.95rem;
      }

      /* Hanya sembunyikan kolom-kolom ini di tabel Stok Produk (kartu utama) pada mobile */
      .card.card-outline.card-primary .stock-table th:nth-child(4),
      .card.card-outline.card-primary .stock-table td:nth-child(4),
      .card.card-outline.card-primary .stock-table th:nth-child(5),
      .card.card-outline.card-primary .stock-table td:nth-child(5),
      .card.card-outline.card-primary .stock-table th:nth-child(6),
      .card.card-outline.card-primary .stock-table td:nth-child(6),
      .card.card-outline.card-primary .stock-table th:nth-child(7),
      .card.card-outline.card-primary .stock-table td:nth-child(7),
      .card.card-outline.card-primary .stock-table th:nth-child(8),
      .card.card-outline.card-primary .stock-table td:nth-child(8),
      .card.card-outline.card-primary .stock-table th:nth-child(9),
      .card.card-outline.card-primary .stock-table td:nth-child(9),
      .card.card-outline.card-primary .stock-table th:nth-child(13),
      .card.card-outline.card-primary .stock-table td:nth-child(13) {
        display: none !important;
      }

      .stock-table.no-lokasi-on-mobile th:nth-child(13),
      .stock-table.no-lokasi-on-mobile td:nth-child(13) {
        display: none !important;
      }

      .stock-table td.lokasi-empty {
        display: none !important;
      }

      .stock-table tbody td:nth-child(3) {
        white-space: normal;
        min-width: 160px;
        font-size: 1rem;
        position: relative;
      }

      .stock-table tbody td:nth-child(3)::after {
        content: 'Kategori: ' attr(data-kategori) ' | Sub: ' attr(data-subkategori);
        display: block;
        margin-top: 0.35rem;
        font-size: 0.85rem;
        background: #f1f8e9;
        color: #2e7d32;
        padding: 0.3rem 0.45rem;
        border-radius: 4px;
      }

      .stock-table tbody td:nth-child(12),
      .stock-table tbody td:nth-child(13) {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.9rem;
        background: #fff8e1;
        padding: 0.4rem 0.5rem;
        border-top: 1px dashed #ffe082;
      }

      .stock-table tbody td:nth-child(12)::before {
        content: 'Keterangan: ';
        font-weight: 600;
        color: #856404;
        margin-right: 0.25rem;
      }

      .stock-table tbody td:nth-child(13)::before {
        content: 'Lokasi: ';
        font-weight: 600;
        color: #0d47a1;
        margin-right: 0.25rem;
      }

      .stock-table thead th,
      .stock-table tbody td {
        padding: 0.45rem 0.4rem;
        font-size: 0.95rem;
      }

      .card-footer .pagination {
        margin: 0.25rem 0;
      }

      .card-warning .card-tools {
        flex-wrap: wrap;
        gap: 0.4rem;
      }

      .card-warning .card-tools .input-group {
        width: 100% !important;
        margin-top: 0.25rem;
      }

      .card-warning .stock-table th:nth-child(4),
      .card-warning .stock-table td:nth-child(4),
      .card-warning .stock-table th:nth-child(5),
      .card-warning .stock-table td:nth-child(5) {
        display: none !important;
      }

      .card-warning .stock-table th:nth-child(3),
      .card-warning .stock-table td:nth-child(3),
      .card-warning .stock-table th:nth-child(7),
      .card-warning .stock-table td:nth-child(7) {
        white-space: normal;
        min-width: 140px;
      }

      .card-warning .stock-table th:nth-child(1),
      .card-warning .stock-table td:nth-child(1) {
        width: 40px;
      }

      .card-warning .stock-table th:nth-child(11),
      .card-warning .stock-table td:nth-child(11) {
        width: 90px;
      }
    }

    @media (max-width: 420px) {
      .stock-table thead th,
      .stock-table tbody td {
        font-size: 0.9rem;
      }
    }
  </style>

  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('message') }}
    </div>
  @endif

  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <ol class="breadcrumb breadcrumb-inline">
            <li class="breadcrumb-item">
              <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Laporan Stok</li>
          </ol>
        </div>
        <div class="col-sm-6 text-right">
          <a href="{{ route('stock-batches.index') }}" class="btn btn-sm btn-primary mr-2">
            <i class="fas fa-layer-group"></i>
            Kelola Tumpukan Stok
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Summary Cards -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="info-box bg-success">
            <span class="info-box-icon bg-success-gradient"><i class="fas fa-store"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Stok Toko</span>
              <span class="info-box-number">
                {{ number_format($this->getTotalStokToko(), 0) }}
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box bg-warning">
            <span class="info-box-icon bg-warning-gradient"><i class="fas fa-warehouse"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Stok Gudang</span>
              <span class="info-box-number">
                {{ number_format($this->getTotalStokGudang(), 0) }}
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box bg-info">
            <span class="info-box-icon bg-info-gradient"><i class="fas fa-boxes"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Stok Keseluruhan</span>
              <span class="info-box-number">
                {{ number_format($this->getTotalStokToko() + $this->getTotalStokGudang(), 0) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="card card-outline card-primary elevation-2">
        <div class="card-header p-0">
          <div class="d-flex justify-content-between align-items-center p-2">
            <h3 class="card-title mb-0">
              <i class="fas fa-chart-bar mr-2"></i>
              Laporan Stok Produk
            </h3>
            <div class="card-tools d-flex align-items-center">
              <select
                wire:model.live="perPage"
                class="form-control form-control-sm"
                style="width: 120px; margin-right: 10px"
              >
                <option value="10">10 baris</option>
                <option value="15">15 baris</option>
                <option value="25">25 baris</option>
                <option value="50">50 baris</option>
                <option value="100">100 baris</option>
              </select>
              <button
                type="button"
                wire:click="exportExcel"
                class="btn btn-xs btn-success mr-2"
                title="Export ke Excel"
              >
                <i class="fas fa-file-excel mr-1"></i>
                <strong>Export Excel</strong>
              </button>
              <button
                type="button"
                wire:click="createAdjustment"
                class="btn btn-xs btn-success mr-2"
              >
                <i class="fas fa-plus mr-1"></i>
                <strong>Penyesuaian Stok</strong>
              </button>
              <div class="input-group input-group-sm" style="width: 250px">
                <input
                  wire:model.live.debounce.300ms="search"
                  type="text"
                  class="form-control"
                  placeholder="Cari produk..."
                />
                <div class="input-group-append">
                  <button type="button" class="btn btn-default">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <ul class="nav nav-pills nav-fill border-top mb-2 mt-2">
            <li class="nav-item">
              <a
                class="nav-link {{ $activeTab === 'store' ? 'active' : '' }}"
                href="#"
                wire:click.prevent="setActiveTab('store')"
              >
                <i class="fas fa-store mr-1"></i>
                Stok Toko
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ $activeTab === 'warehouse' ? 'active' : '' }}"
                href="#"
                wire:click.prevent="setActiveTab('warehouse')"
              >
                <i class="fas fa-warehouse mr-1"></i>
                Stok Gudang
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body p-0">
          <div class="tab-content">
            <!-- Stok Toko -->
            <div class="{{ $activeTab === 'store' ? '' : 'd-none' }}">
              @if ($stocks->count() > 0)
                @php
                  $hasLokasi = $stocks->contains(fn ($s) => ! empty($s->store->nama_toko ?? null));
                @endphp

                <div
                  class="table-responsive table-scroll-wrapper"
                  x-data="{ isScrolled: false }"
                  x-on:scroll="isScrolled = $el.scrollTop > 10"
                >
                  <table
                    class="table table-hover table-striped table-sm table-bordered mb-0 stock-table {{ ! $hasLokasi ? 'no-lokasi-on-mobile' : '' }}"
                  >
                    <thead :class="isScrolled ? 'header-scrolled' : 'header-normal'">
                      <tr>
                        <th class="text-center" style="width: 50px">No</th>
                        <th style="min-width: 100px">Kode</th>
                        <th style="min-width: 180px">Nama Produk</th>
                        <th style="min-width: 100px">Kategori</th>
                        <th style="min-width: 100px">Subkategori</th>
                        <th class="text-center" style="width: 70px">Satuan</th>
                        <th class="text-center" style="width: 80px">Awal</th>
                        <th class="text-center" style="width: 80px">Masuk</th>
                        <th class="text-center" style="width: 80px">Keluar</th>
                        <th class="text-center" style="width: 80px">Akhir</th>
                        <th class="text-center" style="width: 90px">Total</th>
                        <th class="text-center" style="width: 50px">Keterangan</th>
                        <th style="min-width: 100px">Lokasi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($stocks as $index => $stock)
                        <tr>
                          <td class="text-center">
                            <span class="badge badge-light text-dark border">
                              {{ $stocks->firstItem() + $index }}
                            </span>
                          </td>
                          <td>
                            <code class="text-primary">{{ $stock->product->kode_produk }}</code>
                          </td>
                          <td
                            data-kategori="{{ $stock->product->category->nama_kategori ?? '-' }}"
                            data-subkategori="{{ $stock->product->subcategory->kode_subkategori ?? '-' }}"
                          >
                            <strong>{{ $stock->product->nama_produk }}</strong>
                          </td>
                          <td>
                            <span class="badge badge-secondary">
                              {{ $stock->product->category->nama_kategori ?? '-' }}
                            </span>
                          </td>
                          <td>
                            <span class="badge badge-light text-dark">
                              {{ $stock->product->subcategory->nama_subkategori ?? '-' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <small class="text-muted">{{ $stock->unit ?? '-' }}</small>
                          </td>
                          <td class="text-center">
                            <span class="text-muted">
                              {{ number_format($stock->stok_awal, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="text-success font-weight-bold">
                              <i class="fas fa-arrow-up fa-xs"></i>
                              {{ number_format($stock->stok_masuk, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="text-danger font-weight-bold">
                              <i class="fas fa-arrow-down fa-xs"></i>
                              {{ number_format($stock->stok_keluar, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-info">
                              {{ number_format($stock->stok_akhir, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            @php
                              $batchTotal = $batchTotals[$stock->product_id] ?? null;
                              $displayTotal = $batchTotal ? number_format($batchTotal->total_qty, 0) : number_format($stock->total_stok ?? 0, 0);
                            @endphp

                            <span class="badge badge-success px-3">{{ $displayTotal }}</span>
                          </td>
                          <td>
                            @php
                              $holdQty = $holdTotals[$stock->product_id] ?? 0;
                            @endphp

                            @if ($holdQty > 0)
                              <div>
                                <small class="text-warning">
                                  <i class="fas fa-lock mr-1"></i>
                                  Hold: {{ number_format($holdQty, 0) }}
                                </small>
                                <a
                                  href="{{ route('admin.hold-orders', ['search' => $stock->product->kode_produk]) }}"
                                  class="btn btn-sm btn-outline-warning ml-2"
                                >
                                  Lihat Hold
                                </a>
                              </div>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                          @php
                            $storeLokasiRaw = $stock->store->nama_toko ?? null;
                            $storeLokasi = trim((string) ($storeLokasiRaw ?? ''));
                            $storeLokasiEmpty = $storeLokasi === '' || $storeLokasi === '-';
                          @endphp

                          <td class="{{ $storeLokasiEmpty ? 'lokasi-empty' : '' }}">
                            @if (! $storeLokasiEmpty)
                              <span class="badge badge-primary">
                                <i class="fas fa-store fa-xs mr-1"></i>
                                {{ $storeLokasi }}
                              </span>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="13" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <strong>Tidak ada data stok toko</strong>
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                @if ($stocks->hasPages())
                  <div class="card-footer bg-white border-top p-2">
                    {{ $stocks->links('pagination::livewire-bootstrap-4') }}
                  </div>
                @endif
              @else
                <div class="text-center text-muted py-5">
                  <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                  <strong>Tidak ada data stok toko</strong>
                </div>
              @endif
            </div>

            <!-- Stok Gudang -->
            <div class="{{ $activeTab === 'warehouse' ? '' : 'd-none' }}">
              @if ($stocks->count() > 0)
                @php
                  $hasLokasi = $stocks->contains(fn ($s) => ! empty($s->warehouse->nama_gudang ?? null));
                @endphp

                <div
                  class="table-responsive table-scroll-wrapper"
                  x-data="{ isScrolled: false }"
                  x-on:scroll="isScrolled = $el.scrollTop > 10"
                >
                  <table
                    class="table table-hover table-striped table-sm table-bordered mb-0 stock-table warehouse-table {{ ! $hasLokasi ? 'no-lokasi-on-mobile' : '' }}"
                  >
                    <thead :class="isScrolled ? 'header-scrolled-green' : 'header-normal-green'">
                      <tr>
                        <th class="text-center" style="width: 50px">No</th>
                        <th style="min-width: 100px">Kode</th>
                        <th style="min-width: 180px">Nama Produk</th>
                        <th style="min-width: 100px">Kategori</th>
                        <th style="min-width: 100px">Subkategori</th>
                        <th class="text-center" style="width: 70px">Satuan</th>
                        <th class="text-center" style="width: 80px">Awal</th>
                        <th class="text-center" style="width: 80px">Masuk</th>
                        <th class="text-center" style="width: 80px">Keluar</th>
                        <th class="text-center" style="width: 80px">Akhir</th>
                        <th class="text-center" style="width: 90px">Total</th>
                        <th style="min-width: 140px">Keterangan</th>
                        <th style="min-width: 100px">Lokasi</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($stocks as $index => $stock)
                        <tr>
                          <td class="text-center">
                            <span class="badge badge-light text-dark border">
                              {{ $stocks->firstItem() + $index }}
                            </span>
                          </td>
                          <td>
                            <code class="text-success">{{ $stock->product->kode_produk }}</code>
                          </td>
                          <td
                            data-kategori="{{ $stock->product->category->nama_kategori ?? '-' }}"
                            data-subkategori="{{ $stock->product->subcategory->kode_subkategori ?? '-' }}"
                          >
                            <strong>{{ $stock->product->nama_produk }}</strong>
                          </td>
                          <td>
                            <span class="badge badge-secondary">
                              {{ $stock->product->category->nama_kategori ?? '-' }}
                            </span>
                          </td>
                          <td>
                            <span class="badge badge-light text-dark">
                              {{ $stock->product->subcategory->nama_subkategori ?? '-' }}
                            </span>
                          </td>
                          <td class="text-center">
                            <small class="text-muted">{{ $stock->unit ?? '-' }}</small>
                          </td>
                          <td class="text-center">
                            <span class="text-muted">
                              {{ number_format($stock->stok_awal, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="text-success font-weight-bold">
                              <i class="fas fa-arrow-up fa-xs"></i>
                              {{ number_format($stock->stok_masuk, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="text-danger font-weight-bold">
                              <i class="fas fa-arrow-down fa-xs"></i>
                              {{ number_format($stock->stok_keluar, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-info">
                              {{ number_format($stock->stok_akhir, 0) }}
                            </span>
                          </td>
                          <td class="text-center">
                            @php
                              $batchTotal = $batchTotals[$stock->product_id] ?? null;
                              $displayTotal = $batchTotal ? number_format($batchTotal->total_qty, 0) : number_format($stock->total_stok ?? 0, 0);
                            @endphp

                            <span class="badge badge-warning text-dark px-3">
                              {{ $displayTotal }}
                            </span>
                          </td>
                          <td>
                            @php
                              $holdQty = $holdTotals[$stock->product_id] ?? 0;
                            @endphp

                            @if ($holdQty > 0)
                              <div>
                                <small class="text-warning">
                                  <i class="fas fa-lock mr-1"></i>
                                  Hold: {{ number_format($holdQty, 0) }}
                                </small>
                                <a
                                  href="{{ route('admin.hold-orders', ['search' => $stock->product->kode_produk]) }}"
                                  class="btn btn-sm btn-outline-warning ml-2"
                                >
                                  Lihat Hold
                                </a>
                              </div>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                          @php
                            $warehouseLokasiRaw = $stock->warehouse->nama_gudang ?? null;
                            $warehouseLokasi = trim((string) ($warehouseLokasiRaw ?? ''));
                            $warehouseLokasiEmpty = $warehouseLokasi === '' || $warehouseLokasi === '-';
                          @endphp

                          <td class="{{ $warehouseLokasiEmpty ? 'lokasi-empty' : '' }}">
                            @if (! $warehouseLokasiEmpty)
                              <span class="badge badge-success">
                                <i class="fas fa-warehouse fa-xs mr-1"></i>
                                {{ $warehouseLokasi }}
                              </span>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                        </tr>
                      @empty
                        <tr>
                          <td colspan="13" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                            <strong>Tidak ada data stok gudang</strong>
                          </td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>

                @if ($stocks->hasPages())
                  <div class="card-footer bg-white border-top p-2">
                    {{ $stocks->links('pagination::livewire-bootstrap-4') }}
                  </div>
                @endif
              @else
                <div class="text-center text-muted py-5">
                  <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                  <strong>Tidak ada data stok gudang</strong>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Riwayat Penyesuaian Stok -->
      <div class="card card-warning card-outline elevation-2 mt-4">
        <div class="card-header bg-warning">
          <h3 class="card-title">
            <i class="fas fa-history mr-2"></i>
            Riwayat Penyesuaian Stok
          </h3>
          <div class="card-tools d-flex align-items-center">
            <div class="input-group input-group-sm" style="width: 250px; margin-right: 10px">
              <input
                type="text"
                wire:model.live.debounce.300ms="searchAdjustments"
                class="form-control"
                placeholder="Cari produk, kode, alasan..."
              />
              <div class="input-group-append">
                @if ($searchAdjustments)
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    wire:click="clearSearchAdjustments"
                    title="Reset"
                  >
                    <i class="fas fa-times"></i>
                  </button>
                @endif

                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
              </div>
            </div>
            <select
              wire:model.live="perPageAdjustments"
              class="form-control form-control-sm"
              style="width: 120px; margin-right: 10px"
            >
              <option value="10">10 baris</option>
              <option value="15">15 baris</option>
              <option value="25">25 baris</option>
              <option value="50">50 baris</option>
              <option value="100">100 baris</option>
            </select>
            @if (count($this->selectedAdjustments) > 0)
              <button
                class="btn btn-sm btn-danger mr-2"
                onclick="if(confirm('Hapus {{ count($this->selectedAdjustments) }} penyesuaian secara permanen?')) { $wire.deleteSelectedAdjustments() }"
                title="Hapus pilihan"
              >
                <i class="fas fa-trash"></i>
                Hapus {{ count($this->selectedAdjustments) }}
              </button>
              <button class="btn btn-sm btn-secondary" wire:click="clearAdjustmentSelection">
                <i class="fas fa-times"></i>
                Batal
              </button>
            @endif

            <span class="ml-2 text-danger font-weight-bold">{{ $adjustments->count() }}</span>
          </div>
        </div>

        <div class="card-body p-0">
          @if ($adjustments->count() > 0)
            <div class="table-responsive table-scroll-wrapper">
              <table
                class="table table-hover table-striped table-sm table-bordered mb-0 stock-table"
              >
                <thead class="bg-warning text-dark">
                  <tr>
                    <th class="text-center" style="width: 40px">
                      <input
                        type="checkbox"
                        wire:click="$toggle('selectAllAdjustments')"
                        wire:model="selectAllAdjustments"
                      />
                    </th>
                    <th class="text-center" style="width: 50px">No</th>
                    <th style="min-width: 150px">Produk</th>
                    <th style="min-width: 100px">Lokasi</th>
                    <th class="text-center" style="width: 80px">Tipe</th>
                    <th class="text-center" style="width: 80px">Qty</th>
                    <th style="min-width: 150px">Alasan</th>
                    <th class="text-center" style="width: 100px">Tanggal</th>
                    <th class="text-center" style="width: 70px">Jam</th>
                    <th class="text-center" style="width: 80px">User</th>
                    <th class="text-center" style="width: 90px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($adjustments as $index => $adjustment)
                    <tr
                      @if(in_array($adjustment->id, $this->selectedAdjustments)) class="table-warning" @endif
                    >
                      <td class="text-center">
                        <input
                          type="checkbox"
                          wire:click="toggleSelectAdjustment({{ $adjustment->id }})"
                          @if(in_array($adjustment->id, $this->selectedAdjustments)) checked @endif
                        />
                      </td>
                      <td class="text-center">{{ $adjustments->firstItem() + $index }}</td>
                      <td>
                        <code class="text-warning">{{ $adjustment->product->kode_produk }}</code>
                        <br />
                        <small class="text-muted">{{ $adjustment->product->nama_produk }}</small>
                      </td>
                      <td><small class="text-muted">{{ $adjustment->location }}</small></td>
                      <td class="text-center">
                        @if ($adjustment->adjustment_type === 'add')
                          <span class="badge badge-success">
                            <i class="fas fa-plus-circle"></i>
                            Tambah
                          </span>
                        @else
                          <span class="badge badge-danger">
                            <i class="fas fa-minus-circle"></i>
                            Kurang
                          </span>
                        @endif
                      </td>
                      <td class="text-center">
                        <strong class="text-primary">
                          {{ number_format($adjustment->quantity, 0) }}
                        </strong>
                      </td>
                      <td>
                        <small class="text-muted">
                          {{ Str::limit($adjustment->reason ?: '-', 30) }}
                        </small>
                      </td>
                      <td class="text-center">
                        <small>{{ $adjustment->adjustment_date->format('d/m/Y') }}</small>
                      </td>
                      <td class="text-center">
                        <small class="text-muted">
                          {{ $adjustment->created_at->format('H:i') }}
                        </small>
                      </td>
                      <td class="text-center">
                        <small class="text-muted">{{ $adjustment->user->name ?? '-' }}</small>
                      </td>
                      <td class="text-center">
                        <div class="btn-group btn-group-sm">
                          <button
                            wire:click="editAdjustment({{ $adjustment->id }})"
                            class="btn btn-outline-info btn-sm"
                            title="Edit"
                          >
                            <i class="fas fa-edit"></i>
                          </button>
                          <button
                            wire:click="deleteAdjustment({{ $adjustment->id }})"
                            wire:confirm="Yakin ingin menghapus penyesuaian ini?"
                            class="btn btn-outline-danger btn-sm"
                            title="Hapus"
                          >
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="11" class="text-center text-muted py-5">
                        <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
                        <strong>Belum ada riwayat penyesuaian stok</strong>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            @if ($adjustments->hasPages())
              <div class="card-footer bg-white border-top p-0">
                {{ $adjustments->links('pagination::livewire-bootstrap-4') }}
              </div>
            @endif
          @else
            <div class="text-center text-muted py-5">
              <i class="fas fa-history fa-3x mb-3 d-block opacity-50"></i>
              <strong>Belum ada riwayat penyesuaian stok</strong>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Penyesuaian Stok -->
  <div wire:ignore.self class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">
            <i class="fas fa-adjust mr-2"></i>
            {{ $editingAdjustmentId ? 'Edit Penyesuaian Stok' : 'Buat Penyesuaian Stok Baru' }}
          </h4>
          <button type="button" class="close" wire:click="closeAdjustmentModal">&times;</button>
        </div>
        <form wire:submit.prevent="saveAdjustment">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Produk
                    <span class="text-danger">*</span>
                  </label>
                  <select wire:model="adjustment_product_id" class="form-control">
                    <option value="">Pilih Produk</option>
                    @foreach (\App\Models\Product::orderBy('nama_produk')->get() as $product)
                      <option value="{{ $product->id }}">
                        {{ $product->kode_produk }} - {{ $product->nama_produk }}
                      </option>
                    @endforeach
                  </select>
                  @error('adjustment_product_id')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Satuan
                    <span class="text-danger">*</span>
                  </label>
                  <select wire:model="adjustment_product_unit" class="form-control">
                    <option value="">Pilih Satuan</option>
                    @foreach (\App\Models\Unit::orderBy('nama_unit')->get() as $unit)
                      <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                    @endforeach
                  </select>
                  @error('adjustment_product_unit')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row" x-data="stokCalculator()">
              <div class="col-md-4">
                <div class="form-group">
                  <label>
                    Stok Awal
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    wire:model="adjustment_stok_awal"
                    @input="calculateTotal()"
                    type="number"
                    step="0.01"
                    min="0"
                    class="form-control"
                    x-ref="stokAwal"
                  />
                  @error('adjustment_stok_awal')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>
                    Stok Masuk
                    <span class="text-danger">*</span>
                  </label>
                  <input
                    wire:model="adjustment_stok_masuk"
                    @input="calculateTotal()"
                    type="number"
                    step="0.01"
                    min="0"
                    class="form-control"
                    x-ref="stokMasuk"
                  />
                  @error('adjustment_stok_masuk')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Total Stok</label>
                  <div class="input-group">
                    <input
                      type="number"
                      class="form-control bg-light font-weight-bold"
                      x-model.number="totalStok"
                      disabled
                    />
                    <div class="input-group-append">
                      <span class="input-group-text"><i class="fas fa-calculator"></i></span>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-1">
                    <strong x-text="stokAwal.toFixed(2)">0.00</strong>
                    +
                    <strong x-text="stokMasuk.toFixed(2)">0.00</strong>
                    =
                    <strong style="color: #28a745" x-text="totalStok.toFixed(2)">0.00</strong>
                  </small>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Tanggal Penyesuaian
                    <span class="text-danger">*</span>
                  </label>
                  <input wire:model="adjustment_date" type="date" class="form-control" />
                  @error('adjustment_date')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Tipe Penyesuaian
                    <span class="text-danger">*</span>
                  </label>
                  <select wire:model="adjustment_type" class="form-control">
                    <option value="add">Tambah Stok</option>
                    <option value="remove">Kurangi Stok</option>
                  </select>
                  @error('adjustment_type')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Lokasi
                    <span class="text-danger">*</span>
                  </label>
                  <div class="form-check">
                    <input
                      wire:model="adjustment_location"
                      class="form-check-input"
                      type="radio"
                      name="location_type"
                      id="store_location"
                      value="store"
                    />
                    <label class="form-check-label" for="store_location">Toko</label>
                  </div>
                  <select
                    wire:model="adjustment_store_id"
                    class="form-control form-control-sm mt-1"
                    style="display: {{ $adjustment_location === 'store' ? 'block' : 'none' }}"
                  >
                    <option value="">Pilih Toko</option>
                    @foreach (\App\Models\Store::orderBy('nama_toko')->get() as $store)
                      <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                    @endforeach
                  </select>

                  <div class="form-check mt-2">
                    <input
                      wire:model="adjustment_location"
                      class="form-check-input"
                      type="radio"
                      name="location_type"
                      id="warehouse_location"
                      value="warehouse"
                    />
                    <label class="form-check-label" for="warehouse_location">Gudang</label>
                  </div>
                  <select
                    wire:model="adjustment_warehouse_id"
                    class="form-control form-control-sm mt-1"
                    style="display: {{ $adjustment_location === 'warehouse' ? 'block' : 'none' }}"
                  >
                    <option value="">Pilih Gudang</option>
                    @foreach (\App\Models\Warehouse::orderBy('nama_gudang')->get() as $warehouse)
                      <option value="{{ $warehouse->id }}">{{ $warehouse->nama_gudang }}</option>
                    @endforeach
                  </select>
                  @error('adjustment_location')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Alasan Penyesuaian</label>
                  <textarea
                    wire:model="adjustment_reason"
                    class="form-control"
                    rows="3"
                    placeholder="Jelaskan alasan penyesuaian stok..."
                  ></textarea>
                  @error('adjustment_reason')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeAdjustmentModal">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i>
              {{ $editingAdjustmentId ? 'Update' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function stokCalculator() {
      return {
        stokAwal: 0,
        stokMasuk: 0,
        totalStok: 0,
        calculateTotal() {
          const awal = parseFloat(this.$refs.stokAwal?.value || 0) || 0;
          const masuk = parseFloat(this.$refs.stokMasuk?.value || 0) || 0;
          this.stokAwal = awal;
          this.stokMasuk = masuk;
          this.totalStok = awal + masuk;

          if (window.$wire) {
            $wire.set('adjustment_total_stok', this.totalStok);
          }
        },
      };
    }

    window.addEventListener('show-adjustment-modal', () => {
      $('#adjustmentModal').modal('show');
    });

    window.addEventListener('hide-adjustment-modal', () => {
      $('#adjustmentModal').modal('hide');
    });

    document.addEventListener('livewire:load', function () {
      document.getElementById('store_location')?.addEventListener('change', function () {
        if (this.checked) $wire.set('adjustment_warehouse_id', null);
      });

      document.getElementById('warehouse_location')?.addEventListener('change', function () {
        if (this.checked) $wire.set('adjustment_store_id', null);
      });
    });
  </script>
</div>

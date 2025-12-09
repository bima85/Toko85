<div>
  @php
    $transactionColors = [
      'in' => 'success',
      'out' => 'danger',
      'adjustment' => 'warning',
      'return' => 'info',
    ];
  @endphp

  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Manajemen Kartu Stok</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Kartu Stok</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Quick Links -->
      <div class="row mb-3">
        <div class="col-lg-3 col-md-6">
          <div class="info-box bg-light-primary">
            <span class="info-box-icon bg-primary"><i class="fas fa-cube"></i></span>
            <div class="info-box-content">
              <a
                href="{{ route('stock-batches.index') }}"
                class="info-box-text text-decoration-none"
              >
                Stok Tumpukan
              </a>
              <span class="info-box-number text-sm">Lihat Batch Stok</span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="info-box bg-light-info">
            <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
            <div class="info-box-content">
              <a
                href="{{ route('admin.stock-reports') }}"
                class="info-box-text text-decoration-none"
              >
                Laporan Stok
              </a>
              <span class="info-box-number text-sm">Lihat Laporan</span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="info-box bg-light-success">
            <span class="info-box-icon bg-success"><i class="fas fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <a href="{{ route('admin.purchases') }}" class="info-box-text text-decoration-none">
                Pembelian
              </a>
              <span class="info-box-number text-sm">Transaksi Masuk</span>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-md-6">
          <div class="info-box bg-light-danger">
            <span class="info-box-icon bg-danger"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
              <a href="{{ route('admin.sales') }}" class="info-box-text text-decoration-none">
                Penjualan
              </a>
              <span class="info-box-number text-sm">Transaksi Keluar</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="row mb-4">
        <div class="col-lg-4">
          <div class="card card-success card-outline">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-3 text-center">
                  <i class="fas fa-arrow-down fa-3x text-success"></i>
                </div>
                <div class="col-9">
                  <div class="text-right">
                    <h4 class="m-0 font-weight-bold">Total Stok Masuk</h4>
                    <p class="text-muted m-0">
                      {{ number_format($totalIn, 2, ',', '.') }} {{ $commonUnit }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card card-danger card-outline">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-3 text-center">
                  <i class="fas fa-arrow-up fa-3x text-danger"></i>
                </div>
                <div class="col-9">
                  <div class="text-right">
                    <h4 class="m-0 font-weight-bold">Total Stok Keluar</h4>
                    <p class="text-muted m-0">
                      {{ number_format($totalOut, 2, ',', '.') }} {{ $commonUnit }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card {{ $net >= 0 ? 'card-primary' : 'card-warning' }} card-outline">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-3 text-center">
                  <i
                    class="fas fa-exchange-alt fa-3x {{ $net >= 0 ? 'text-primary' : 'text-warning' }}"
                  ></i>
                </div>
                <div class="col-9">
                  <div class="text-right">
                    <h4 class="m-0 font-weight-bold">Saldo Bersih</h4>
                    <p class="text-muted m-0">
                      {{ number_format($net, 2, ',', '.') }} {{ $commonUnit }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Actions -->
      <div class="card card-primary card-outline mb-4">
        <div class="card-header">
          <h3 class="card-title">Filter & Pencarian</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Pencarian</label>
                <input
                  type="text"
                  class="form-control form-control-sm"
                  placeholder="Cari produk atau catatan..."
                  wire:model.debounce.500ms="search"
                />
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Tipe Transaksi</label>
                <select class="form-control form-control-sm" wire:model="filter_type">
                  <option value="">Semua Tipe</option>
                  <option value="in">Masuk</option>
                  <option value="out">Keluar</option>
                  <option value="adjustment">Penyesuaian</option>
                  <option value="return">Retur</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label>Data per Halaman</label>
                <select class="form-control form-control-sm" wire:model="per_page">
                  <option value="10">10</option>
                  <option value="15">15</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <div class="btn-group btn-block" role="group">
                  <a href="{{ route('stock-card.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i>
                    Tambah
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Daftar Kartu Stok</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <div class="card-body p-0">
          @if ($stockCards->count() > 0)
            <div class="table-responsive">
              <table class="table table-striped table-hover m-0">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th>Produk</th>
                    <th style="width: 12%">Tipe</th>
                    <th style="width: 10%" class="text-right">Kuantitas</th>
                    <th>Batch</th>
                    <th style="width: 14%">Tanggal</th>
                    <th style="width: 10%">Jam</th>
                    <th>Catatan</th>
                    <th style="width: 12%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($stockCards as $index => $card)
                    <tr>
                      <td>{{ $stockCards->firstItem() + $index }}</td>
                      <td>
                        <strong>{{ $card->product->nama_produk }}</strong>
                        <br />
                        <small class="text-muted">{{ $card->product->kode_produk }}</small>
                      </td>
                      <td>
                        <span
                          class="badge badge-{{ $transactionColors[$card->type] ?? 'secondary' }}"
                        >
                          {{ $transactionTypes[$card->type] ?? $card->type }}
                        </span>
                      </td>
                      <td class="text-right">
                        <strong>{{ number_format($card->qty, 2, ',', '.') }}</strong>
                      </td>
                      <td>
                        @if ($card->batch)
                          <span class="badge badge-info">
                            {{ $card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id }}
                          </span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        <small>{{ $card->created_at->format('d/m/Y') }}</small>
                      </td>
                      <td>
                        <small>{{ $card->created_at->format('H:i') }}</small>
                      </td>
                      <td>
                        <small class="text-muted" title="{{ $card->note }}">
                          {{ Str::limit($card->note ?? '', 25) }}
                        </small>
                      </td>
                      <td>
                        <a
                          href="{{ route('stock-card.show', $card->id) }}"
                          class="btn btn-xs btn-info"
                          title="Lihat"
                        >
                          <i class="fas fa-eye"></i>
                        </a>
                        <a
                          href="{{ route('stock-card.edit', $card->id) }}"
                          class="btn btn-xs btn-warning"
                          title="Edit"
                        >
                          <i class="fas fa-edit"></i>
                        </a>
                        <button
                          type="button"
                          class="btn btn-xs btn-danger"
                          wire:click="deleteStockCard({{ $card->id }})"
                          wire:confirm="Yakin ingin menghapus?"
                          title="Hapus"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
              {{ $stockCards->links() }}
            </div>
          @else
            <div class="alert alert-info m-3">
              <i class="fas fa-info-circle"></i>
              Tidak ada data kartu stok.
              <a href="{{ route('stock-card.create') }}" class="alert-link">Tambah sekarang</a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>
</div>

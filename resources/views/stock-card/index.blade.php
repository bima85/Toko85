@php
  $transactionColors = [
    'in' => 'success',
    'out' => 'danger',
    'adjustment' => 'warning',
    'return' => 'info',
  ];
@endphp

<div class="wrapper">
  <!-- Header -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="text-muted">Kartu Stok</span>
      </li>
    </ul>
  </nav>

  <!-- Content -->
  <div class="content-wrapper">
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
        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>{{ $stockCards->total() }}</h3>
                <p>Total Transaksi</p>
              </div>
              <div class="icon">
                <i class="fas fa-receipt"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>{{ number_format($transactionTypes['in'] ?? 0, 0, ',', '.') }}</h3>
                <p>Stok Masuk</p>
              </div>
              <div class="icon">
                <i class="fas fa-arrow-down"></i>
              </div>
              <a href="?filter_type=in" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>{{ number_format($transactionTypes['out'] ?? 0, 0, ',', '.') }}</h3>
                <p>Stok Keluar</p>
              </div>
              <div class="icon">
                <i class="fas fa-arrow-up"></i>
              </div>
              <a href="?filter_type=out" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>{{ number_format($transactionTypes['adjustment'] ?? 0, 0, ',', '.') }}</h3>
                <p>Penyesuaian</p>
              </div>
              <div class="icon">
                <i class="fas fa-tools"></i>
              </div>
              <a href="?filter_type=adjustment" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Filters and Actions -->
        <div class="card card-primary card-outline">
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
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            @if ($stockCards->count() > 0)
              <table class="table table-striped table-hover">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th>Produk</th>
                    <th style="width: 15%">Tipe</th>
                    <th style="width: 10%" class="text-right">Kuantitas</th>
                    <th>Batch</th>
                    <th>Dari/Ke</th>
                    <th>Catatan</th>
                    <th style="width: 15%">Aksi</th>
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
                          <span class="badge badge-info">{{ $card->batch->batch_number }}</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        <small>
                          @if ($card->from_location)
                            <strong>Dari:</strong>
                            {{ $card->from_location }}
                            <br />
                          @endif

                          @if ($card->to_location)
                            <strong>Ke:</strong>
                            {{ $card->to_location }}
                            <br />
                          @endif

                          @if (! $card->from_location && ! $card->to_location)
                            <span class="text-muted">-</span>
                          @endif
                        </small>
                      </td>
                      <td>
                        <small class="text-muted" title="{{ $card->note }}">
                          {{ Str::limit($card->note, 30) }}
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
</div>

@push('scripts')
  <script>
    // Flash message handling
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
  </script>
@endpush

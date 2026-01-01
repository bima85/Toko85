<div>
  @php
    $transactionColors = [
      'in' => 'success',
      'out' => 'danger',
      'adjustment' => 'warning',
      'return' => 'info',
    ];
  @endphp

  <!-- Flash Messages -->
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('message') }}
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-exclamation-triangle"></i>
      {{ session('error') }}
    </div>
  @endif

  <!-- Content Header -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h2 class="mb-0">
            <i class="fas fa-clipboard-list mr-2 text-primary"></i>
            Kartu Stok
          </h2>
          <small class="text-muted">Kelola dan pantau pergerakan stok produk</small>
        </div>
        <ol class="breadcrumb m-0 bg-transparent p-0">
          <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
          <li class="breadcrumb-item active">Kartu Stok</li>
        </ol>
      </div>
      <hr />
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row mb-3">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
      <a href="{{ route('stock-batches.index') }}" class="text-decoration-none">
        <div class="info-box bg-primary mb-0">
          <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Stok Batch</span>
            <span class="info-box-number">Lihat Batch Stok</span>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
      <a href="{{ route('admin.stock-reports') }}" class="text-decoration-none">
        <div class="info-box bg-info mb-0">
          <span class="info-box-icon"><i class="fas fa-chart-bar"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Laporan</span>
            <span class="info-box-number">Laporan Stok</span>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
      <a href="{{ route('admin.purchases') }}" class="text-decoration-none">
        <div class="info-box bg-success mb-0">
          <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Pembelian</span>
            <span class="info-box-number">Transaksi Masuk</span>
          </div>
        </div>
      </a>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
      <a href="{{ route('admin.sales') }}" class="text-decoration-none">
        <div class="info-box bg-danger mb-0">
          <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Penjualan</span>
            <span class="info-box-number">Transaksi Keluar</span>
          </div>
        </div>
      </a>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-3">
    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-success elevation-1">
          <i class="fas fa-arrow-down"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Stok Masuk</span>
          <span class="info-box-number">
            {{ number_format($totalIn, 2, ',', '.') }}
            <small>{{ $commonUnit }}</small>
          </span>
          <small class="text-muted">dari history transaksi</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-danger elevation-1">
          <i class="fas fa-arrow-up"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Total Stok Keluar</span>
          <span class="info-box-number">
            {{ number_format($totalOut, 2, ',', '.') }}
            <small>{{ $commonUnit }}</small>
          </span>
          <small class="text-muted">dari history transaksi</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
      <div class="info-box shadow-sm">
        <span class="info-box-icon {{ $net >= 0 ? 'bg-info' : 'bg-warning' }} elevation-1">
          <i class="fas fa-calculator"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Saldo (Masuk - Keluar)</span>
          <span class="info-box-number {{ $net >= 0 ? 'text-info' : 'text-warning' }}">
            {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 2, ',', '.') }}
            <small>{{ $commonUnit }}</small>
          </span>
          <small class="text-muted">perhitungan dari history</small>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
      <div class="info-box shadow-sm">
        <span class="info-box-icon bg-primary elevation-1">
          <i class="fas fa-warehouse"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text">Stok Aktual (Batch)</span>
          <span class="info-box-number text-primary">
            {{ number_format($currentStock, 2, ',', '.') }}
            <small>{{ $commonUnit }}</small>
          </span>
          <small class="text-muted">dari stok batch aktif</small>
        </div>
      </div>
    </div>
  </div>

  @if(abs($net - $currentStock) > 0.01)
    <div class="alert alert-warning alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h5><i class="icon fas fa-exclamation-triangle"></i> Perbedaan Stok Terdeteksi!</h5>
      <p>
        Terdapat perbedaan antara perhitungan dari history transaksi ({{ number_format($net, 2, ',', '.') }} {{ $commonUnit }}) 
        dengan stok aktual di batch ({{ number_format($currentStock, 2, ',', '.') }} {{ $commonUnit }}).
        <br>
        <strong>Selisih: {{ number_format(abs($net - $currentStock), 2, ',', '.') }} {{ $commonUnit }}</strong>
      </p>
      <p class="mb-0">
        <small>
          <i class="fas fa-info-circle mr-1"></i>
          Stok aktual dari <strong>Stok Batch</strong> adalah sumber data yang paling akurat. 
          Perbedaan ini mungkin terjadi karena ada transaksi manual atau penyesuaian yang belum tercatat di kartu stok.
        </small>
      </p>
    </div>
  @endif

  <!-- Main Card -->
  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-list mr-1"></i>
        Daftar Kartu Stok
      </h3>
      <div class="card-tools">
        <button
          type="button"
          class="btn btn-success btn-sm mr-1"
          wire:click="exportToExcel"
          wire:loading.attr="disabled"
        >
          <i class="fas fa-file-excel"></i>
          <span wire:loading.remove wire:target="exportToExcel">Export Excel</span>
          <span wire:loading wire:target="exportToExcel">Mengunduh...</span>
        </button>
        <a href="{{ route('stock-card.create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Tambah Kartu Stok
        </a>
      </div>
    </div>

    <div class="card-body">
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-5 col-sm-12 mb-2">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
            </div>
            <input
              type="text"
              class="form-control"
              placeholder="Cari produk atau catatan..."
              wire:model.live.debounce.300ms="search"
            />
            @if ($search)
              <div class="input-group-append">
                <button
                  class="btn btn-outline-secondary"
                  type="button"
                  wire:click="$set('search', '')"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            @endif
          </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-2">
          <select class="form-control" wire:model.live="filter_type">
            <option value="">-- Semua Tipe --</option>
            <option value="in">🟢 Masuk</option>
            <option value="out">🔴 Keluar</option>
            <option value="adjustment">🟡 Penyesuaian</option>
            <option value="return">🔵 Retur</option>
          </select>
        </div>
        <div class="col-md-2 col-sm-6 mb-2">
          <select class="form-control" wire:model.live="per_page">
            <option value="10">10 / halaman</option>
            <option value="15">15 / halaman</option>
            <option value="25">25 / halaman</option>
            <option value="50">50 / halaman</option>
          </select>
        </div>
        <div class="col-md-2 col-sm-12 mb-2">
          <div class="btn-group btn-group-sm w-100" role="group">
            <button
              type="button"
              class="btn {{ $groupByProduct ? 'btn-primary' : 'btn-outline-secondary' }}"
              wire:click="$set('groupByProduct', true)"
              title="Kelompokkan berdasarkan produk"
            >
              <i class="fas fa-layer-group"></i>
            </button>
            <button
              type="button"
              class="btn {{ ! $groupByProduct ? 'btn-primary' : 'btn-outline-secondary' }}"
              wire:click="$set('groupByProduct', false)"
              title="Tampilkan semua"
            >
              <i class="fas fa-list"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Loading Indicator -->
      <div wire:loading.delay class="text-center text-muted py-2 mb-2">
        <i class="fas fa-spinner fa-spin"></i>
        Memuat...
      </div>

      <!-- Bulk Actions -->
      @if (count($selectedCards) > 0)
        <div class="alert alert-info d-flex align-items-center justify-content-between mb-3">
          <div>
            <i class="fas fa-check-circle mr-2"></i>
            <strong>{{ count($selectedCards) }}</strong>
            item dipilih
          </div>
          <div>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary mr-2"
              wire:click="$set('selectedCards', [])"
            >
              <i class="fas fa-times"></i>
              Batal
            </button>
            <button
              type="button"
              class="btn btn-sm btn-danger"
              wire:click="bulkDelete"
              wire:confirm="Yakin ingin menghapus {{ count($selectedCards) }} kartu stok?"
            >
              <i class="fas fa-trash"></i>
              Hapus Pilihan
            </button>
          </div>
        </div>
      @endif

      <!-- Table -->
      @if ($stockCards->count() > 0)
        @if ($groupByProduct)
          {{-- GROUPED VIEW --}}
          <div class="mb-2">
            <button
              type="button"
              class="btn btn-sm btn-outline-primary mr-1"
              wire:click="expandAllGroups"
            >
              <i class="fas fa-expand-alt"></i>
              Buka Semua
            </button>
            <button
              type="button"
              class="btn btn-sm btn-outline-secondary"
              wire:click="collapseAllGroups"
            >
              <i class="fas fa-compress-alt"></i>
              Tutup Semua
            </button>
          </div>

          @php
            $grouped = $stockCards->groupBy('product_id');
          @endphp

          @foreach ($grouped as $productId => $cards)
            @php
              $product = $cards->first()->product;
              $totalIn = $cards->where('type', 'in')->sum('qty');
              $totalOut = $cards->where('type', 'out')->sum('qty');
              $balance = $totalIn - $totalOut;
              $isExpanded = in_array($productId, $expandedGroups);
            @endphp

            <div
              class="card card-outline {{ $balance >= 0 ? 'card-success' : 'card-danger' }} mb-2"
            >
              <div
                class="card-header py-2"
                style="cursor: pointer"
                wire:click="toggleGroup({{ $productId }})"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                    <i
                      class="fas {{ $isExpanded ? 'fa-chevron-down' : 'fa-chevron-right' }} mr-2 text-muted"
                    ></i>
                    <div>
                      <strong>{{ $product->nama_produk ?? 'Produk Tidak Ditemukan' }}</strong>
                      <small class="text-muted ml-2">
                        <code>{{ $product->kode_produk ?? '-' }}</code>
                      </small>
                    </div>
                  </div>
                  <div class="d-flex align-items-center">
                    <span class="badge badge-pill badge-secondary mr-2">
                      {{ $cards->count() }} transaksi
                    </span>
                    @if(!empty($holdTotals[$productId] ?? 0) && ($holdTotals[$productId] ?? 0) > 0)
                      <span class="badge badge-pill badge-warning mr-1" title="Jumlah yang di-hold">
                        <i class="fas fa-lock mr-1"></i>
                        Hold: {{ number_format($holdTotals[$productId] ?? 0, 2, ',', '.') }}
                      </span>
                    @endif
                    <span class="badge badge-pill badge-success mr-1" title="Total Masuk">
                      <i class="fas fa-arrow-down"></i>
                      {{ number_format($totalIn, 2, ',', '.') }}
                    </span>
                    <span class="badge badge-pill badge-danger mr-1" title="Total Keluar">
                      <i class="fas fa-arrow-up"></i>
                      {{ number_format($totalOut, 2, ',', '.') }}
                    </span>
                    <span
                      class="badge badge-pill {{ $balance >= 0 ? 'badge-primary' : 'badge-warning' }}"
                      title="Saldo"
                    >
                      <i class="fas fa-balance-scale"></i>
                      {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 2, ',', '.') }}
                    </span>
                  </div>
                </div>
              </div>
              @if ($isExpanded)
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-sm table-hover m-0">
                      <thead class="thead-light">
                        <tr>
                          <th class="text-center" style="width: 40px">
                            <input type="checkbox" disabled title="Pilih grup" />
                          </th>
                          <th class="text-center" style="width: 100px">Tipe</th>
                          <th class="text-right" style="width: 100px">Kuantitas</th>
                          <th style="width: 130px">Batch</th>
                          <th class="text-center" style="width: 140px">Waktu</th>
                          <th>Catatan</th>
                          <th class="text-center" style="width: 120px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($cards as $card)
                          <tr>
                            <td class="text-center align-middle">
                              <input
                                type="checkbox"
                                value="{{ $card->id }}"
                                wire:model.live="selectedCards"
                                wire:change="toggleCardSelection({{ $card->id }})"
                              />
                            </td>
                            <td class="text-center align-middle">
                              <span
                                class="badge badge-{{ $transactionColors[$card->type] ?? 'secondary' }} px-2 py-1"
                              >
                                @switch($card->type)
                                  @case('in')
                                    <i class="fas fa-arrow-down mr-1"></i>

                                    @break
                                  @case('out')
                                    <i class="fas fa-arrow-up mr-1"></i>

                                    @break
                                  @case('adjustment')
                                    <i class="fas fa-sliders-h mr-1"></i>

                                    @break
                                  @case('return')
                                    <i class="fas fa-undo mr-1"></i>

                                    @break
                                @endswitch
                                {{ $transactionTypes[$card->type] ?? $card->type }}
                              </span>
                            </td>
                            <td class="text-right align-middle">
                              <strong class="text-{{ $transactionColors[$card->type] ?? 'dark' }}">
                                {{ $card->type === 'out' ? '-' : '' }}{{ number_format($card->qty, 2, ',', '.') }}
                              </strong>
                            </td>
                            <td class="align-middle">
                              @if ($card->batch)
                                  <span class="badge badge-info">
                                    <i class="fas fa-cube mr-1"></i>
                                    {{ $card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id }}
                                  </span>
                                  @if(isset($card->batch->status) && $card->batch->status === 'hold')
                                    <small class="badge badge-warning ml-1">HOLD</small>
                                  @endif
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td class="text-center align-middle">
                              <div>
                                <i class="far fa-calendar-alt text-muted mr-1"></i>
                                {{ $card->created_at->format('d/m/Y') }}
                              </div>
                              <small class="text-muted">
                                <i class="far fa-clock mr-1"></i>
                                {{ $card->created_at->format('H:i') }}
                              </small>
                            </td>
                            <td class="align-middle">
                              @if ($card->note)
                                <span title="{{ $card->note }}" data-toggle="tooltip">
                                  {{ Str::limit($card->note, 30) }}
                                </span>
                              @else
                                <span class="text-muted font-italic">-</span>
                              @endif
                            </td>
                            <td class="text-center align-middle">
                              <div class="btn-group btn-group-sm">
                                <a
                                  href="{{ route('stock-card.show', $card->id) }}"
                                  class="btn btn-info btn-xs"
                                  title="Lihat"
                                >
                                  <i class="fas fa-eye"></i>
                                </a>
                                <a
                                  href="{{ route('stock-card.edit', $card->id) }}"
                                  class="btn btn-warning btn-xs"
                                  title="Edit"
                                >
                                  <i class="fas fa-edit"></i>
                                </a>
                                <button
                                  type="button"
                                  class="btn btn-danger btn-xs"
                                  wire:click="deleteStockCard({{ $card->id }})"
                                  wire:confirm="Yakin ingin menghapus?"
                                  title="Hapus"
                                >
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              @endif
            </div>
          @endforeach
        @else
          {{-- UNGROUPED VIEW (Original Table) --}}
          <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
              <thead class="thead-light">
                <tr>
                  <th class="text-center" style="width: 40px">
                    <input
                      type="checkbox"
                      wire:model.live="selectAll"
                      wire:change="toggleSelectAll"
                      title="Pilih semua"
                    />
                  </th>
                  <th class="text-center" style="width: 50px">#</th>
                  <th>Produk</th>
                  <th class="text-center" style="width: 110px">Tipe</th>
                  <th class="text-right" style="width: 100px">Kuantitas</th>
                  <th style="width: 130px">Batch</th>
                  <th class="text-center" style="width: 140px">Waktu</th>
                  <th>Catatan</th>
                  <th class="text-center" style="width: 120px">Aksi</th>
                </tr>
              </thead>
              <tbody wire:loading.class="opacity-50">
                @foreach ($stockCards as $index => $card)
                  <tr>
                    <td class="text-center align-middle">
                      <input
                        type="checkbox"
                        value="{{ $card->id }}"
                        wire:model.live="selectedCards"
                        wire:change="toggleCardSelection({{ $card->id }})"
                      />
                    </td>
                    <td class="text-center align-middle">
                      {{ $stockCards->firstItem() + $index }}
                    </td>
                    <td class="align-middle">
                      <div>
                        <strong>{{ $card->product->nama_produk ?? '-' }}</strong>
                      </div>
                      <small class="text-muted">
                        <code>{{ $card->product->kode_produk ?? '-' }}</code>
                      </small>
                    </td>
                    <td class="text-center align-middle">
                      <span
                        class="badge badge-{{ $transactionColors[$card->type] ?? 'secondary' }} px-2 py-1"
                      >
                        @switch($card->type)
                          @case('in')
                            <i class="fas fa-arrow-down mr-1"></i>

                            @break
                          @case('out')
                            <i class="fas fa-arrow-up mr-1"></i>

                            @break
                          @case('adjustment')
                            <i class="fas fa-sliders-h mr-1"></i>

                            @break
                          @case('return')
                            <i class="fas fa-undo mr-1"></i>

                            @break
                        @endswitch
                        {{ $transactionTypes[$card->type] ?? $card->type }}
                      </span>
                    </td>
                    <td class="text-right align-middle">
                      <strong class="text-{{ $transactionColors[$card->type] ?? 'dark' }}">
                        {{ $card->type === 'out' ? '-' : '' }}{{ number_format($card->qty, 2, ',', '.') }}
                      </strong>
                    </td>
                    <td class="align-middle">
                      @if ($card->batch)
                        <span class="badge badge-info">
                          <i class="fas fa-cube mr-1"></i>
                          {{ $card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id }}
                        </span>
                        @if(isset($card->batch->status) && $card->batch->status === 'hold')
                          <small class="badge badge-warning ml-1">HOLD</small>
                        @endif
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td class="text-center align-middle">
                      <div>
                        <i class="far fa-calendar-alt text-muted mr-1"></i>
                        {{ $card->created_at->format('d/m/Y') }}
                      </div>
                      <small class="text-muted">
                        <i class="far fa-clock mr-1"></i>
                        {{ $card->created_at->format('H:i') }}
                      </small>
                    </td>
                    <td class="align-middle">
                      @if ($card->note)
                        <span title="{{ $card->note }}" data-toggle="tooltip">
                          {{ Str::limit($card->note, 30) }}
                        </span>
                      @else
                        <span class="text-muted font-italic">Tidak ada catatan</span>
                      @endif
                    </td>
                    <td class="text-center align-middle">
                      <div class="btn-group btn-group-sm">
                        <a
                          href="{{ route('stock-card.show', $card->id) }}"
                          class="btn btn-info"
                          title="Lihat Detail"
                          data-toggle="tooltip"
                        >
                          <i class="fas fa-eye"></i>
                        </a>
                        <a
                          href="{{ route('stock-card.edit', $card->id) }}"
                          class="btn btn-warning"
                          title="Edit"
                          data-toggle="tooltip"
                        >
                          <i class="fas fa-edit"></i>
                        </a>
                        <button
                          type="button"
                          class="btn btn-danger"
                          wire:click="deleteStockCard({{ $card->id }})"
                          wire:confirm="Yakin ingin menghapus kartu stok ini?"
                          title="Hapus"
                          data-toggle="tooltip"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
          <div class="text-muted mb-2">
            Menampilkan {{ $stockCards->firstItem() }} - {{ $stockCards->lastItem() }} dari
            {{ $stockCards->total() }} data
          </div>
          <div>
            {{ $stockCards->onEachSide(1)->links('pagination::bootstrap-4') }}
          </div>
        </div>
      @else
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="fas fa-inbox fa-4x text-muted"></i>
          </div>
          <h5 class="text-muted">Tidak ada data kartu stok</h5>
          <p class="text-muted">
            @if ($search || $filter_type)
              Tidak ditemukan kartu stok dengan filter yang dipilih.
              <br />
              <button
                class="btn btn-outline-secondary btn-sm mt-2"
                wire:click="$set('search', ''); $set('filter_type', '')"
              >
                <i class="fas fa-times"></i>
                Reset Filter
              </button>
            @else
                Mulai dengan menambahkan kartu stok pertama.
            @endif
          </p>
          <a href="{{ route('stock-card.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i>
            Tambah Kartu Stok
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

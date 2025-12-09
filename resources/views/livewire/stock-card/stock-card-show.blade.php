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
          <h1 class="m-0">Detail Kartu Stok</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">
              <a href="{{ route('stock-card.index') }}">Kartu Stok</a>
            </li>
            <li class="breadcrumb-item active">Detail</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Main Card -->
        <div class="col-md-8">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Informasi Kartu Stok #{{ $stockCard->id }}</h3>
            </div>
            <div class="card-body">
              <!-- Produk Info -->
              <div class="form-group row">
                <label class="col-sm-3">Produk</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    <strong>{{ $stockCard->product->nama_produk }}</strong>
                    <br />
                    <small class="text-muted">Kode: {{ $stockCard->product->kode_produk }}</small>
                  </p>
                </div>
              </div>

              <!-- Tipe Transaksi -->
              <div class="form-group row">
                <label class="col-sm-3">Tipe Transaksi</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    <span
                      class="badge badge-{{ $transactionColors[$stockCard->type] ?? 'secondary' }}"
                    >
                      {{ $transactionType }}
                    </span>
                  </p>
                </div>
              </div>

              <!-- Kuantitas -->
              <div class="form-group row">
                <label class="col-sm-3">Kuantitas</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    <strong class="text-lg">
                      {{ number_format($stockCard->qty, 2, ',', '.') }}
                      {{ $stockCard->product->satuan ?? 'pcs' }}
                    </strong>
                  </p>
                </div>
              </div>

              <!-- Batch -->
              <div class="form-group row">
                <label class="col-sm-3">Batch</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    @if ($stockCard->batch)
                      <span class="badge badge-info">
                        {{ $stockCard->batch->nama_tumpukan ?? 'Batch #' . $stockCard->batch->id }}
                      </span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </p>
                </div>
              </div>

              <!-- Lokasi -->
              <div class="form-group row">
                <label class="col-sm-3">Lokasi</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    @if ($stockCard->from_location || $stockCard->to_location)
                      <strong>Dari:</strong>
                      {{ $stockCard->from_location ?? '-' }}
                      <br />
                      <strong>Ke:</strong>
                      {{ $stockCard->to_location ?? '-' }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </p>
                </div>
              </div>

              <!-- Referensi -->
              <div class="form-group row">
                <label class="col-sm-3">Referensi</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    @if ($stockCard->reference_type)
                      <strong>Tipe:</strong>
                      {{ $referenceType }}
                      <br />
                      <strong>ID:</strong>
                      {{ $stockCard->reference_id ?? '-' }}
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </p>
                </div>
              </div>

              <!-- Catatan -->
              <div class="form-group row">
                <label class="col-sm-3">Catatan</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    {{ $stockCard->note ?? '-' }}
                  </p>
                </div>
              </div>

              <!-- Tanggal -->
              <div class="form-group row">
                <label class="col-sm-3">Tanggal Dibuat</label>
                <div class="col-sm-9">
                  <p class="form-control-plaintext">
                    {{ $stockCard->created_at->format('d M Y H:i:s') }}
                  </p>
                </div>
              </div>

              @if ($stockCard->updated_at->notEqualTo($stockCard->created_at))
                <div class="form-group row">
                  <label class="col-sm-3">Terakhir Diperbarui</label>
                  <div class="col-sm-9">
                    <p class="form-control-plaintext">
                      {{ $stockCard->updated_at->format('d M Y H:i:s') }}
                    </p>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>

        <!-- Actions Card -->
        <div class="col-md-4">
          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Aksi</h3>
            </div>
            <div class="card-body">
              <a
                href="{{ route('stock-card.edit', $stockCard->id) }}"
                class="btn btn-block btn-warning mb-2"
              >
                <i class="fas fa-edit"></i>
                Edit
              </a>
              <button
                type="button"
                class="btn btn-block btn-danger"
                wire:click="deleteStockCard"
                wire:confirm="Yakin ingin menghapus kartu stok ini?"
              >
                <i class="fas fa-trash"></i>
                Hapus
              </button>
            </div>
          </div>

          <!-- Info Card -->
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Informasi Produk</h3>
            </div>
            <div class="card-body">
              <dl class="row">
                <dt class="col-sm-6">Kategori</dt>
                <dd class="col-sm-6">
                  @if ($stockCard->product->category)
                    {{ $stockCard->product->category->nama_kategori ?? '-' }}
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </dd>

                <dt class="col-sm-6">Satuan</dt>
                <dd class="col-sm-6">
                  {{ $stockCard->product->satuan ?? '-' }}
                </dd>

                <dt class="col-sm-6">Harga Dasar</dt>
                <dd class="col-sm-6">
                  Rp {{ number_format($stockCard->product->base_price ?? 0, 0, ',', '.') }}
                </dd>
              </dl>
            </div>
          </div>

          <!-- Relasi Card -->
          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">Relasi & Referensi</h3>
            </div>
            <div class="card-body">
              <!-- Stock Batch -->
              @if ($stockCard->batch)
                <div class="mb-3">
                  <small class="text-muted d-block mb-1">📦 Batch Stok</small>
                  <a
                    href="{{ route('stock-batches.index') }}"
                    class="btn btn-sm btn-info btn-block"
                  >
                    <i class="fas fa-cube"></i>
                    {{ $stockCard->batch->nama_tumpukan ?? 'Batch #' . $stockCard->batch->id }}
                  </a>
                </div>
              @endif

              <!-- Stock Reports -->
              <div class="mb-3">
                <small class="text-muted d-block mb-1">📊 Laporan Stok</small>
                <a
                  href="{{ route('admin.stock-reports') }}"
                  class="btn btn-sm btn-primary btn-block"
                >
                  <i class="fas fa-chart-bar"></i>
                  Lihat Laporan
                </a>
              </div>

              <!-- Reference (Purchase/Sale) -->
              @if ($stockCard->reference_type && $stockCard->reference_id)
                <div class="mb-3">
                  <small class="text-muted d-block mb-1">🔗 Dokumen {{ $referenceType }}</small>

                  @if ($stockCard->reference_type === 'purchase')
                    <a
                      href="{{ route('admin.purchases') }}"
                      class="btn btn-sm btn-success btn-block"
                    >
                      <i class="fas fa-shopping-cart"></i>
                      Pembelian #{{ $stockCard->reference_id }}
                    </a>
                  @elseif ($stockCard->reference_type === 'sale')
                    <a href="{{ route('admin.sales') }}" class="btn btn-sm btn-danger btn-block">
                      <i class="fas fa-dollar-sign"></i>
                      Penjualan #{{ $stockCard->reference_id }}
                    </a>
                  @else
                    <span class="text-muted">
                      {{ $referenceType }} #{{ $stockCard->reference_id }}
                    </span>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div class="row mt-3">
        <div class="col-12">
          <a href="{{ route('stock-card.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Kembali
          </a>
        </div>
      </div>
    </div>
  </section>
</div>

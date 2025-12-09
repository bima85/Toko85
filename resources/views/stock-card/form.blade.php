<div class="wrapper">
  <!-- Header -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="text-muted">Form Kartu Stok</span>
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
            <h1 class="m-0">
              @if ($stockCard)
                Edit Kartu Stok
              @else
                Tambah Kartu Stok Baru
              @endif
            </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item">
                <a href="{{ route('stock-card.index') }}">Kartu Stok</a>
              </li>
              <li class="breadcrumb-item active">
                @if ($stockCard)
                  Edit
                @else
                  Tambah
                @endif
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">
                  @if ($stockCard)
                    Edit
                  @else
                    Form Tambah
                  @endif
                  Kartu Stok
                </h3>
              </div>

              <form wire:submit="save">
                <div class="card-body">
                  <!-- Produk -->
                  <div class="form-group">
                    <label for="product_id">
                      Produk
                      <span class="text-danger">*</span>
                    </label>
                    <select
                      id="product_id"
                      class="form-control @error('product_id') is-invalid @enderror"
                      wire:model="product_id"
                    >
                      <option value="">Pilih Produk</option>
                      @foreach ($products as $product)
                        <option value="{{ $product->id }}">
                          {{ $product->nama_produk }} ({{ $product->kode_produk }})
                        </option>
                      @endforeach
                    </select>
                    @error('product_id')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Tipe Transaksi -->
                  <div class="form-group">
                    <label for="type">
                      Tipe Transaksi
                      <span class="text-danger">*</span>
                    </label>
                    <select
                      id="type"
                      class="form-control @error('type') is-invalid @enderror"
                      wire:model="type"
                    >
                      <option value="in">Masuk</option>
                      <option value="out">Keluar</option>
                      <option value="adjustment">Penyesuaian</option>
                      <option value="return">Retur</option>
                    </select>
                    @error('type')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Kuantitas -->
                  <div class="form-group">
                    <label for="qty">
                      Kuantitas
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      type="number"
                      id="qty"
                      class="form-control @error('qty') is-invalid @enderror"
                      placeholder="Masukkan kuantitas"
                      wire:model="qty"
                      step="0.01"
                    />
                    @error('qty')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Batch -->
                  <div class="form-group">
                    <label for="batch_id">Batch (Opsional)</label>
                    <select
                      id="batch_id"
                      class="form-control @error('batch_id') is-invalid @enderror"
                      wire:model="batch_id"
                    >
                      <option value="">Pilih Batch</option>
                      @foreach ($batches as $batch)
                        <option value="{{ $batch->id }}">
                          {{ $batch->batch_number }} - {{ $batch->supplier->name ?? '-' }}
                        </option>
                      @endforeach
                    </select>
                    @error('batch_id')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Divider -->
                  <hr />

                  <!-- Lokasi Asal -->
                  <div class="form-group">
                    <label for="from_location">Dari Lokasi (Opsional)</label>
                    <input
                      type="text"
                      id="from_location"
                      class="form-control @error('from_location') is-invalid @enderror"
                      placeholder="Contoh: Gudang A, Rak 1"
                      wire:model="from_location"
                    />
                    @error('from_location')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Lokasi Tujuan -->
                  <div class="form-group">
                    <label for="to_location">Ke Lokasi (Opsional)</label>
                    <input
                      type="text"
                      id="to_location"
                      class="form-control @error('to_location') is-invalid @enderror"
                      placeholder="Contoh: Gudang B, Rak 2"
                      wire:model="to_location"
                    />
                    @error('to_location')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Divider -->
                  <hr />

                  <!-- Tipe Referensi -->
                  <div class="form-group">
                    <label for="reference_type">Tipe Referensi (Opsional)</label>
                    <select
                      id="reference_type"
                      class="form-control @error('reference_type') is-invalid @enderror"
                      wire:model="reference_type"
                    >
                      <option value="">Pilih Tipe Referensi</option>
                      <option value="purchase">Pembelian</option>
                      <option value="sale">Penjualan</option>
                      <option value="adjustment">Penyesuaian</option>
                      <option value="return">Retur</option>
                      <option value="transfer">Pemindahan</option>
                    </select>
                    @error('reference_type')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Referensi ID -->
                  <div class="form-group">
                    <label for="reference_id">ID Referensi (Opsional)</label>
                    <input
                      type="number"
                      id="reference_id"
                      class="form-control @error('reference_id') is-invalid @enderror"
                      placeholder="Masukkan ID referensi"
                      wire:model="reference_id"
                    />
                    @error('reference_id')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>

                  <!-- Catatan -->
                  <div class="form-group">
                    <label for="note">Catatan (Opsional)</label>
                    <textarea
                      id="note"
                      class="form-control @error('note') is-invalid @enderror"
                      rows="4"
                      placeholder="Masukkan catatan tambahan"
                      wire:model="note"
                    ></textarea>
                    @error('note')
                      <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                  </div>
                </div>

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan
                  </button>
                  <a href="{{ route('stock-card.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                  </a>
                </div>
              </form>
            </div>
          </div>

          <!-- Info Card -->
          <div class="col-md-4">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Bantuan</h3>
              </div>
              <div class="card-body">
                <h5>Tipe Transaksi</h5>
                <ul class="small">
                  <li>
                    <strong>Masuk:</strong>
                    Stok masuk ke gudang
                  </li>
                  <li>
                    <strong>Keluar:</strong>
                    Stok keluar dari gudang
                  </li>
                  <li>
                    <strong>Penyesuaian:</strong>
                    Koreksi stok
                  </li>
                  <li>
                    <strong>Retur:</strong>
                    Barang dikembalikan
                  </li>
                </ul>

                <hr />

                <h5>Catatan Penting</h5>
                <p class="small text-muted">
                  • Produk wajib dipilih
                  <br />
                  • Kuantitas harus lebih dari 0
                  <br />
                  • Batch diisi jika ada
                  <br />
                  • Lokasi berupa deskripsi lokasi stok
                </p>
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
</div>

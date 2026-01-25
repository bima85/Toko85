@push('styles')
{{-- Load purchase.css from resources/css --}}
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}" />
@endpush

<div class="purchases-list-wrapper">
  @if (session()->has('message'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <i class="icon fas fa-check"></i>
    {{ session('message') }}
  </div>
  @endif

  @if (session()->has('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <i class="icon fas fa-exclamation"></i>
    {{ session('error') }}
  </div>
  @endif

  <!-- Content Header -->
  <div class="row mb-3">
    <div class="col-md-12">
      <h2 class="mb-0">
        <i class="fas fa-shopping-cart mr-2"></i>
        Manajemen Pembelian
      </h2>
      <small class="text-muted">Kelola data pembelian dari supplier</small>
      <hr />
    </div>
  </div>

  <!-- Inline Create Form -->
  @if ($showCreateForm)
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-plus-circle mr-2"></i>
            {{ $editingPurchaseId ? 'Edit Pembelian' : 'Buat Pembelian Baru' }}
          </h3>
          <div class="card-tools">
            <button wire:click="cancel" type="button" class="btn btn-sm btn-secondary">
              <i class="fas fa-times"></i>
              Batal
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Informasi Pembelian -->
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>No Invoice</strong></label>
                <input wire:model.live="no_invoice" type="text" class="form-control" readonly />
                @error('no_invoice')
                <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>
                  <strong>
                    Tanggal Pembelian
                    <span class="text-danger">*</span>
                  </strong>
                </label>
                <input wire:model="tanggal_pembelian" type="date"
                  class="form-control @error('tanggal_pembelian') is-invalid @enderror" />
                @error('tanggal_pembelian')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>
                  <strong>Supplier/Owner</strong>
                </label>
                <div class="input-group">
                  <input type="text" list="owners-datalist" wire:model="ownerFilter"
                    wire:change="ownerChanged($event.target.value)" class="form-control"
                    placeholder="Cari Supplier/Owner..." />
                  <datalist id="owners-datalist">
                    @foreach ($owners as $owner)
                    <option value="{{ $owner }}"></option>
                    @endforeach
                  </datalist>
                  <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" type="button" wire:click="openOwnerModal"
                      title="Tambah Supplier/Owner Baru">
                      <i class="fas fa-plus-circle"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-group">
                <label>
                  <strong>
                    Perusahaan
                    <span class="text-danger">*</span>
                  </strong>
                </label>
                <div class="input-group">
                  <select wire:model="supplier_id" wire:change="regenerateInvoice"
                    class="form-control @error('supplier_id') is-invalid @enderror">
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach ($suppliers as $sup)
                    <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                    @endforeach
                  </select>
                  <div class="input-group-append">
                    <button class="btn btn-primary btn-sm" type="button" wire:click="openSupplierModal"
                      title="Tambah Supplier Baru">
                      <i class="fas fa-plus-circle"></i>
                    </button>
                  </div>
                </div>
                @error('supplier_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label><strong>Lokasi</strong></label>
                <select wire:change="selectLocation($event.target.value)"
                  class="form-control @error('store_id') is-invalid @enderror @error('warehouse_id') is-invalid @enderror">
                  <option value="">-- Tidak Ada --</option>
                  <optgroup label="Toko">
                    @foreach ($stores as $s)
                    <option value="store:{{ $s->id }}" @if($store_id==$s->id) selected @endif
                      >
                      {{ $s->nama_toko }}
                    </option>
                    @endforeach
                  </optgroup>
                  <optgroup label="Gudang">
                    @foreach ($warehouses as $wh)
                    <option value="warehouse:{{ $wh->id }}" @if($warehouse_id==$wh->id) selected @endif
                      >
                      {{ $wh->nama_gudang }}
                    </option>
                    @endforeach
                  </optgroup>
                </select>
                @error('store_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror

                @error('warehouse_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
              </div>
            </div>
          </div>

          <div class="form-group m-2">
            <label><strong>Keterangan</strong></label>
            <textarea wire:model="keterangan" class="form-control" rows="2"
              placeholder="Catatan pembelian..."></textarea>
            @error('keterangan')
            <small class="text-danger d-block mt-1">{{ $message }}</small>
            @enderror
          </div>

          <!-- Items Table using Yajra DataTable -->
          @if (count($purchaseItems) > 0)
          <div class="form-group mt-4">
            <label><strong>Item Pembelian</strong></label>
            <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
              <div class="custom-control custom-switch" style="min-height: 30px">
                <input type="checkbox" class="custom-control-input" id="batchToggleSwitch"
                  wire:model.live="batch_enabled" />
                <label class="custom-control-label" for="batchToggleSwitch">
                  Aktifkan Batch
                </label>
              </div>
              <small class="text-muted">
                Jika aktif, qty toko dijumlahkan dari batch di kolom Batch.
              </small>
            </div>
            <!-- Items Table -->
            <div class="table-responsive purchases-table--stack">
              <table id="purchaseItemsTable"
                class="table table-sm table-bordered table-hover table-striped align-middle mb-0" style="width: 100%">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 2%">#</th>
                    <th style="width: 8.5%">Kategori</th>
                    <th style="width: 10%">Subkategori</th>
                    <th style="width: 15%">Produk</th>
                    @if($batch_enabled)
                    <!-- SAAT BATCH DIAKTIFKAN: Menampilkan kolom Batch dan Tujuan terpisah -->
                    <th style="width: 18%">Batch</th>
                    <th style="width: 15%">Tujuan</th>
                    @else
                    <!-- SAAT BATCH DINONAKTIFKAN: Menampilkan kolom Qty dan Tujuan -->
                    <th style="width: 8%">Qty</th>
                    <th style="width: 16%">Tujuan</th>
                    @endif
                    <th style="width: 7%">Unit</th>
                    <th style="width: 10%">Harga Beli</th>
                    <th style="width: 10%">Total</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 8%">Hapus</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($purchaseItems as $index => $item)
                  <tr wire:key="item-{{ $index }}">
                    <td class="text-center" data-label="#">{{ $index + 1 }}</td>
                    <td data-label="Kategori">
                      <select wire:model="purchaseItems.{{ $index }}.category_id" wire:change="$refresh"
                        class="form-control form-control-sm">
                        <option value="">--</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                          {{ $category->nama_kategori }}
                        </option>
                        @endforeach

                        <option value="__add_category__"
                          style="background-color: #28a745; color: white; font-weight: bold">
                          Tambah Kategori
                        </option>
                      </select>
                    </td>
                    <td data-label="Subkategori">
                      <select wire:model="purchaseItems.{{ $index }}.subcategory_id"
                        class="form-control form-control-sm">
                        <option value="">--</option>
                        @php
                        $categoryId = $item['category_id'];
                        $filteredSubcategories = $categoryId ? $this->getSubcategoriesByCategory($categoryId) : [];
                        @endphp

                        @foreach ($filteredSubcategories as $subcat)
                        <option value="{{ $subcat->id }}">
                          {{ $subcat->nama_subkategori }}
                        </option>
                        @endforeach

                        <option value="__add_subcategory__"
                          style="background-color: #28a745; color: white; font-weight: bold">
                          ambah Subkategori
                        </option>
                      </select>
                    </td>
                    <td data-label="Produk">
                      <input type="text" wire:model.live="purchaseItems.{{ $index }}.product_search"
                        wire:change="updateProductFromSearch({{ $index }})" list="products_{{ $index }}"
                        onchange="handleProductSearchChange(event, {{ $index }})" class="form-control form-control-sm"
                        placeholder="Cari produk..." autocomplete="off" />
                      <datalist id="products_{{ $index }}">
                        @foreach ($products as $prod)
                        <option value="{{ $prod->nama_produk }}" data-id="{{ $prod->id }}">
                          {{ $prod->nama_produk }}
                        </option>
                        @endforeach

                        <option value="__add_product__"
                          style="background-color: #28a745; color: white; font-weight: bold">
                          Tambah Produk Baru
                        </option>
                      </datalist>
                      @if ($item['product_id'] === '')
                      <small class="text-muted d-block mt-1">
                        Ketik nama produk untuk mencari
                      </small>
                      @endif
                    </td>
                    <!-- ===== KOLOM BATCH ===== -->
                    @if($this->batch_enabled)
                    <td data-label="Batch">
                      <!-- BATCH ENABLED: Menampilkan interface untuk mengelola batch -->
                      <div>
                        <!-- Label "Batch & Qty" untuk menunjukkan bahwa user dapat input batch name dan qty -->

                        <!-- Container untuk menampilkan semua batch yang sudah ditambahkan -->
                        <div class="batch-actions">
                          <!-- Wrapper untuk batch rows yang akan stack vertikal -->
                          <div class="batch-rows-wrapper">
                            <!-- Loop: Tampilkan setiap batch sebagai "chip" (badge dengan input) -->
                            @foreach ($item['batches'] ?? [] as $bIndex => $batch)
                            <div class="batch-row" wire:key="batch-{{ $index }}-{{ $bIndex }}">
                              <div class="batch-chip">
                                <!-- Input 1: Nama Batch (read-only, auto-generate: T1, T2, T3 dst) -->
                                <input id="batch-name-{{ $index }}-{{ $bIndex }}" type="text"
                                  class="form-control form-control-sm" placeholder="T1"
                                  value="{{ $batch['name'] ?? '' }}" readonly />
                                <!-- Input 2: Quantity batch (jumlah barang untuk batch ini) -->
                                <input type="number" min="0" class="form-control form-control-sm" placeholder="0"
                                  value="{{ $batch['qty'] ?? 0 }}"
                                  wire:input="updateBatchField({{ $index }}, {{ $bIndex }}, 'qty', $event.target.value)"
                                  aria-label="batch-qty-{{ $index }}-{{ $bIndex }}" />
                                <!-- Tombol Hapus: Menghapus batch ini dari daftar -->
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                  wire:click="removeBatchRow({{ $index }}, {{ $bIndex }})" @if(count($item['batches'] ??
                                  []) <=1) disabled title="Minimal harus ada 1 batch" @else title="Hapus batch" @endif>
                                  <i class="fas fa-times"></i>
                                </button>
                              </div>
                            </div>
                            @endforeach
                          </div>

                          <!-- Tombol Tambah Batch: Menambahkan batch baru ke dalam item ini -->
                          <div class="batch-add">
                            <button type="button" class="btn btn-outline-primary btn-sm"
                              wire:click="addBatchRow({{ $index }})" title="Tambah Batch">
                              <i class="fas fa-plus"></i>
                            </button>
                          </div>
                        </div>
                      </div>

                      <!-- Pesan informasi: Menjelaskan bahwa qty toko dijumlahkan dari semua batch -->
                      @if (count($item['batches'] ?? []) > 0)

                      @endif
                    </td>
                    @endif

                    <!-- ===== KOLOM TUJUAN / QTY ===== -->
                    @if($this->batch_enabled)
                    <!-- BATCH ENABLED: Menampilkan kolom Tujuan untuk setiap batch -->
                    <td data-label="Tujuan">
                      <!-- Loop: Tampilkan select tujuan untuk setiap batch yang ada -->
                      <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach ($item['batches'] ?? [] as $bIndex => $batch)
                        <div style="display: flex; gap: 4px; align-items: center;">

                          <select wire:model.live="purchaseItems.{{ $index }}.batches.{{ $bIndex }}.destination_type"
                            class="form-control form-control-sm" style="flex: 1;">
                            <option value="">-- Pilih --</option>
                            <option value="gudang">Gudang</option>
                            <option value="toko">Toko</option>
                          </select>
                        </div>
                        @endforeach
                      </div>
                    </td>
                    @else
                    <!-- BATCH DISABLED: Menampilkan kolom Qty dan Tujuan dengan input conditional -->
                    <!-- Kolom Qty: Input quantity item yang bisa diedit -->
                    <td data-label="Qty" class="text-center">
                      <!-- Input Qty: Untuk menginput jumlah barang pembelian -->
                      <input wire:model="purchaseItems.{{ $index }}.qty" wire:change="updateTotal({{ $index }})"
                        type="number" min="0" class="form-control form-control-sm text-center"
                        style="max-width: 80px; margin: 0 auto;" />
                    </td>

                    <!-- BATCH DISABLED: Menampilkan kolom Tujuan dengan input conditional -->
                    <td data-label="Tujuan">
                      <!-- Selector Tujuan: Memilih kemana barang akan dikirim -->
                      <div style="display: flex; gap: 6px; align-items: flex-end; flex-direction: row; width: 100%">
                        <div style="flex: 1">
                          <select wire:model.live="purchaseItems.{{ $index }}.destination_type"
                            class="form-control form-control-sm">
                            <option value="gudang" style="width: 86px">Gudang</option>
                            <option value="toko" style="width: 86px">Toko</option>
                          </select>
                        </div>

                        <!-- Input Gudang Qty: Muncul HANYA ketika destination_type = 'gudang' -->
                        <!-- Menginput jumlah barang yang dikirim ke gudang -->
                        @if ($item['destination_type'] === 'gudang')
                        <div style="flex: 0 0 auto">
                          <input wire:model="purchaseItems.{{ $index }}.qty_gudang"
                            wire:change="updateTotal({{ $index }})" type="number" min="0"
                            class="form-control form-control-sm" placeholder="0" style="max-width: 53px;" />
                        </div>
                        @endif
                      </div>
                    </td>
                    @endif

                    <td data-label="Unit">
                      @if($this->batch_enabled)
                      <!-- BATCH ENABLED: Menampilkan unit untuk setiap batch -->
                      <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach ($item['batches'] ?? [] as $bIndex => $batch)
                        <div style="display: flex; gap: 4px; align-items: center;">
                          <select wire:model="purchaseItems.{{ $index }}.batches.{{ $bIndex }}.unit_id"
                            class="form-control form-control-sm" style="flex: 1;">
                            <option value="">--</option>
                            @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach
                          </select>
                        </div>
                        @endforeach
                      </div>
                      @else
                      <!-- BATCH DISABLED: Unit normal untuk item -->
                      <select wire:model="purchaseItems.{{ $index }}.unit_id" class="form-control form-control-sm">
                        <option value="">--</option>
                        @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                      </select>
                      @endif
                    </td>
                    <td data-label="Harga Beli">
                      <input wire:model="purchaseItems.{{ $index }}.harga_beli" wire:change="updateTotal({{ $index }})"
                        type="number" step="0.01" min="0" class="form-control form-control-sm" placeholder="0" />
                    </td>
                    <td class="text-right font-weight-bold" data-label="Total">
                      @php
                      $itemQty = $item['qty'] ?? 0;
                      $itemHarga = $item['harga_beli'] ?? 0;
                      $itemUnit = $item['unit_id'] ?? null;
                      $conv = 1;
                      if ($itemUnit) {
                      $unit = \App\Models\Unit::find($itemUnit);
                      $conv = $unit ? (float) ($unit->conversion_value ?? 1) : 1;
                      }
                      $itemTotal = $itemQty * $conv * $itemHarga;
                      @endphp

                      Rp {{ number_format($itemTotal, 0, ',', '.') }}
                    </td>
                    <td data-label="Status">
                      <select wire:model="purchaseItems.{{ $index }}.status" class="form-control form-control-sm">
                        <option value="pending">Pending</option>
                        <option value="hold">Hold / Keep</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                      </select>
                    </td>
                    <td class="text-center" data-label="Hapus">
                      <button wire:click="removeItem({{ $index }})" type="button" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot class="bg-light font-weight-bold">
                  <tr>
                    @if($batch_enabled)
                    <td colspan="8" class="text-right">TOTAL:</td>
                    @else
                    <td colspan="9" class="text-right">TOTAL:</td>
                    @endif
                    <td colspan="3" class="text-right text-success">
                      Rp {{ number_format($this->getTotalProperty(), 0, ',', '.') }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
            @error('purchaseItems')
            <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>
          @else
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Belum ada item. Klik "Tambah Item" untuk menambah item pembelian.
          </div>
          @endif
        </div>
        <div class="card-footer">
          <button wire:click="addItem" type="button" class="btn btn-info mr-2">
            <i class="fas fa-plus-circle mr-1"></i>
            Tambah Item
          </button>
          <div class="float-right">
            <button type="button" class="btn btn-primary mr-2"
              onclick="printPurchaseReceipt({{ $editingPurchaseId ?? 'null' }})">
              <i class="fas fa-print mr-1"></i>
              Cetak
            </button>
            <button wire:click="cancel" class="btn btn-secondary mr-2">
              <i class="fas fa-times"></i>
              Batal
            </button>
            <button wire:click="save" class="btn btn-success">
              <i class="fas fa-save"></i>
              Simpan Pembelian
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- List View -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Daftar Pembelian
          </h3>
          <div class="card-tools">
            @if (! $showCreateForm)
            <button wire:click="create" class="btn btn-success btn-sm">
              <i class="fas fa-plus-circle"></i>
              Buat Pembelian
            </button>
            @endif
          </div>
        </div>
        <div class="card-body purchases-list-wrapper">
          <!-- Search Box -->
          <div class="row mb-3">
            <div class="col-md-12">
              <input type="text" wire:model.live="search" class="form-control"
                placeholder="Cari no invoice, supplier, produk..." />
            </div>
          </div>

          <!-- Table -->
          @if ($purchases->count() > 0)
          <!-- Purchases table (AdminLTE 3 compatible) -->
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover table-sm mb-0">
              <thead class="bg-light">
                <tr>
                  <th style="width: 40px">#</th>
                  <th>No Invoice</th>
                  <th>Tanggal</th>
                  <th>Supplier</th>
                  <th>Kategori</th>
                  <th>Subkategori</th>
                  <th>Produk</th>
                  <th style="width: 60px">Qty</th>
                  <th>Unit</th>
                  <th>Lokasi</th>
                  <th>Harga Beli</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th style="width: 100px">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($purchases as $index => $purchase)
                @forelse ($purchase->purchaseItems as $item)
                <tr>
                  @if ($loop->first)
                  <td class="text-center" rowspan="{{ $purchase->purchaseItems->count() }}">{{ $loop->parent->iteration
                    }}</td>
                  <td rowspan="{{ $purchase->purchaseItems->count() }}"><strong>{{ $purchase->no_invoice }}</strong>
                  </td>
                  <td rowspan="{{ $purchase->purchaseItems->count() }}">{{ $purchase->tanggal_pembelian->format('d/m/Y')
                    }}</td>
                  <td rowspan="{{ $purchase->purchaseItems->count() }}">{{ $purchase->supplier?->nama_supplier ?? '-' }}
                  </td>
                  @endif
                  <td>{{ $item->category?->nama_kategori ?? '-' }}</td>
                  <td>{{ $item->subcategory?->nama_subkategori ?? '-' }}</td>
                  <td>{{ $item->product?->nama_produk ?? '-' }}</td>
                  <td class="text-center">{{ $item->qty }}</td>
                  <td>{{ $item->unit?->nama_unit ?? '-' }}</td>
                  @if ($loop->first)
                  <td rowspan="{{ $purchase->purchaseItems->count() }}">
                    @if ($purchase->store_id)
                    <span class="badge badge-primary">
                      {{ $purchase->store?->nama_toko }}
                    </span>
                    @elseif ($purchase->warehouse_id)
                    <span class="badge badge-warning">
                      {{ $purchase->warehouse?->nama_gudang }}
                    </span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                  </td>
                  @endif
                  <td class="text-right">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                  <td class="text-right"><strong>Rp {{ number_format($item->total, 0, ',', '.') }}</strong></td>
                  <td>
                    @if ($item->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                    @elseif ($item->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                    @elseif ($item->status === 'hold')
                    <span class="badge badge-info">Hold / Keep</span>
                    @else
                    <span class="badge badge-danger">Cancelled</span>
                    @endif
                  </td>
                  @if ($loop->first)
                  <td class="actions text-right" rowspan="{{ $purchase->purchaseItems->count() }}">
                    <div class="btn-group" role="group" aria-label="Aksi">
                      <button wire:click="edit({{ $purchase->id }})" class="btn btn-info btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button wire:click="delete({{ $purchase->id }})" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                  @endif
                </tr>
                @empty
                <tr>
                  <td class="text-center">{{ $loop->parent->iteration }}</td>
                  <td><strong>{{ $purchase->no_invoice }}</strong></td>
                  <td>{{ $purchase->tanggal_pembelian->format('d/m/Y') }}</td>
                  <td>{{ $purchase->supplier?->nama_supplier ?? '-' }}</td>
                  <td colspan="6" class="text-muted text-center">Tidak ada item</td>
                  <td>
                    @if ($purchase->store_id)
                    <span class="badge badge-primary">
                      {{ $purchase->store?->nama_toko }}
                    </span>
                    @elseif ($purchase->warehouse_id)
                    <span class="badge badge-warning">
                      {{ $purchase->warehouse?->nama_gudang }}
                    </span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>-</td>
                  <td>
                    @if ($purchase->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                    @elseif ($purchase->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                    @else
                    <span class="badge badge-danger">Cancelled</span>
                    @endif
                  </td>
                  <td class="actions text-right">
                    <div class="btn-group" role="group" aria-label="Aksi">
                      <button wire:click="edit({{ $purchase->id }})" class="btn btn-info btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button wire:click="delete({{ $purchase->id }})" class="btn btn-danger btn-sm" title="Hapus">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforelse
                @endforeach
              </tbody>
            </table>
          </div>
          @else
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Belum ada data pembelian. Klik "Buat Pembelian" untuk membuat pembelian baru.
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah/Edit Supplier -->
  @if ($showSupplierModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog">
    <div class="modal-backdrop fade show"></div>
    <div class="modal-dialog modal-lg" role="document" style="z-index: 1050; position: relative">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle mr-2"></i>
            Tambah Pemasok Baru
          </h5>
          <button type="button" class="close" wire:click="closeSupplierModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form wire:submit.prevent="saveSupplier">
          <div class="modal-body">
            @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
              </button>
              <i class="icon fas fa-check"></i>
              {{ session('message') }}
            </div>
            @endif

            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
              </button>
              <i class="icon fas fa-exclamation"></i>
              {{ session('error') }}
            </div>
            @endif

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    <strong>
                      Kode Pemasok
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <input type="text" wire:model.defer="kode_supplier"
                    class="form-control @error('kode_supplier') is-invalid @enderror"
                    placeholder="Kode pemasok (e.g., SUP001)" />
                  @error('kode_supplier')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    <strong>
                      Nama Pemasok
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <input type="text" wire:model.defer="nama_supplier"
                    class="form-control @error('nama_supplier') is-invalid @enderror" placeholder="Nama pemasok" />
                  @error('nama_supplier')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Telepon</strong></label>
                  <input type="text" wire:model.defer="telepon"
                    class="form-control @error('telepon') is-invalid @enderror" placeholder="Nomor telepon" />
                  @error('telepon')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Email</strong></label>
                  <input type="email" wire:model.defer="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email pemasok" />
                  @error('email')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-group">
              <label><strong>Alamat</strong></label>
              <textarea wire:model.defer="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3"
                placeholder="Alamat pemasok..."></textarea>
              @error('alamat')
              <small class="text-danger d-block mt-1">{{ $message }}</small>
              @enderror
            </div>

            <div class="form-group">
              <label><strong>Keterangan</strong></label>
              <textarea wire:model.defer="supplier_keterangan" class="form-control" rows="2"
                placeholder="Keterangan tambahan (opsional)..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeSupplierModal">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save mr-1"></i>
              Simpan Pemasok
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <!-- Modal Tambah Owner/Supplier -->
  @if ($showOwnerModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog">
    <div class="modal-backdrop fade show"></div>
    <div class="modal-dialog modal-lg" role="document" style="z-index: 1050; position: relative">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h5 class="modal-title">
            <i class="fas fa-plus-circle mr-2"></i>
            Tambah Supplier/Owner Baru
          </h5>
          <button type="button" class="close" wire:click="closeOwnerModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form wire:submit.prevent="saveOwner">
          <div class="modal-body">
            <div class="form-group">
              <label>
                <strong>
                  Nama Supplier/Owner
                  <span class="text-danger">*</span>
                </strong>
              </label>
              <input type="text" wire:model.defer="new_owner_name"
                class="form-control @error('new_owner_name') is-invalid @enderror" placeholder="Nama supplier/owner" />
              @error('new_owner_name')
              <small class="text-danger d-block mt-1">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeOwnerModal">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save mr-1"></i>
              Simpan Owner
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  @push('scripts')
  {{-- DataTables JS moved to scripts stack --}}
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

  {{-- keep existing inline init code that depends on DataTables (no logic changes) --}}
  <script>
    $(document).ready(function () {
        // Initialize DataTable for purchase items only if we're editing
        if ($('#purchaseItemsTable').length) {
          var table = $('#purchaseItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url: '{{ $editingPurchaseId ? route('admin.purchases.items', $editingPurchaseId) : '' }}',
              data: function (d) {
                d.purchase_id = {{ json_encode($editingPurchaseId) }};
              },
            },
            columns: [
              { data: 'DT_RowIndex', orderable: false, searchable: false },
              { data: 'category_name' },
              { data: 'subcategory_name' },
              { data: 'product_name' },
              { data: 'qty' },
              { data: 'qty_gudang' },
              { data: 'unit_name' },
              { data: 'harga_beli' },
              { data: 'total_formatted' },
              { data: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['excel', 'pdf', 'print'],
          });

          // Handle edit button click
          $(document).on('click', '.edit-item', function () {
            var id = $(this).data('id');
            alert('Edit item ' + id + ' - Feature coming soon');
          });

          // Handle delete button click
          $(document).on('click', '.delete-item', function () {
            var id = $(this).data('id');
            if (confirm('Yakin ingin menghapus item ini?')) {
              alert('Delete item ' + id + ' - Feature coming soon');
            }
          });
        }
      });

    // Fungsi untuk cetak nota pembelian
    function printPurchaseReceipt(purchaseId) {
      if (!purchaseId) {
        alert('Silakan simpan pembelian terlebih dahulu');
        return;
      }

      // Buka halaman cetak di tab baru
      window.open('{{ url('/admin/purchases') }}/' + purchaseId + '/print', '_blank');
    }
  </script>
  @endpush

  <!-- Category Modal -->
  @if ($showCategoryModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog">
    <div class="modal-backdrop fade show"></div>
    <div class="modal-dialog" role="document" style="z-index: 1050; position: relative">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Tambah Kategori</h5>
          <button type="button" class="close" wire:click="closeCategoryModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form wire:submit.prevent="saveCategoryModal">
          <div class="modal-body">
            <div class="form-group">
              <label>
                Nama Kategori
                <span class="text-danger">*</span>
              </label>
              <input type="text" wire:model.defer="new_category_name" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeCategoryModal">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">Simpan Kategori</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <!-- Subcategory Modal -->
  @if ($showSubcategoryModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog">
    <div class="modal-backdrop fade show"></div>
    <div class="modal-dialog" role="document" style="z-index: 1050; position: relative">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Tambah Subkategori</h5>
          <button type="button" class="close" wire:click="closeSubcategoryModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form wire:submit.prevent="saveSubcategoryModal">
          <div class="modal-body">
            <div class="form-group">
              <label>
                Kategori
                <span class="text-danger">*</span>
              </label>
              <select wire:model="subcategory_modal_category_id" class="form-control">
                <option value="">-- pilih kategori --</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>
                Nama Subkategori
                <span class="text-danger">*</span>
              </label>
              <input type="text" wire:model.defer="new_subcategory_name" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeSubcategoryModal">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">Simpan Subkategori</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <!-- Product Modal -->
  @if ($showProductModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog">
    <div class="modal-backdrop fade show"></div>
    <div class="modal-dialog" role="document" style="z-index: 1050; position: relative">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title">Tambah Produk</h5>
          <button type="button" class="close" wire:click="closeProductModal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form wire:submit.prevent="saveProductModal">
          <div class="modal-body">
            <div class="form-group">
              <label>
                Kategori
                <span class="text-danger">*</span>
              </label>
              <select wire:model="product_modal_category_id" class="form-control">
                <option value="">-- pilih kategori --</option>
                @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Subkategori (opsional)</label>
              <select wire:model="product_modal_subcategory_id" class="form-control">
                <option value="">-- pilih subkategori --</option>
                @foreach ($subcategories as $sub)
                <option value="{{ $sub->id }}">{{ $sub->nama_subkategori }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>
                Nama Produk
                <span class="text-danger">*</span>
              </label>
              <input type="text" wire:model.defer="new_product_name" class="form-control" />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeProductModal">
              Batal
            </button>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif

  <script>
    function emitLW(eventName, ...params) {
      try {
        if (window.Livewire && typeof window.Livewire.emit === 'function') {
          window.Livewire.emit(eventName, ...params);
          return;
        }
        if (window.livewire && typeof window.livewire.emit === 'function') {
          window.livewire.emit(eventName, ...params);
          return;
        }

        // attempt to find component instance and call method directly as a fallback
        const root = document.querySelector('[wire\\:id], [wire-id]');
        const id = root ? root.getAttribute('wire:id') || root.getAttribute('wire-id') : null;
        if (id && window.Livewire && typeof window.Livewire.find === 'function') {
          const comp = window.Livewire.find(id);
          if (comp) {
            if (typeof comp.call === 'function') {
              // call the component method directly (openCategoryModal/openSubcategoryModal/openProductModal exist)
              comp.call(eventName, ...params);
              return;
            }
          }
        }
      } catch (err) {
        console.warn('emitLW error', err);
      }
      console.warn('Livewire emit not available for', eventName);
    }
    function handleCategoryChange(e, index) {
      const val = e.target.value;
      if (val === '__add_category__') {
        emitLW('openCategoryModal', index);
        e.target.value = '';
      }
    }

    function handleSubcategoryChange(e, index) {
      const val = e.target.value;
      if (val === '__add_subcategory__') {
        emitLW('openSubcategoryModal', index);
        e.target.value = '';
      }
    }

    function handleProductSelectChange(e, index) {
      const val = e.target.value;
      if (val === '__add_product__') {
        emitLW('openProductModal', index);
        e.target.value = '';
      }
    }

    function handleProductSearchChange(e, index) {
      const val = e.target.value;
      if (val === '__add_product__') {
        emitLW('openProductModal', index);
        e.target.value = '';
      }
    }

    // autofocus newly added batch input when server dispatches 'batch-added'
    window.addEventListener('batch-added', function (e) {
      try {
        const d = e.detail || e;
        let itemIndex = null;
        let batchIndex = null;
        if (Array.isArray(d) && d[0]) {
          itemIndex = d[0].itemIndex ?? d[0].item_index ?? null;
          batchIndex = d[0].batchIndex ?? d[0].batch_index ?? null;
        } else if (typeof d === 'object') {
          itemIndex = d.itemIndex ?? d.item_index ?? null;
          batchIndex = d.batchIndex ?? d.batch_index ?? null;
        }
        if (itemIndex === null || batchIndex === null) return;
        const id = 'batch-name-' + itemIndex + '-' + batchIndex;
        const el = document.getElementById(id) || document.querySelector('[aria-label="' + id + '"]');
        if (el) {
          // small timeout to wait for Livewire DOM patch
          setTimeout(() => el.focus(), 60);
        }
      } catch (err) {
        console.warn('batch-added handler error', err);
      }
    });
  </script>

  <script>
    document.addEventListener('click', function (event) {
      const batchToggle = event.target.closest('.batch-toggle-btn');
      if (batchToggle) {
        const batchIndex = batchToggle.getAttribute('data-batch-index');
        const batchRow = document.getElementById('batch-' + batchIndex);
        if (batchRow) {
          const isHidden = batchRow.style.display === 'none';
          batchRow.style.display = isHidden ? '' : 'none';
          batchToggle.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        }
      }
    });
  </script>
</div>
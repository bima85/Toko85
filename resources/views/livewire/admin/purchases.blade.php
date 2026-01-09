@push('styles')
  <style>
    .purchases-list-wrapper .card {
      border-radius: 10px;
      border-color: #e9ecef;
    }

    .purchases-list-wrapper .card-header {
      padding: 0.75rem 1rem;
    }

    .purchases-list-wrapper .card-body {
      padding: 1rem 1.25rem;
    }

    .purchases-list-wrapper .input-group > .form-control,
    .purchases-list-wrapper .input-group > .input-group-append > .btn {
      border-radius: 6px;
    }

    .purchases-list-wrapper .badge {
      font-weight: 600;
    }

    .purchases-table--stack .table,
    .purchases-table--cards .table {
      margin-bottom: 0;
    }

    .purchases-table--cards .actions {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: flex-start;
    }

    @media (max-width: 991.98px) {
      .purchases-list-wrapper h2 {
        font-size: 1.25rem;
        line-height: 1.4;
      }

      .purchases-list-wrapper .card-body {
        padding: 0.85rem 0.95rem;
      }

      .purchases-table--stack table thead,
      .purchases-table--cards table thead {
        display: none;
      }

      .purchases-table--stack table tbody tr,
      .purchases-table--cards table tbody tr {
        display: block;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        margin-bottom: 12px;
        background: #fff;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
      }

      .purchases-table--stack table tbody tr td,
      .purchases-table--cards table tbody tr td {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        padding: 10px 12px;
        border: 0;
        border-bottom: 1px solid #f1f3f5;
        width: 100%;
      }

      .purchases-table--stack table tbody tr td:last-child,
      .purchases-table--cards table tbody tr td:last-child {
        border-bottom: 0;
      }

      .purchases-table--stack table tbody tr td::before,
      .purchases-table--cards table tbody tr td::before {
        content: attr(data-label);
        font-weight: 700;
        color: #495057;
        min-width: 110px;
        flex-shrink: 0;
      }

      .purchases-table--cards .actions {
        flex-direction: column;
        align-items: stretch;
      }

      .purchases-table--cards .actions .btn {
        width: 100%;
        text-align: center;
        margin: 0 0 6px 0;
      }

      .purchases-table--cards .actions .btn:last-child {
        margin-bottom: 0;
      }
    }

    @media (max-width: 575.98px) {
      .purchases-list-wrapper .card-header,
      .purchases-list-wrapper .card-body {
        padding: 0.75rem;
      }

      .purchases-table--stack table tbody tr td,
      .purchases-table--cards table tbody tr td {
        padding: 10px;
      }
    }
  </style>
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
                  <input
                    wire:model="tanggal_pembelian"
                    type="date"
                    class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                  />
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
                    <input
                      type="text"
                      list="owners-datalist"
                      wire:model="ownerFilter"
                      wire:change="ownerChanged($event.target.value)"
                      class="form-control"
                      placeholder="Cari Supplier/Owner..."
                    />
                    <datalist id="owners-datalist">
                      @foreach ($owners as $owner)
                        <option value="{{ $owner }}"></option>
                      @endforeach
                    </datalist>
                    <div class="input-group-append">
                      <button
                        class="btn btn-primary btn-sm"
                        type="button"
                        wire:click="openOwnerModal"
                        title="Tambah Supplier/Owner Baru"
                      >
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
                    <select
                      wire:model="supplier_id"
                      class="form-control @error('supplier_id') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Perusahaan --</option>
                      @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama_supplier }}</option>
                      @endforeach
                    </select>
                    <div class="input-group-append">
                      <button
                        class="btn btn-primary btn-sm"
                        type="button"
                        wire:click="openSupplierModal"
                        title="Tambah Supplier Baru"
                      >
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
                  <select
                    wire:change="selectLocation($event.target.value)"
                    class="form-control @error('store_id') is-invalid @enderror @error('warehouse_id') is-invalid @enderror"
                  >
                    <option value="">-- Tidak Ada --</option>
                    <optgroup label="Toko">
                      @foreach ($stores as $s)
                        <option
                          value="store:{{ $s->id }}"
                          @if($store_id == $s->id) selected @endif
                        >
                          {{ $s->nama_toko }}
                        </option>
                      @endforeach
                    </optgroup>
                    <optgroup label="Gudang">
                      @foreach ($warehouses as $wh)
                        <option
                          value="warehouse:{{ $wh->id }}"
                          @if($warehouse_id == $wh->id) selected @endif
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
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    <strong>
                      Status
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <select
                    wire:model="status"
                    class="form-control @error('status') is-invalid @enderror"
                  >
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                  @error('status')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-group m-2">
              <label><strong>Keterangan</strong></label>
              <textarea
                wire:model="keterangan"
                class="form-control"
                rows="2"
                placeholder="Catatan pembelian..."
              ></textarea>
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
                    <input
                      type="checkbox"
                      class="custom-control-input"
                      id="batchToggleSwitch"
                      wire:model.live="batch_enabled"
                    />
                    <label class="custom-control-label" for="batchToggleSwitch">
                      Aktifkan Batch
                    </label>
                  </div>
                  <small class="text-muted">
                    Jika aktif, qty toko dijumlahkan dari batch di kolom Batch.
                  </small>
                </div>
                <div class="table-responsive purchases-table--stack">
                  <table
                    id="purchaseItemsTable"
                    class="table table-sm table-bordered table-hover"
                    style="width: 100%"
                  >
                    <thead class="bg-light">
                      <tr>
                        <th style="width: 2%">#</th>
                        <th style="width: 8.5%">Kategori</th>
                        <th style="width: 10%">Subkategori</th>
                        <th style="width: 15%">Produk</th>
                        <th style="width: 8%; vertical-align: middle">Batch</th>
                        <th style="width: 25%">Tujuan & Qty</th>
                        <th style="width: 7%">Unit</th>
                        <th style="width: 10%">Harga Beli</th>
                        <th style="width: 10%">Total</th>
                        <th style="width: 8%">Hapus</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($purchaseItems as $index => $item)
                        <tr wire:key="item-{{ $index }}">
                          <td class="text-center" data-label="#">{{ $index + 1 }}</td>
                          <td data-label="Kategori">
                            <select
                              wire:model="purchaseItems.{{ $index }}.category_id"
                              wire:change="$refresh"
                              class="form-control form-control-sm"
                            >
                              <option value="">--</option>
                              @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                  {{ $category->nama_kategori }}
                                </option>
                              @endforeach

                              <option
                                value="__add_category__"
                                style="background-color: #28a745; color: white; font-weight: bold"
                              >
                                ✚ Tambah Kategori
                              </option>
                            </select>
                          </td>
                          <td data-label="Subkategori">
                            <select
                              wire:model="purchaseItems.{{ $index }}.subcategory_id"
                              class="form-control form-control-sm"
                            >
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

                              <option
                                value="__add_subcategory__"
                                style="background-color: #28a745; color: white; font-weight: bold"
                              >
                                ✚ Tambah Subkategori
                              </option>
                            </select>
                          </td>
                          <td data-label="Produk">
                            <input
                              type="text"
                              wire:model.live="purchaseItems.{{ $index }}.product_search"
                              wire:change="updateProductFromSearch({{ $index }})"
                              list="products_{{ $index }}"
                              onchange="handleProductSearchChange(event, {{ $index }})"
                              class="form-control form-control-sm"
                              placeholder="Cari produk..."
                              autocomplete="off"
                            />
                            <datalist id="products_{{ $index }}">
                              @foreach ($products as $prod)
                                <option
                                  value="{{ $prod->nama_produk }}"
                                  data-id="{{ $prod->id }}"
                                >
                                  {{ $prod->nama_produk }}
                                </option>
                              @endforeach

                              <option
                                value="__add_product__"
                                style="background-color: #28a745; color: white; font-weight: bold"
                              >
                                ✚ Tambah Produk Baru
                              </option>
                            </datalist>
                            @if ($item['product_id'] === '')
                              <small class="text-muted d-block mt-1">
                                Ketik nama produk untuk mencari
                              </small>
                            @endif
                          </td>
                          <td
                            data-label="Batch"
                            style="padding: 8px; vertical-align: top; width: 210px"
                          >
                            @if ($this->batch_enabled)
                              <div style="display: flex; flex-direction: column; gap: 6px">
                                <div style="font-size: 0.8rem; font-weight: 600; color: #444">
                                  Batch & Qty
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px">
                                  @foreach ($item['batches'] ?? [] as $bIndex => $batch)
                                    <div
                                      style="
                                        display: flex;
                                        align-items: center;
                                        gap: 4px;
                                        background: #f8f9fa;
                                        border: 1px solid #e5e7eb;
                                        border-radius: 6px;
                                        padding: 4px 6px;
                                      "
                                      wire:key="batch-{{ $index }}-{{ $bIndex }}"
                                    >
                                      <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        placeholder="T1"
                                        value="{{ $batch['name'] ?? '' }}"
                                        wire:input="updateBatchField({{ $index }}, {{ $bIndex }}, 'name', $event.target.value)"
                                        style="width: 56px"
                                      />
                                      <input
                                        type="number"
                                        min="0"
                                        class="form-control form-control-sm"
                                        placeholder="0"
                                        value="{{ $batch['qty'] ?? 0 }}"
                                        wire:input="updateBatchField({{ $index }}, {{ $bIndex }}, 'qty', $event.target.value)"
                                        style="width: 58px"
                                      />
                                      <button
                                        type="button"
                                        class="btn btn-outline-danger btn-xs"
                                        style="padding: 4px 6px"
                                        wire:click="removeBatchRow({{ $index }}, {{ $bIndex }})"
                                        title="Hapus batch"
                                      >
                                        <i class="fas fa-times"></i>
                                      </button>
                                    </div>
                                  @endforeach
                                </div>

                                <div
                                  style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                  "
                                >
                                  <button
                                    type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    wire:click="addBatchRow({{ $index }})"
                                  >
                                    <i class="fas fa-plus-circle mr-1"></i>
                                    Tambah
                                  </button>
                                  <small class="text-muted">Qty toko dijumlahkan</small>
                                </div>
                              </div>
                            @endif
                          </td>
                          <td data-label="Tujuan & Qty" style="padding: 6px">
                            <div style="display: flex; gap: 6px; align-items: flex-end">
                              <div style="flex: 1.5">
                                <select
                                  wire:model.live="purchaseItems.{{ $index }}.destination_type"
                                  class="form-control form-control-sm"
                                >
                                  <option value="gudang">Gudang</option>
                                  <option value="toko">Toko</option>
                                  <option value="both">Gudang & Toko</option>
                                </select>
                              </div>

                              @if ($item['destination_type'] === 'gudang' || $item['destination_type'] === 'both')
                                <div style="flex: 1; display: flex; flex-direction: column">
                                  <small
                                    style="
                                      font-size: 0.7rem;
                                      font-weight: bold;
                                      margin-bottom: 2px;
                                      color: #333;
                                    "
                                  >
                                    Gudang
                                  </small>
                                  <input
                                    wire:model="purchaseItems.{{ $index }}.qty_gudang"
                                    wire:change="updateTotal({{ $index }})"
                                    type="number"
                                    min="0"
                                    class="form-control form-control-sm"
                                    placeholder="0"
                                  />
                                </div>
                              @endif

                              @if ($item['destination_type'] === 'toko' || $item['destination_type'] === 'both')
                                <div style="flex: 1; display: flex; flex-direction: column">
                                  <small
                                    style="
                                      font-size: 0.7rem;
                                      font-weight: bold;
                                      margin-bottom: 2px;
                                      color: #333;
                                    "
                                  >
                                    Toko
                                  </small>
                                  <input
                                    wire:model="purchaseItems.{{ $index }}.qty"
                                    wire:change="updateTotal({{ $index }})"
                                    type="number"
                                    min="0"
                                    class="form-control form-control-sm"
                                    placeholder="0"
                                    @if ($this->batch_enabled) readonly @endif
                                  />
                                  @if ($this->batch_enabled)
                                    <small class="text-muted">
                                      Otomatis dari batch di kolom Batch
                                    </small>
                                  @endif
                                </div>
                              @endif
                            </div>
                          </td>
                          <td data-label="Unit">
                            <select
                              wire:model="purchaseItems.{{ $index }}.unit_id"
                              class="form-control form-control-sm"
                            >
                              <option value="">--</option>
                              @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                              @endforeach
                            </select>
                          </td>
                          <td data-label="Harga Beli">
                            <input
                              wire:model="purchaseItems.{{ $index }}.harga_beli"
                              wire:change="updateTotal({{ $index }})"
                              type="number"
                              step="0.01"
                              min="0"
                              class="form-control form-control-sm"
                              placeholder="0"
                            />
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
                          <td class="text-center" data-label="Hapus">
                            <button
                              wire:click="removeItem({{ $index }})"
                              type="button"
                              class="btn btn-danger btn-sm"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                      <tr>
                        <td colspan="9" class="text-right">TOTAL:</td>
                        <td colspan="2" class="text-right text-success">
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
        <div class="card-body">
          <!-- Search Box -->
          <div class="row mb-3">
            <div class="col-md-12">
              <input
                type="text"
                wire:model.live="search"
                class="form-control"
                placeholder="Cari no invoice, supplier, produk..."
              />
            </div>
          </div>

          <!-- Table -->
          @if ($purchases->count() > 0)
            <div class="table-responsive purchases-table--cards">
              <table class="table table-sm table-striped table-hover">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 12%">No Invoice</th>
                    <th style="width: 12%">Tanggal</th>
                    <th style="width: 18%">Supplier</th>
                    <th style="width: 12%">Lokasi</th>
                    <th style="width: 15%">Total Item</th>
                    <th style="width: 12%">Status</th>
                    <th style="width: 14%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($purchases as $index => $purchase)
                    <tr>
                      <td class="text-center" data-label="#">{{ $loop->iteration }}</td>
                      <td data-label="No Invoice"><strong>{{ $purchase->no_invoice }}</strong></td>
                      <td data-label="Tanggal">
                        {{ $purchase->tanggal_pembelian->format('d/m/Y') }}
                      </td>
                      <td data-label="Supplier">
                        {{ $purchase->supplier?->nama_supplier ?? '-' }}
                      </td>
                      <td data-label="Lokasi">
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
                      <td data-label="Total Item">
                        <span class="badge badge-info">
                          {{ $purchase->purchaseItems->count() }} item
                        </span>
                      </td>
                      <td data-label="Status">
                        @if ($purchase->status === 'completed')
                          <span class="badge badge-success">Completed</span>
                        @elseif ($purchase->status === 'pending')
                          <span class="badge badge-warning">Pending</span>
                        @else
                          <span class="badge badge-danger">Cancelled</span>
                        @endif
                      </td>
                      <td class="actions" data-label="Aksi">
                        <button
                          wire:click="edit({{ $purchase->id }})"
                          class="btn btn-info btn-sm mr-1"
                          title="Edit"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button
                          wire:click="delete({{ $purchase->id }})"
                          wire:confirm="Yakin ingin menghapus pembelian ini?"
                          class="btn btn-danger btn-sm"
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
                    <input
                      type="text"
                      wire:model.defer="kode_supplier"
                      class="form-control @error('kode_supplier') is-invalid @enderror"
                      placeholder="Kode pemasok (e.g., SUP001)"
                    />
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
                    <input
                      type="text"
                      wire:model.defer="nama_supplier"
                      class="form-control @error('nama_supplier') is-invalid @enderror"
                      placeholder="Nama pemasok"
                    />
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
                    <input
                      type="text"
                      wire:model.defer="telepon"
                      class="form-control @error('telepon') is-invalid @enderror"
                      placeholder="Nomor telepon"
                    />
                    @error('telepon')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label><strong>Email</strong></label>
                    <input
                      type="email"
                      wire:model.defer="email"
                      class="form-control @error('email') is-invalid @enderror"
                      placeholder="Email pemasok"
                    />
                    @error('email')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label><strong>Alamat</strong></label>
                <textarea
                  wire:model.defer="alamat"
                  class="form-control @error('alamat') is-invalid @enderror"
                  rows="3"
                  placeholder="Alamat pemasok..."
                ></textarea>
                @error('alamat')
                  <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
              </div>

              <div class="form-group">
                <label><strong>Keterangan</strong></label>
                <textarea
                  wire:model.defer="supplier_keterangan"
                  class="form-control"
                  rows="2"
                  placeholder="Keterangan tambahan (opsional)..."
                ></textarea>
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
                <input
                  type="text"
                  wire:model.defer="new_owner_name"
                  class="form-control @error('new_owner_name') is-invalid @enderror"
                  placeholder="Nama supplier/owner"
                />
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

  <!-- DataTables JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>

  @if ($editingPurchaseId)
    <script>
      $(document).ready(function () {
        // Initialize DataTable for purchase items only if we're editing
        if ($('#purchaseItemsTable').length) {
          var table = $('#purchaseItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
              url: '{{ route('admin.purchases.items', $editingPurchaseId) }}',
              data: function (d) {
                d.purchase_id = {{ $editingPurchaseId }};
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
    </script>
  @endif

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
            <button
              type="button"
              class="close"
              wire:click="closeSubcategoryModal"
              aria-label="Close"
            >
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
  </script>

  <style>
    /* Minimal styling adjustments for product select to match other fields */
    .form-control.form-control-sm {
      font-size: 0.875rem;
      line-height: 1.2;
    }

    /* Batch details row styling */
    [id^='batch-'] {
      transition: all 0.3s ease;
    }

    .batch-toggle-btn i {
      transition: transform 0.3s ease;
    }

    .batch-toggle-btn[aria-expanded='true'] i {
      transform: rotate(180deg);
    }
  </style>

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

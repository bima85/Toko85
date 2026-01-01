<div>
  <style>
    /* Item Penjualan - Responsive Design */
    .sale-items-container {
      margin-top: 2rem;
    }

    /* Desktop View - Table */
    @media (min-width: 769px) {
      .sale-items-desktop {
        display: table;
        width: 100%;
      }

      .sale-items-mobile {
        display: none;
      }

      .sale-items-table {
        width: 100%;
      }

      .sale-items-table thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
      }

      .sale-items-table tbody td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
      }

      .sale-items-table .form-control-sm {
        font-size: 0.9rem;
      }
    }

    /* Mobile View - Cards */
    @media (max-width: 768px) {
      .sale-items-desktop {
        display: none;
      }

      .sale-items-mobile {
        display: block;
      }

      .sale-item-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
      }

      .sale-item-card.active {
        border-color: #007bff;
        background: #f0f7ff;
      }

      .sale-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e0e0e0;
      }

      .sale-item-no {
        font-size: 0.9rem;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
      }

      .sale-item-delete {
        margin: 0;
      }

      .sale-item-row {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 0.5rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
      }

      .sale-item-row:last-child {
        margin-bottom: 0;
      }

      .sale-item-label {
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .sale-item-value {
        display: flex;
        align-items: center;
      }

      .sale-item-row .form-control-sm,
      .sale-item-row .form-select-sm {
        font-size: 0.9rem;
        padding: 0.4rem 0.5rem;
      }

      .sale-item-row input[type='text'],
      .sale-item-row input[type='number'],
      .sale-item-row select {
        max-width: 100%;
      }

      .sale-item-total {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.25rem;
        text-align: right;
        font-weight: 600;
        color: #28a745;
        font-size: 1rem;
      }

      .sale-items-summary {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 1rem;
      }

      .sale-items-summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
      }

      .sale-items-summary-row:last-child {
        border-top: 2px solid #e0e0e0;
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        font-weight: 600;
        font-size: 1.1rem;
      }
    }

    /* Shared Styles */
    .sale-items-label {
      font-weight: 600;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }
  </style>

  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('success') }}
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
        <i class="fas fa-shopping-bag mr-2"></i>
        Manajemen Penjualan
      </h2>
      <small class="text-muted">Kelola data penjualan kepada pelanggan</small>
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
              {{ $editingSaleId ? 'Edit Penjualan' : 'Buat Penjualan Baru' }}
            </h3>
            <div class="card-tools">
              <button wire:click="cancel" type="button" class="btn btn-sm btn-secondary">
                <i class="fas fa-times"></i>
                Batal
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- Informasi Penjualan -->
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
                      Tanggal Penjualan
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <input
                    wire:model="tanggal_penjualan"
                    type="date"
                    class="form-control @error('tanggal_penjualan') is-invalid @enderror"
                  />
                  @error('tanggal_penjualan')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    <strong>
                      Pelanggan
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <div class="input-group">
                    <select
                      wire:model.live="customer_id"
                      class="form-control @error('customer_id') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Pelanggan --</option>
                      @foreach ($customers as $cust)
                        <option value="{{ $cust->id }}">{{ $cust->nama_pelanggan }}</option>
                      @endforeach
                    </select>
                    <div class="input-group-append">
                      <button
                        type="button"
                        class="btn btn-outline-success"
                        wire:click.prevent="showCreateCustomerModal = true"
                        title="Tambah Pelanggan"
                      >
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  @error('customer_id')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    <strong>
                      Ambil Stok Dari
                      <span class="text-danger">*</span>
                    </strong>
                  </label>
                  <div class="d-flex gap-3 mb-2">
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        wire:model.live="location_source"
                        value="toko"
                        id="locationToko"
                      />
                      <label class="form-check-label" for="locationToko">
                        <i class="fas fa-store text-info"></i>
                        Toko
                      </label>
                    </div>
                    <div class="form-check">
                      <input
                        class="form-check-input"
                        type="radio"
                        wire:model.live="location_source"
                        value="gudang"
                        id="locationGudang"
                      />
                      <label class="form-check-label" for="locationGudang">
                        <i class="fas fa-warehouse text-success"></i>
                        Gudang
                      </label>
                    </div>
                  </div>

                  @if ($location_source === 'toko')
                    <select
                      wire:model.live="store_id"
                      class="form-control @error('store_id') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Toko --</option>
                      @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                      @endforeach
                    </select>
                    @error('store_id')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  @else
                    <select
                      wire:model.live="warehouse_id"
                      class="form-control @error('warehouse_id') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Gudang --</option>
                      @foreach ($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->nama_gudang }}</option>
                      @endforeach
                    </select>
                    @error('warehouse_id')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  @endif
                </div>
              </div>
            </div>

            <div class="row">
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
                    <option value="hold">Hold / Keep (Tahan Stok)</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                  @error('status')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-group">
              <label><strong>Keterangan</strong></label>
              <textarea
                wire:model="keterangan"
                class="form-control"
                rows="2"
                placeholder="Catatan penjualan..."
              ></textarea>
              @error('keterangan')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
              @enderror
            </div>

            <!-- Inline Add Customer Modal -->
            @if ($showCreateCustomerModal)
              <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Tambah Pelanggan</h5>
                      <button
                        type="button"
                        class="close"
                        wire:click="$set('showCreateCustomerModal', false)"
                      >
                        <span>&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      <div class="form-group">
                        <label>Nama Pelanggan</label>
                        <input wire:model.defer="new_customer_nama" class="form-control" />
                      </div>
                      <div class="form-group">
                        <label>Telepon</label>
                        <input wire:model.defer="new_customer_telepon" class="form-control" />
                      </div>
                      <div class="form-group">
                        <label>Email</label>
                        <input wire:model.defer="new_customer_email" class="form-control" />
                      </div>
                      <div class="form-group">
                        <label>Alamat</label>
                        <textarea
                          wire:model.defer="new_customer_alamat"
                          class="form-control"
                          rows="2"
                        ></textarea>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button class="btn btn-success" wire:click.prevent="saveNewCustomer">
                        Simpan
                      </button>
                      <button
                        class="btn btn-secondary"
                        wire:click="$set('showCreateCustomerModal', false)"
                      >
                        Batal
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            @endif

            <!-- Items Table/Cards -->
            @if (count($saleItems) > 0)
              <div class="sale-items-container">
                <label class="sale-items-label">
                  <strong>Item Penjualan</strong>
                </label>

                <!-- Desktop View -->
                <div class="sale-items-desktop">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered sale-items-table">
                      <thead>
                        <tr>
                          <th style="width: 5%">#</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 10%">Subkategori</th>
                          <th style="width: 12%">Produk</th>
                          <th style="width: 12%">Batch/Tumpukan</th>
                          <th style="width: 7%">Qty</th>
                          <th style="width: 8%">Unit</th>
                          <th style="width: 12%">Harga Jual</th>
                          <th style="width: 10%">Total</th>
                          <th style="width: 5%">Hapus</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($saleItems as $index => $item)
                          <tr wire:key="item-{{ $index }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                              <select
                                wire:model="saleItems.{{ $index }}.category_id"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @foreach ($categories as $cat)
                                  <option value="{{ $cat->id }}">
                                    {{ $cat->nama_kategori }}
                                  </option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select
                                wire:model="saleItems.{{ $index }}.subcategory_id"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @foreach ($subcategories as $sub)
                                  <option value="{{ $sub->id }}">
                                    {{ $sub->nama_subkategori }}
                                  </option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <div class="input-group input-group-sm">
                                <input
                                  type="text"
                                  list="productList{{ $index }}"
                                  wire:model.live="saleItems.{{ $index }}.product_search"
                                  data-item-index="{{ $index }}"
                                  class="form-control form-control-sm product-search-input"
                                  placeholder="Cari produk..."
                                  autocomplete="off"
                                />
                                <datalist id="productList{{ $index }}">
                                  @foreach ($products as $prod)
                                    <option
                                      value="[{{ $prod->kode_produk }}] {{ $prod->nama_produk }}"
                                      data-product-id="{{ $prod->id }}"
                                    ></option>
                                  @endforeach
                                </datalist>
                              </div>
                              @if ($item['product_id'])
                                <small class="text-success d-block mt-1">
                                  <i class="fas fa-check-circle"></i>
                                  Dipilih
                                </small>
                              @endif
                            </td>
                            <td>
                              <div class="input-group input-group-sm">
                                <select
                                  wire:model.live="saleItems.{{ $index }}.batch_id"
                                  class="form-control form-control-sm"
                                >
                                  <option value="">-- Pilih --</option>
                                  @php
                                    $batches = $this->getAvailableBatches($index);
                                  @endphp

                                  @if ($batches->count() === 0)
                                    <option disabled selected>Pilih produk</option>
                                  @else
                                    @foreach ($batches as $batch)
                                      <option
                                        value="{{ $batch->id }}"
                                        @if($item['batch_id'] == $batch->id) selected @endif
                                      >
                                        {{ $batch->nama_tumpukan }} ({{ $batch->qty }})
                                      </option>
                                    @endforeach
                                  @endif
                                </select>
                              </div>
                            </td>
                            <td>
                              <input
                                wire:model="saleItems.{{ $index }}.qty"
                                wire:change="updateTotal({{ $index }})"
                                type="number"
                                min="1"
                                class="form-control form-control-sm text-center"
                              />
                            </td>
                            <td>
                              <select
                                wire:model="saleItems.{{ $index }}.unit_id"
                                wire:change="updateTotal({{ $index }})"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @foreach ($units as $unit)
                                  <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <input
                                wire:model="saleItems.{{ $index }}.harga_jual"
                                wire:change="updateTotal({{ $index }})"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control form-control-sm text-right"
                              />
                            </td>
                            <td class="text-right font-weight-bold">
                              Rp {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
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
                          <td></td>
                          <td colspan="7" class="text-right pr-3">Kuli:</td>
                          <td style="padding: 4px 0; width: 150px">
                            <div class="input-group input-group-sm">
                              <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                              </div>
                              <input
                                id="kuliInput"
                                wire:model.defer="kuli"
                                type="text"
                                class="form-control form-control-sm text-right"
                                placeholder="0"
                              />
                            </div>
                          </td>
                          <td colspan="2"></td>
                        </tr>
                        <tr>
                          <td colspan="7" class="text-right pr-3">TOTAL:</td>
                          <td colspan="2" class="text-right text-success">
                            Rp
                            {{ number_format(array_sum(array_column($saleItems, 'total')) + ($kuli ?? 0), 0, ',', '.') }}
                          </td>
                          <td></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>

                <!-- Mobile View -->
                <div class="sale-items-mobile">
                  @foreach ($saleItems as $index => $item)
                    <div class="sale-item-card" wire:key="mobile-item-{{ $index }}">
                      <div class="sale-item-header">
                        <span class="sale-item-no">Item #{{ $index + 1 }}</span>
                        <button
                          wire:click="removeItem({{ $index }})"
                          type="button"
                          class="btn btn-danger btn-sm sale-item-delete"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>

                      <!-- Kategori -->
                      <div class="sale-item-row">
                        <div class="sale-item-label">Kategori</div>
                        <div class="sale-item-value">
                          <select
                            wire:model="saleItems.{{ $index }}.category_id"
                            class="form-control form-control-sm form-select-sm"
                          >
                            <option value="">-- Pilih --</option>
                            @foreach ($categories as $cat)
                              <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Subkategori -->
                      <div class="sale-item-row">
                        <div class="sale-item-label">Subkategori</div>
                        <div class="sale-item-value">
                          <select
                            wire:model="saleItems.{{ $index }}.subcategory_id"
                            class="form-control form-control-sm form-select-sm"
                          >
                            <option value="">-- Pilih --</option>
                            @foreach ($subcategories as $sub)
                              <option value="{{ $sub->id }}">
                                {{ $sub->nama_subkategori }}
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Produk -->
                      <div class="sale-item-row">
                        <div class="sale-item-label">Produk</div>
                        <div class="sale-item-value">
                          <input
                            type="text"
                            list="productListMobile{{ $index }}"
                            wire:model.live="saleItems.{{ $index }}.product_search"
                            class="form-control form-control-sm"
                            placeholder="Cari produk..."
                            autocomplete="off"
                          />
                          <datalist id="productListMobile{{ $index }}">
                            @foreach ($products as $prod)
                              <option
                                value="[{{ $prod->kode_produk }}] {{ $prod->nama_produk }}"
                                data-product-id="{{ $prod->id }}"
                              ></option>
                            @endforeach
                          </datalist>
                        </div>
                      </div>
                      @if ($item['product_id'])
                        <small class="text-success">
                          <i class="fas fa-check-circle"></i>
                          Produk dipilih
                        </small>
                      @endif

                      <!-- Batch -->
                      <div class="sale-item-row">
                        <div class="sale-item-label">Batch</div>
                        <div class="sale-item-value">
                          <select
                            wire:model.live="saleItems.{{ $index }}.batch_id"
                            class="form-control form-control-sm form-select-sm"
                          >
                            <option value="">-- Pilih --</option>
                            @php
                              $batches = $this->getAvailableBatches($index);
                            @endphp

                            @foreach ($batches as $batch)
                              <option
                                value="{{ $batch->id }}"
                                @if($item['batch_id'] == $batch->id) selected @endif
                              >
                                {{ $batch->nama_tumpukan }} ({{ $batch->qty }})
                              </option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Qty & Unit (Row) -->
                      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                        <div class="sale-item-row" style="margin-bottom: 0; grid-column: 1 / 2">
                          <div class="sale-item-label">Qty</div>
                          <div class="sale-item-value">
                            <input
                              wire:model="saleItems.{{ $index }}.qty"
                              wire:change="updateTotal({{ $index }})"
                              type="number"
                              min="1"
                              class="form-control form-control-sm text-center"
                            />
                          </div>
                        </div>
                        <div class="sale-item-row" style="margin-bottom: 0; grid-column: 2 / 3">
                          <div class="sale-item-label">Unit</div>
                          <div class="sale-item-value">
                            <select
                              wire:model="saleItems.{{ $index }}.unit_id"
                              wire:change="updateTotal({{ $index }})"
                              class="form-control form-control-sm form-select-sm"
                            >
                              <option value="">--</option>
                              @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                              @endforeach
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- Harga Jual -->
                      <div class="sale-item-row mt-3">
                        <div class="sale-item-label">Harga Jual</div>
                        <div class="sale-item-value">
                          <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                              <span class="input-group-text">Rp</span>
                            </div>
                            <input
                              wire:model="saleItems.{{ $index }}.harga_jual"
                              wire:change="updateTotal({{ $index }})"
                              type="number"
                              step="0.01"
                              min="0"
                              class="form-control form-control-sm text-right"
                            />
                          </div>
                        </div>
                      </div>

                      <!-- Total -->
                      <div class="sale-item-total">
                        Total: Rp {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                      </div>
                    </div>
                  @endforeach

                  <!-- Summary for Mobile -->
                  <div class="sale-items-summary">
                    <div class="sale-items-summary-row">
                      <span>Kuli:</span>
                      <div class="input-group input-group-sm" style="width: 120px">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Rp</span>
                        </div>
                        <input
                          wire:model.defer="kuli"
                          type="text"
                          class="form-control form-control-sm text-right"
                          placeholder="0"
                        />
                      </div>
                    </div>
                    <div class="sale-items-summary-row">
                      <span>TOTAL:</span>
                      <span class="text-success">
                        Rp
                        {{ number_format(array_sum(array_column($saleItems, 'total')) + ($kuli ?? 0), 0, ',', '.') }}
                      </span>
                    </div>
                  </div>
                </div>

                @error('saleItems')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>
            @else
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Belum ada item. Klik "Tambah Item" untuk menambah item penjualan.
              </div>
            @endif
          </div>
          <div class="card-footer">
            <button wire:click="addItem" type="button" class="btn btn-info btn-sm mr-2">
              <i class="fas fa-plus"></i>
              Tambah Item
            </button>
            <div class="float-right">
              <button wire:click="cancel" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-times"></i>
                Batal
              </button>
              <button wire:click="save" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i>
                Simpan Penjualan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- List View -->
  @endif

  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>
            Daftar Penjualan
          </h3>
          <div class="card-tools">
            @if (! $showCreateForm)
              <button wire:click="create" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle"></i>
                Buat Penjualan
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
                placeholder="Cari no invoice, nama pelanggan..."
              />
            </div>
          </div>

          <!-- Table -->
          @if ($sales->count() > 0)
            <div class="table-responsive">
              <table class="table table-sm table-striped table-hover">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 12%">No Invoice</th>
                    <th style="width: 10%">Tanggal</th>
                    <th style="width: 14%">Pelanggan</th>
                    <th style="width: 12%">Owner</th>
                    <th style="width: 10%">Lokasi</th>
                    <th style="width: 8%">Total Item</th>
                    <th style="width: 12%" class="text-right">Total Amount</th>
                    <th style="width: 8%">Status</th>
                    <th style="width: 14%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $index => $sale)
                    <tr>
                      <td class="text-center">{{ $loop->iteration }}</td>
                      <td><strong>{{ $sale->no_invoice }}</strong></td>
                      <td>{{ $sale->tanggal_penjualan?->format('d/m/Y') ?? '-' }}</td>
                      <td>{{ $sale->customer?->nama_pelanggan ?? '-' }}</td>
                      <td>
                        @php
                          $owners = $sale->saleItems
                            ->map(fn ($it) => $it->product?->supplier?->owner)
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();
                          $ownerText = count($owners) ? implode(', ', $owners) : '-';
                        @endphp

                        {{ $ownerText }}
                      </td>
                      <td>
                        @if ($sale->store_id)
                          <span class="badge badge-primary">{{ $sale->store?->nama_toko }}</span>
                        @elseif ($sale->warehouse_id)
                          <span class="badge badge-warning">
                            {{ $sale->warehouse?->nama_gudang }}
                          </span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-info">{{ $sale->saleItems->count() }} item</span>
                      </td>
                      <td class="text-right">
                        <strong class="text-primary">
                          Rp
                          {{ number_format($sale->total_amount ?? $sale->saleItems->sum(fn ($item) => $item->qty * $item->harga_jual), 0, ',', '.') }}
                        </strong>
                      </td>
                      <td>
                        @if ($sale->status === 'completed')
                          <span class="badge badge-success">Completed</span>
                        @elseif ($sale->status === 'hold')
                          <span class="badge badge-warning">
                            <i class="fas fa-hand-paper"></i>
                            Hold
                          </span>
                        @elseif ($sale->status === 'pending')
                          <span class="badge badge-info">Pending</span>
                        @else
                          <span class="badge badge-danger">Cancelled</span>
                        @endif
                      </td>
                      <td>
                        <button
                          wire:click="edit({{ $sale->id }})"
                          class="btn btn-info btn-xs mr-1"
                          title="Edit"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button
                          wire:click="delete({{ $sale->id }})"
                          wire:confirm="Yakin ingin menghapus penjualan ini?"
                          class="btn btn-danger btn-xs"
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
            <div class="row mt-3">
              <div class="col-md-12">
                {{ $sales->links() }}
              </div>
            </div>
          @else
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i>
              Belum ada data penjualan. Klik "Buat Penjualan" untuk membuat penjualan baru.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: Stock Warning -->
  @if ($showStockWarning)
    <div
      class="modal fade show"
      style="display: block; background: rgba(0, 0, 0, 0.5)"
      tabindex="-1"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-warning">
            <h5 class="modal-title">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              Stok Toko Tidak Mencukupi
            </h5>
            <button wire:click="cancelStockWarning" type="button" class="close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p class="font-weight-bold">Stok di toko tidak mencukupi untuk transaksi ini:</p>
            <div class="alert alert-warning">
              <pre style="white-space: pre-wrap; font-family: inherit">
{{ $stockWarningMessage }}</pre
              >
            </div>
            <p class="mt-3">
              Apakah Anda ingin mengambil stok dari
              <strong>GUDANG</strong>
              ?
            </p>
          </div>
          <div class="modal-footer">
            <button wire:click="cancelStockWarning" type="button" class="btn btn-secondary">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button wire:click="proceedWithWarehouse" type="button" class="btn btn-success">
              <i class="fas fa-warehouse mr-1"></i>
              Ya, Ambil dari Gudang
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Modal: Delivery Note (Surat Jalan) -->
  @if ($showDeliveryNoteModal)
    <div
      class="modal fade show"
      style="display: block; background: rgba(0, 0, 0, 0.5)"
      tabindex="-1"
    >
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
              <i class="fas fa-truck mr-2"></i>
              Surat Jalan
            </h5>
            <button wire:click="cancelDeliveryNote" type="button" class="close text-white">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto">
            <div class="card">
              <div class="card-body">
                <!-- Header Surat Jalan -->
                <div class="text-center mb-4">
                  <h3 class="font-weight-bold">SURAT JALAN</h3>
                  <p class="text-muted mb-0">PT. Your Company Name</p>
                  <p class="text-muted mb-0">Alamat: Jl. Contoh No. 123, Kota</p>
                  <p class="text-muted">Telp: (021) 1234567</p>
                </div>

                <hr />

                <!-- Detail Surat Jalan -->
                <div class="row mb-4">
                  <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                      <tr>
                        <td width="150"><strong>No. Surat Jalan</strong></td>
                        <td>: {{ $deliveryNoteNumber }}</td>
                      </tr>
                      <tr>
                        <td><strong>Tanggal</strong></td>
                        <td>
                          : {{ $deliveryDate ? date('d/m/Y', strtotime($deliveryDate)) : '-' }}
                        </td>
                      </tr>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table table-borderless table-sm">
                      <tr>
                        <td width="150"><strong>Kepada</strong></td>
                        <td>: {{ $customers->find($customer_id)?->nama_pelanggan ?? '-' }}</td>
                      </tr>
                      <tr>
                        <td><strong>Alamat</strong></td>
                        <td>: {{ $customers->find($customer_id)?->alamat ?? '-' }}</td>
                      </tr>
                    </table>
                  </div>
                </div>

                <!-- Tabel Item -->
                <table class="table table-bordered table-sm">
                  <thead class="bg-light">
                    <tr>
                      <th width="50">No</th>
                      <th>Nama Produk</th>
                      <th width="120">Jumlah</th>
                      <th width="100">Satuan</th>
                      <th width="150">Batch</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($saleItems as $index => $item)
                      @if (! empty($item['product_id']))
                        @php
                          $batch = ! empty($item['batch_id']) ? \App\Models\StockBatch::find($item['batch_id']) : null;
                          $batchName = $batch ? $batch->nama_tumpukan ?? "Batch #{$batch->id}" : '-';
                        @endphp

                        <tr>
                          <td class="text-center">{{ $index + 1 }}</td>
                          <td>{{ $products->find($item['product_id'])?->nama_produk ?? '-' }}</td>
                          <td class="text-right">{{ number_format($item['qty'] ?? 0, 0) }}</td>
                          <td>{{ $units->find($item['unit_id'])?->nama_unit ?? '-' }}</td>
                          <td>{{ $batchName }}</td>
                        </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>

                <!-- Catatan -->
                <div class="form-group mt-3">
                  <label><strong>Catatan Pengiriman:</strong></label>
                  <textarea
                    wire:model="deliveryNotes"
                    class="form-control"
                    rows="3"
                    placeholder="Masukkan catatan khusus pengiriman (opsional)"
                  ></textarea>
                </div>

                <!-- TTD -->
                <div class="row mt-5">
                  <div class="col-md-4 text-center">
                    <p class="mb-5">Pengirim,</p>
                    <p class="border-top d-inline-block px-5">(__________)</p>
                  </div>
                  <div class="col-md-4 text-center">
                    <p class="mb-5">Sopir,</p>
                    <p class="border-top d-inline-block px-5">(__________)</p>
                  </div>
                  <div class="col-md-4 text-center">
                    <p class="mb-5">Penerima,</p>
                    <p class="border-top d-inline-block px-5">(__________)</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button wire:click="cancelDeliveryNote" type="button" class="btn btn-secondary">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button wire:click="approveDeliveryNote" type="button" class="btn btn-success">
              <i class="fas fa-check mr-1"></i>
              Setuju & Lanjutkan Transaksi
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

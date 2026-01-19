<div>
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

{{-- @push('styles')
  <style>
    /* Redesigned 'Buat Penjualan Baru' card */
    .sales-create-card {
      border-radius: 0.8rem;
      overflow: hidden;
      box-shadow: 0 6px 24px rgba(17, 24, 39, 0.12);
      border: none;
    }

    .sales-create-card .card-header {
      background: linear-gradient(90deg,#5b6cff 0%,#7b61ff 100%);
      color: #fff;
      padding: 1rem 1.25rem;
      border-bottom: 0;
    }

    .sales-create-card .card-title {
      font-weight: 600;
      letter-spacing: 0.2px;
    }

    .sales-create-card .card-body {
      background: #ffffff;
      padding: 1.25rem;
    }

    .sales-create-card .form-control {
      border-radius: 8px;
      border: 1px solid #e6e9ef;
      box-shadow: none;
      transition: border-color .15s ease, box-shadow .15s ease;
    }

    .sales-create-card .form-control:focus {
      border-color: rgba(91,108,255,0.8);
      box-shadow: 0 0 0 0.15rem rgba(91,108,255,0.12);
      outline: none;
    }

    .sales-create-card .card-footer {
      background: linear-gradient(180deg, rgba(0,0,0,0.02), rgba(0,0,0,0.01));
      padding: 0.85rem 1.25rem;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }

    .sales-create-card .btn {
      border-radius: 6px;
      padding: .45rem .9rem;
    }

    /* Responsive tweaks */
    @media (max-width: 767px) {
      .sales-create-card .card-header { padding: .75rem 1rem; }
      .sales-create-card .card-body { padding: .85rem; }
      .sales-create-card .card-footer { flex-direction: column-reverse; gap: .5rem; align-items:stretch; }
      .sales-create-card .float-right { width:100%; display:flex; justify-content:flex-end; gap:.5rem }
    }
  </style>
@endpush --}}

  <!-- Inline Create Form -->
  @if ($showCreateForm)
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="card card-primary card-outline sales-create-card">
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
                  <div class="input-group">
                    <input
                      id="tanggal_penjualan_input"
                      wire:model="tanggal_penjualan"
                      type="text"
                      placeholder="dd/mm/YYYY"
                      class="form-control @error('tanggal_penjualan') is-invalid @enderror"
                    />
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="button" id="tanggal_penjualan_btn" title="Pilih tanggal">
                        <i class="far fa-calendar-alt"></i>
                      </button>
                    </div>
                  </div>
                  <small class="text-muted">Contoh: 18/01/2026</small>
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
                      <input id="customer_search" list="customers_list" class="form-control" placeholder="Ketik atau pilih pelanggan..." wire:model.live.debounce.300ms="customer_search">
                      <datalist id="customers_list">
                          @foreach($customers as $cust)
                              <option value="[{{ $cust->kode_pelanggan }}] {{ $cust->nama_pelanggan }}"></option>
                          @endforeach
                      </datalist>
                      <input type="hidden" wire:model="customer_id">
                    <div class="input-group-append">
                      <button
                        type="button"
                        class="btn btn-outline-success"
                        wire:click="openCreateCustomerModal"
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

                  <!-- Tampilkan nama toko sebagai badge (statik). Gunakan hidden input untuk binding store_id -->
                  @php
                    $selectedStore = null;
                    if (!empty($store_id)) {
                        foreach ($stores as $s) {
                            if ($s->id == $store_id) { $selectedStore = $s; break; }
                        }
                    }
                  @endphp

                  @if($selectedStore)
                    <span class="badge badge-primary p-2">{{ $selectedStore->nama_toko }}</span>
                  @else
                    <span class="badge badge-secondary p-2">-- Pilih Toko --</span>
                  @endif

                  <input type="hidden" wire:model.live="store_id" />
                  <!-- Pastikan location_source tetap 'toko' di backend -->
                  <input type="hidden" wire:model.live="location_source" value="toko" />
                </div>
              </div>
            </div>

            {{-- Status moved into the Items table after Total column --}}

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
              <div id="createCustomerModal" class="lw-modal" aria-modal="true" role="dialog">
                <div class="lw-backdrop"></div>
                <div class="lw-dialog" role="document">
                  <div class="lw-content">
                    <div class="lw-header">
                      <h5 class="lw-title">Tambah Pelanggan</h5>
                      <button type="button" class="close" aria-label="Close" wire:click="$set('showCreateCustomerModal', false)">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="lw-body">
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
                        <textarea wire:model.defer="new_customer_alamat" class="form-control" rows="3"></textarea>
                      </div>
                    </div>
                    <div class="lw-footer">
                      <button class="btn btn-success" wire:click.prevent="saveNewCustomer">Simpan</button>
                      <button class="btn btn-secondary" wire:click="$set('showCreateCustomerModal', false)">Batal</button>
                    </div>
                  </div>
                </div>
              </div>

              <style>
                /* Lightweight Livewire modal styles (scoped) - adjusted to match lightweight appearance from screenshot #1 */
                /* Keep z-index above main content but avoid full-page darkening */
                /* Use a fixed-position dialog with transform centering to avoid jitter when scrollbars or layout change */
                .lw-modal { position: fixed; inset: 0; z-index: 1060; }
                /* very light backdrop so page remains visible (matches screenshot 1) */
                .lw-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.06); }
                /* Keep dialog fixed and centered via transform to avoid reflow caused by flex-centering */
                .lw-dialog { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1061; width: 680px; max-width: 92%; max-height: calc(100vh - 120px); }
                .lw-content { background: #fff; border-radius: 6px; box-shadow: 0 6px 18px rgba(17,24,39,0.08); display: flex; flex-direction: column; max-height: calc(100vh - 160px); }
                .lw-header { padding: 12px 16px; border-bottom: 1px solid #e9ecef; display:flex; align-items:center; justify-content:space-between; }
                .lw-title { margin:0; font-size:1rem; }
                .lw-body { padding: 16px; overflow:auto; }
                .lw-footer { padding: 12px 16px; border-top:1px solid #e9ecef; display:flex; gap:8px; justify-content:flex-end; }
                @media (max-width: 480px) { .lw-dialog { width: 95%; } }
              </style>
            @endif

            <!-- Items Table -->
            @if (count($saleItems) > 0)
              <div class="form-group mt-4">
                <label><strong>Item Penjualan</strong></label>
                <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead class="bg-light">
                      <tr>
                        <th style="width: 5%">#</th>
                         <th style="width: 15%">Pelanggan</th>
                        <th style="width: 10%">Kategori</th>
                        <th style="width: 10%">Subkategori</th>
                        <th style="width: 12%">Produk</th>
                        <th style="width: 12%">Batch/Tumpukan</th>
                        <th style="width: 7%">Qty</th>
                        <th style="width: 8%">Unit</th>
                        <th style="width: 12%">Harga Jual</th>
                        <th style="width: 10%">Total</th>
                        <th style="width: 8%">Status</th>
                        <th style="width: 5%">Hapus</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($saleItems as $index => $item)
                        <tr wire:key="item-{{ $index }}">
                          <td class="text-center">{{ $index + 1 }}</td>
                           <td>{{ $customer_search ?: (optional($customers->firstWhere('id', $customer_id))->nama_pelanggan ?: '-') }}</td>
                          <td>
                            <select
                              wire:model="saleItems.{{ $index }}.category_id"
                              class="form-control form-control-sm"
                            >
                              <option value="">--</option>
                              @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
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
                                Produk dipilih
                              </small>
                            @endif
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <select
                                wire:model.live="saleItems.{{ $index }}.batch_id"
                                class="form-control form-control-sm"
                              >
                                <option value="">-- Pilih Batch --</option>
                                @php
                                  $batches = $this->getAvailableBatches($index);
                                @endphp

                                @if ($batches->count() === 0)
                                  <option disabled selected>
                                    Pilih produk dulu (pid={{ $item['product_id'] ?? 'null' }})
                                  </option>
                                @else
                                  @foreach ($batches as $batch)
                                    <option
                                      value="{{ $batch->id }}"
                                      @if($item['batch_id'] == $batch->id) selected @endif
                                    >
                                      {{ $batch->nama_tumpukan }} ({{ $batch->qty }} sak)
                                    </option>
                                  @endforeach
                                @endif
                              </select>
                              @if ($item['batch_id'])
                                @php
                                  $selectedBatch = \App\Models\StockBatch::find($item['batch_id']);
                                @endphp

                                <span class="input-group-text" title="Stok tersedia">
                                  @if ($selectedBatch)
                                    <span class="badge badge-info">
                                      {{ $selectedBatch->qty }} sak
                                    </span>
                                  @endif
                                </span>
                              @endif
                            </div>
                            @if ($item['batch_id'] && $item['qty'])
                              @php
                                $batch = \App\Models\StockBatch::find($item['batch_id']);
                                $itemQty = $item['qty'] ?? 0;
                                $batchQty = $batch ? $batch->qty : 0;
                              @endphp

                              @if ($itemQty > $batchQty)
                                <small class="text-danger d-block mt-1">
                                  <i class="fas fa-exclamation-triangle"></i>
                                  Batch tidak cukup! Tersedia: {{ $batchQty }} sak
                                </small>
                              @endif
                            @endif
                          </td>
                          <td>
                            <input
                              wire:model="saleItems.{{ $index }}.qty"
                              wire:change="updateTotal({{ $index }})"
                              type="number"
                              min="1"
                              class="form-control form-control-sm"
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
                              class="form-control form-control-sm"
                            />
                          </td>
                          <td class="text-right font-weight-bold">
                            Rp {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                          </td>
                          @if ($index === 0)
                            <td rowspan="{{ count($saleItems) }}" class="align-middle text-center">
                              <select
                                wire:model="status"
                                class="form-control form-control-sm @error('status') is-invalid @enderror"
                              >
                                <option value="pending">Pending</option>
                                <option value="hold">Hold / Keep (Tahan Stok)</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                              </select>
                              @error('status')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                              @enderror
                            </td>
                          @endif

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
                        <td colspan="8" class="text-right pr-3">Kuli:</td>
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
                              @keyup="
                                                                let value = $el.value.replace(/\D/g, '');
                                                                if(value) {
                                                                    $el.value = new Intl.NumberFormat('id-ID').format(parseInt(value));
                                                                }
                                                            "
                              @blur="@this.set('kuli', parseInt($el.value.replace(/\D/g, '') || 0))"
                            />
                          </div>
                        </td>
                        <td colspan="2"></td>
                      </tr>
                      <tr>
                        <td colspan="8" class="text-right pr-3">TOTAL:</td>
                        <td colspan="2" class="text-right text-success">
                          Rp
                          {{ number_format(array_sum(array_column($saleItems, 'total')) + ($kuli ?? 0), 0, ',', '.') }}
                        </td>
                        <td></td>
                      </tr>
                    </tfoot>
                  </table>
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
          <div class="card-footer d-flex justify-content-end align-items-center">
            <div class="mr-auto">
              <button wire:click="addItem" type="button" class="btn btn-info btn-sm">
                <i class="fas fa-plus"></i>
                Tambah Item
              </button>
            </div>
            <div class="d-flex gap-2">
              <button wire:click="cancel" class="btn btn-secondary btn-sm">
                <i class="fas fa-times"></i>
                Batal
              </button>
              <button
                type="button"
                class="btn btn-primary btn-sm"
                title="Cetak (simpan dulu jika baru)"
                onclick="window._printWindow = window.open('about:blank'); window.__startPrintWatcher();"
                wire:click="printSale"
              >
                <i class="fas fa-print"></i>
                Cetak
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
                    <th style="width: 15%">Pelanggan</th>
                    <th style="width: 10%">Lokasi</th>
                    <th style="width: 8%">Total Item</th>
                    <th style="width: 14%" class="text-right">Total Amount</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 16%">Aksi</th>
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

  </script>

  {{-- Hidden field updated by Livewire to communicate print URL after action completes --}}
  <input type="hidden" id="__print_url" value="{{ $lastSavedSaleId ? route('admin.sales.print', $lastSavedSaleId) : '' }}" />

  <script>
    (function () {
      function openPrintUrl(url) {
        if (!url) return;
        try {
          if (window._printWindow && !window._printWindow.closed) {
            window._printWindow.location = url;
            window._printWindow.focus();
          } else {
            window.open(url, '_blank');
          }
        } catch (ex) {
          window.open(url, '_blank');
        }
      }

      // Polling watcher: checks hidden input for URL after Livewire updates
      let __printWatcher = null;
      function startPrintWatcher() {
        if (__printWatcher) return;
        __printWatcher = setInterval(() => {
          try {
            const el = document.getElementById('__print_url');
            const url = el ? (el.value || el.getAttribute('value')) : null;
            if (url) {
              openPrintUrl(url);
              // clear value to avoid reopening repeatedly
              if (el) {
                el.value = '';
                el.setAttribute('value', '');
              }
              clearInterval(__printWatcher);
              __printWatcher = null;
            }
          } catch (ex) {
            // ignore errors
          }
        }, 250);
      }

      // Expose starter for the print button (button opens blank window synchronously)
      window.__startPrintWatcher = startPrintWatcher;
    })();
  </script>
</div>

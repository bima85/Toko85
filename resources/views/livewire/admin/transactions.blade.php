<div>
  <style>
    .tx-batch { display: inline-flex; gap: 6px; align-items: center; margin-right: 6px; margin-bottom: 6px; }
    .tx-batch .badge { font-size: 0.82rem; padding: 0.35rem 0.55rem; }
    .tx-loc { font-size: 0.72rem; padding: 0.28rem 0.45rem; }
    .tx-product-name { font-weight: 600; display: block; margin-bottom: 4px; }
  </style>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Transaksi Realtime</h3>
    <div class="btn-group">
      <button
        wire:click="$set('tab','sale')"
        class="btn btn-sm @if($tab==='sale') btn-primary @else btn-outline-primary @endif"
      >
        Penjualan
      </button>
      <button
        wire:click="$set('tab','purchase')"
        class="btn btn-sm @if($tab==='purchase') btn-success @else btn-outline-secondary @endif"
      >
        Pembelian
      </button>
    </div>
  </div>

  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="row">
    <div class="col-md-12 mb-3">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-9">
              <input
                type="text"
                class="form-control form-control-sm"
                placeholder="Cari produk..."
                value="{{ $recent_search }}"
                wire:change="setRecentSearch($event.target.value)"
              />
            </div>
            <div class="col-md-3">
              <select
                wire:change="setRecentLocation($event.target.value)"
                class="form-control form-control-sm w-100"
              >
                <option value="">-- Semua Lokasi --</option>
                <optgroup label="Toko">
                  @foreach ($stores as $s)
                    <option value="store:{{ $s->id }}">Toko: {{ $s->nama_toko }}</option>
                  @endforeach
                </optgroup>
                <optgroup label="Gudang">
                  @foreach ($warehouses as $wh)
                    <option value="warehouse:{{ $wh->id }}">Gudang: {{ $wh->nama_gudang }}</option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>

          {{-- <div class="mb-2">
            <small class="text-muted">Contoh grup produk (dikelompokkan berdasarkan batch):</small>
          </div> --}}

          {{-- <div class="small text-muted mb-2">
            <strong>DEBUG:</strong>
            search="{{ $recent_search }}" location="{{ $recent_location }}" — grouped
            {{ $groupedBatches->count() }} products
          </div> --}}

          <div style="max-height: 220px; overflow: auto">
            <ul class="list-group">
              @forelse ($groupedBatches as $productName => $batches)
                <li class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong>{{ $productName }}</strong>
                      <div class="small text-muted font-weight-bolder">Batch:</div>
                      <div class="small">
                        @foreach ($batches as $b)
                          @php
                            $locType = $b['location_type'] ?? null;
                            $locName = $b['location'] ?? null;
                            $locIcon = $locType === 'warehouse' ? 'fa-warehouse' : 'fa-store';
                          @endphp

                          <span class="tx-batch">
                            <span class="badge badge-success">{{ $b['name'] }}: {{ (int) $b['qty'] }} sak</span>
                            @if(!empty($locName))
                              <span class="badge badge-info tx-loc"><i class="fas {{ $locIcon }} fa-xs mr-1"></i> {{ $locName }}</span>
                            @elseif($locType === 'store')
                              <span class="badge badge-primary tx-loc"><i class="fas fa-store fa-xs mr-1"></i> Toko</span>
                            @elseif($locType === 'warehouse')
                              <span class="badge badge-success tx-loc"><i class="fas fa-warehouse fa-xs mr-1"></i> Gudang</span>
                            @endif
                          </span>
                        @endforeach
                      </div>
                    </div>
                    <div class="text-muted small text-sm">{{ $batches->sum('qty') }} total</div>
                  </div>
                </li>
              @empty
                <li class="list-group-item small text-muted">Tidak ada batch untuk filter ini.</li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12 mb-3">
      @if ($tab === 'sale')
        {{-- Full Sales component (header hidden) --}}
        <livewire:admin.sales :hide-header="true" />
      @else
        {{-- Purchase form (matching Purchases component) --}}
        <form wire:submit.prevent="submitPurchase">
          <div class="card card-primary card-outline">
            @if(trim($recent_search) !== '')
              <div class="mb-2">
                <small class="text-muted">Contoh grup produk (dikelompokkan berdasarkan batch):</small>
              </div>

              {{-- <div class="small text-muted mb-2">
                <strong>DEBUG:</strong> search="{{ $recent_search }}" location="{{ $recent_location }}" — grouped {{ $groupedBatches->count() }} products
              </div> --}}

              <div style="max-height: 220px; overflow: auto">
                <ul class="list-group">
                  @forelse ($groupedBatches as $productName => $batches)
                    <li class="list-group-item">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <strong>{{ $productName }}</strong>
                          <div class="small text-muted">Batch:</div>
                          <div class="small">
                              @foreach ($batches as $b)
                                @php
                                  $locType = $b['location_type'] ?? null;
                                  $locName = $b['location'] ?? null;
                                  $locIcon = $locType === 'warehouse' ? 'fa-warehouse' : 'fa-store';
                                @endphp

                                <span class="tx-batch">
                                  <span class="badge badge-light">{{ $b['name'] }}: {{ (int) $b['qty'] }} sak</span>
                                  @if(!empty($locName))
                                    <span class="badge badge-info tx-loc"><i class="fas {{ $locIcon }} fa-xs mr-1"></i> {{ $locName }}</span>
                                  @elseif($locType === 'store')
                                    <span class="badge badge-primary tx-loc"><i class="fas fa-store fa-xs mr-1"></i> Toko</span>
                                  @elseif($locType === 'warehouse')
                                    <span class="badge badge-success tx-loc"><i class="fas fa-warehouse fa-xs mr-1"></i> Gudang</span>
                                  @endif
                                </span>
                              @endforeach
                          </div>
                        </div>
                        <div class="text-muted small">{{ $batches->sum('qty') }} total</div>
                      </div>
                    </li>
                  @empty
                    <li class="list-group-item small text-muted">Tidak ada batch untuk filter ini.</li>
                  @endforelse
                </ul>
              </div>
            @else
              <div class="small text-muted">Ketik nama produk untuk melihat grup batch.</div>
            @endif

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

              <!-- Items Table -->
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
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                      <thead class="bg-light">
                        <tr>
                          <th style="width: 3%">#</th>
                          <th style="width: 10%">Kategori</th>
                          <th style="width: 12%">Subkategori</th>
                          <th style="width: 14%">Produk</th>
                          @if ($batch_enabled)
                            <th style="width: 10%">Batch</th>
                          @endif

                          <th style="width: 10%">Qty</th>
                          <th style="width: 6%">Unit</th>
                          <th style="width: 9%">Harga Beli</th>
                          <th style="width: 9%">Total</th>
                          <th style="width: 7%">Hapus</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($purchaseItems as $index => $item)
                          <tr wire:key="item-{{ $index }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                              <select
                                wire:model="purchaseItems.{{ $index }}.category_id"
                                wire:change="$refresh"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @foreach ($categories as $cat)
                                  <option value="{{ $cat->id }}">
                                    {{ $cat->nama_kategori }}
                                  </option>
                                @endforeach
                              </select>
                              @error('purchaseItems.' . $index . '.category_id')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            <td>
                              <select
                                wire:model="purchaseItems.{{ $index }}.subcategory_id"
                                wire:change="$refresh"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @if ($item['category_id'])
                                  @foreach (\App\Models\Subcategory::where('category_id', $item['category_id'])->get() as $sub)
                                    <option value="{{ $sub->id }}">
                                      {{ $sub->nama_subkategori }}
                                    </option>
                                  @endforeach
                                @endif
                              </select>
                              @error('purchaseItems.' . $index . '.subcategory_id')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            <td>
                              @php
                                $currentProductName = '';
                                if (! empty($item['product_id'])) {
                                    $p = $products->firstWhere('id', $item['product_id']);
                                    if ($p) $currentProductName = $p->nama_produk;
                                }
                              @endphp

                              <div style="position:relative">
                                <input
                                  type="text"
                                  class="form-control form-control-sm"
                                  placeholder="Cari produk..."
                                  wire:model.debounce.300ms="purchaseItems.{{ $index }}.product_search"
                                  wire:input="searchProducts({{ $index }}, $event.target.value)"
                                  autocomplete="off"
                                />

                                @if(!empty($productSuggestions[$index] ?? []))
                                  <div class="list-group position-absolute w-100" style="z-index:50; max-height:220px; overflow:auto;">
                                    @foreach($productSuggestions[$index] as $s)
                                      <button type="button" class="list-group-item list-group-item-action" wire:click="selectSuggestedProduct({{ $index }}, {{ $s['id'] }})">
                                        {{ $s['nama_produk'] }}
                                      </button>
                                    @endforeach
                                  </div>
                                  @endif
                                  <div class="mt-1">
                                    <small class="text-muted">Saran: {{ count($productSuggestions[$index] ?? []) }}</small>
                                  </div>
                              </div>

                              @error('purchaseItems.' . $index . '.product_id')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            @if ($batch_enabled)
                              <td
                                data-label="Batch"
                                style="padding: 8px; vertical-align: top; width: 210px"
                              >
                                <div style="display: flex; flex-direction: column; gap: 6px">
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
                                      class="btn btn-outline-primary btn-sm text-center mt-2"
                                      wire:click="addBatchRow({{ $index }})"
                                    >
                                      <i class="fas fa-plus-circle mr-1"></i>
                                      Tambah
                                    </button>
                                  </div>
                                </div>
                              </td>
                            @endif

                            <td>
                              <input
                                type="number"
                                wire:model="purchaseItems.{{ $index }}.qty"
                                class="form-control form-control-sm"
                                min="1"
                                @if ($batch_enabled) disabled @endif
                              />
                              @error('purchaseItems.' . $index . '.qty')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            <td>
                              <select
                                wire:model="purchaseItems.{{ $index }}.unit_id"
                                class="form-control form-control-sm"
                              >
                                <option value="">--</option>
                                @foreach ($units as $unit)
                                  <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                @endforeach
                              </select>
                              @error('purchaseItems.' . $index . '.unit_id')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            <td>
                              <input
                                type="number"
                                step="0.01"
                                wire:model="purchaseItems.{{ $index }}.harga_beli"
                                class="form-control form-control-sm"
                              />
                              @error('purchaseItems.' . $index . '.harga_beli')
                                <div class="text-danger small">{{ $message }}</div>
                              @enderror
                            </td>
                            <td class="text-right">
                              Rp
                              {{ number_format(($item['harga_beli'] ?? 0) * ($item['qty'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                              <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                wire:click.prevent="removePurchaseItem({{ $index }})"
                                @if(count($purchaseItems) <= 1) disabled @endif
                              >
                                ×
                              </button>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                  <div class="mt-2">
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-primary"
                      wire:click.prevent="addPurchaseItem"
                    >
                      <i class="fas fa-plus"></i>
                      Tambah Item
                    </button>
                  </div>
                </div>
              @endif

              <div class="d-flex justify-content-end mt-3">
                <button
                  class="btn btn-success"
                  wire:loading.attr="disabled"
                  wire:target="submitPurchase"
                >
                  <span wire:loading.remove wire:target="submitPurchase">Simpan Pembelian</span>
                  <span wire:loading wire:target="submitPurchase">Memproses…</span>
                </button>
              </div>
            </div>
          </div>
        </form>
      @endif
    </div>
  </div>

  <!-- Owner Modal -->
  @if ($showOwnerModal)
    <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5)" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Supplier/Owner Baru</h5>
            <button type="button" class="close" wire:click="closeOwnerModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Supplier/Owner</label>
              <input
                type="text"
                wire:model="new_owner_name"
              @foreach ($recent as $r)
                <li class="list-group-item d-flex justify-content-between align-items-start">
                  <div>
                    <span class="badge badge-{{ $r->type === 'in' ? 'success' : 'danger' }} mr-2">
                      {{ strtoupper($r->type) }}
                    </span>
                    <strong>{{ $r->product?->nama_produk ?? 'Produk tidak ditemukan' }}</strong>
                    <div class="small text-muted">{{ $r->note }}</div>
                    @if($r->batch)
                      <div class="small text-muted">Batch: {{ $r->batch->nama_tumpukan ?: '—' }}
                        @php
                          $loc = null;
                          if (! empty($r->batch->location_type) && ! empty($r->batch->location_id)) {
                              if ($r->batch->location_type === 'store') {
                                  $loc = $stores->firstWhere('id', $r->batch->location_id)?->nama_toko;
                              } elseif ($r->batch->location_type === 'warehouse') {
                                  $loc = $warehouses->firstWhere('id', $r->batch->location_id)?->nama_gudang;
                              }
                          }
                          $loc = $loc ?? ($r->from_location ?? $r->to_location ?? '');
                        @endphp
                        @if($loc)
                          — <small class="text-muted">{{ $loc }}</small>
                        @endif
                      </div>
                    @endif
                  </div>
                  <div class="text-right small">
                    <div>{{ $r->qty }}</div>
                    <div class="text-muted">{{ $r->created_at->diffForHumans() }}</div>
                  </div>
                </li>
              @endforeach
      </div>
    </div>
  @endif

  <!-- Supplier Modal -->
  @if ($showSupplierModal)
    <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5)" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Supplier Baru</h5>
            <button type="button" class="close" wire:click="closeSupplierModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Supplier</label>
                  <input
                    type="text"
                    wire:model="kode_supplier"
                    class="form-control"
                    placeholder="SUP-001"
                  />
                  @error('kode_supplier')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Supplier</label>
                  <input
                    type="text"
                    wire:model="nama_supplier"
                    class="form-control"
                    placeholder="PT. Supplier Indonesia"
                  />
                  @error('nama_supplier')
                    <small class="text-danger">{{ $message }}</small>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Telepon</label>
                  <input
                    type="text"
                    wire:model="telepon"
                    class="form-control"
                    placeholder="08123456789"
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email</label>
                  <input
                    type="email"
                    wire:model="email"
                    class="form-control"
                    placeholder="supplier@email.com"
                  />
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Alamat</label>
              <textarea
                wire:model="alamat"
                class="form-control"
                rows="2"
                placeholder="Alamat lengkap supplier"
              ></textarea>
            </div>
            <div class="form-group">
              <label>Keterangan</label>
              <textarea
                wire:model="supplier_keterangan"
                class="form-control"
                rows="2"
                placeholder="Catatan tambahan"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeSupplierModal">
              Batal
            </button>
            <button type="button" class="btn btn-primary" wire:click="saveSupplier">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

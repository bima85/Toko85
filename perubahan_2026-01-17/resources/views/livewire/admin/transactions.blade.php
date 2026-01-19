<div>
  <style>
    .tx-batch {
      display: inline-flex;
      gap: 6px;
      align-items: center;
      margin-right: 6px;
      margin-bottom: 6px;
    }
    .tx-batch .badge {
      font-size: 0.82rem;
      padding: 0.35rem 0.55rem;
    }
    .tx-loc {
      font-size: 0.72rem;
      padding: 0.28rem 0.45rem;
    }
    .tx-product-name {
      font-weight: 600;
      display: block;
      margin-bottom: 4px;
    }
    /* Card spacing tweaks: slightly more compact and consistent padding */
    .card {
      margin-bottom: 0.9rem;
      border-radius: 0.45rem;
    }

    .card-header {
      padding: 0.55rem 0.9rem;
    }

    .card-body {
      padding: 0.75rem 0.9rem;
    }

    /* Make controls inside cards a bit tighter on mobile */
    @media (max-width: 768px) {
      .card-body {
        padding: 0.6rem 0.65rem;
      }
    }
  </style>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">Transaksi Realtime</h3>
    <div class="btn-group">
      <button
        wire:click="$set('tab','purchase')"
        class="btn btn-sm @if($tab==='purchase') btn-success @else btn-outline-secondary @endif"
      >
        Pembelian
      </button>
      <button
        wire:click="$set('tab','sale')"
        class="btn btn-sm @if($tab==='sale') btn-primary @else btn-outline-primary @endif"
      >
        Penjualan
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
                    <option value="warehouse:{{ $wh->id }}">
                      Gudang: {{ $wh->nama_gudang }}
                    </option>
                  @endforeach
                </optgroup>
              </select>
            </div>
          </div>

          {{--
            <div class="mb-2">
            <small class="text-muted">Contoh grup produk (dikelompokkan berdasarkan batch):</small>
            </div>
          --}}

          {{--
            <div class="small text-muted mb-2">
            <strong>DEBUG:</strong>
            search="{{ $recent_search }}" location="{{ $recent_location }}" — grouped
            {{ $groupedBatches->count() }} products
            </div>
          --}}

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
                            <span class="badge badge-success">
                              {{ $b['name'] }}: {{ (int) $b['qty'] }} sak
                            </span>

                            @if (! empty($locName))
                              <span class="badge badge-info tx-loc">
                                <i class="fas {{ $locIcon }} fa-xs mr-1"></i>
                                {{ $locName }}
                              </span>
                            @elseif ($locType === 'store')
                              <span class="badge badge-primary tx-loc">
                                <i class="fas fa-store fa-xs mr-1"></i>
                                Toko
                              </span>
                            @elseif ($locType === 'warehouse')
                              <span class="badge badge-success tx-loc">
                                <i class="fas fa-warehouse fa-xs mr-1"></i>
                                Gudang
                              </span>
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
        <livewire:admin.purchases :hide-header="true" />
      @endif
    </div>
  </div>

  <!-- Owner Modal -->
  {{--
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
    <input type="text" wire:model="new_owner_name" class="form-control" />
    </div>
    
    <hr />
    
    <div class="small text-muted mb-2">Riwayat transaksi terbaru</div>
    <ul class="list-group">
    @foreach ($recent as $r)
    <li class="list-group-item d-flex justify-content-between align-items-start">
    <div>
    <span class="badge badge-{{ $r->type === 'in' ? 'success' : 'danger' }} mr-2">
    {{ strtoupper($r->type) }}
    </span>
    <strong>{{ $r->product?->nama_produk ?? 'Produk tidak ditemukan' }}</strong>
    @if($r->product)
    <div class="small text-muted">
    <span class="badge badge-secondary">{{ $r->product->category?->nama_kategori ?? 'Kategori tidak ditemukan' }}</span>
    @if($r->product->subcategory)
    <span class="badge badge-light">{{ $r->product->subcategory->nama_subkategori }}</span>
    @endif
    </div>
    @endif
    <div class="small text-muted">{{ $r->note }}</div>
    @if ($r->batch)
    <div class="small text-muted">
    Batch: {{ $r->batch->nama_tumpukan ?: '—' }}
    @php
    $loc = null;
    if (! empty($r->batch->location_type) && ! empty($r->batch->location_id)) {
    if ($r->batch->location_type === 'store') {
    $loc = $stores->firstWhere('id', $r->batch->location_id)?->nama_toko;
    } elseif ($r->batch->location_type === 'warehouse') {
    $loc = $warehouses->firstWhere('id', $r->batch->location_id)?->nama_gudang;
    }
    }
    $loc = $loc ?? ($r->from_location ?? ($r->to_location ?? ''));
    @endphp
    
    @if ($loc)
    —
    <small class="text-muted">{{ $loc }}</small>
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
    </ul>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-secondary" wire:click="closeOwnerModal">
    Batal
    </button>
    <button type="button" class="btn btn-primary" wire:click="saveOwner">Simpan</button>
    </div>
    </div>
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
  --}}
</div>

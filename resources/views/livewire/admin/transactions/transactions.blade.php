<div>
  <style>
    /* Ensure col-md-12 doesn't have width constraints */
    .col-md-12.mb-3 {
      width: 100%;
      padding-left: 0;
      padding-right: 0;
    }

    /* Batch styling - CSS minimal untuk batch column */
    .batch-actions {
      display: flex;
      gap: 0.3rem;
      align-items: flex-start;
      position: relative;
      flex-wrap: nowrap;
      max-width: 340px;
      min-width: 0;
      white-space: normal;
      overflow: visible;
    }

    .batch-chip {
      display: inline-flex !important;
      align-items: center;
      gap: 0.3rem;
      background: #f8f9fa;
      border: 1px solid #e5e7eb;
      border-radius: 0.45rem;
      padding: 0.25rem 0.35rem;
      width: auto;
      box-sizing: border-box;
      white-space: nowrap;
    }

    .batch-chip input {
      width: 56px;
      max-width: 56px;
      padding: 0.25rem 0.4rem;
      height: calc(1.5em + 0.5rem + 2px);
      font-size: 0.875rem;
    }

    .batch-chip>.btn {
      flex: 0 0 auto;
    }

    .batch-rows-wrapper {
      display: flex;
      flex-direction: column;
      flex: 1;
      gap: 0.25rem;
    }

    .batch-row {
      display: block;
    }

    .batch-row+.batch-row {
      margin-top: 0.25rem;
    }

    .batch-add {
      display: flex;
      align-items: flex-start;
      flex: 0 0 auto;
      z-index: 2;
    }

    .batch-actions .btn {
      padding: 0.25rem 0.45rem;
      font-size: 0.8125rem;
      margin: 0;
    }
  </style>

  <div class="row">
    <div class="col-md-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Transaksi Realtime</h3>
        <div class="btn-group">
          <button wire:click="$set('tab','sale')"
            class="btn btn-sm @if($tab==='sale') btn-primary @else btn-outline-primary @endif">
            Penjualan
          </button>
          @php
          $canPurchaseButton =
          Auth::user()
          ?->can('purchases.view') ||
          Auth::user()
          ?->can('transactions.view.purchase');
          @endphp

          @if ($canPurchaseButton)
          <button wire:click="$set('tab','purchase')"
            class="btn btn-sm @if($tab==='purchase') btn-success @else btn-outline-secondary @endif">
            Pembelian
          </button>
          @endif
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
                  <input type="text" class="form-control form-control-sm" placeholder="Cari produk..."
                    value="{{ $recent_search }}" wire:change="setRecentSearch($event.target.value)" />
                </div>
                <div class="col-md-3">
                  <select wire:change="setRecentLocation($event.target.value)"
                    class="form-control form-control-sm w-100">
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

              <div class="mb-2">
                <small class="text-muted">Riwayat aktivitas (terbaru)</small>
              </div>
              <div style="max-height: 160px; overflow: auto; margin-bottom: 8px">
                <ul class="list-group">
                  @forelse ($recent as $r)
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <div class="small tx-product-name">
                        {{ $r->product?->nama_produk ?? 'Produk tidak ditemukan' }}
                      </div>
                      <div class="small text-muted">
                        @if ($r->type === 'in')
                        <i class="fas fa-plus-circle text-success"></i>
                        @else
                        <i class="fas fa-minus-circle text-danger"></i>
                        @endif
                        &nbsp;{{ $r->qty }} &middot; {{ ucfirst($r->type) }} &nbsp;•&nbsp;
                        {{ $r->reference_type ? ucfirst($r->reference_type) : '' }}
                        @if ($r->reference_id)
                        #{{ $r->reference_id }}
                        @endif
                      </div>
                      <div class="small text-muted">
                        @if ($r->batch)
                        Tumpukan: {{ $r->batch->nama_tumpukan }}
                        @endif

                        @if ($r->created_at)
                        &nbsp;•&nbsp; {{ $r->created_at->diffForHumans() }}
                        @endif
                      </div>
                    </div>
                    <div class="text-muted small">{{ $r->product?->kode_produk ?? '' }}</div>
                  </li>
                  @empty
                  <li class="list-group-item small text-muted">Belum ada aktivitas terbaru.</li>
                  @endforelse
                </ul>
              </div>

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
                      <div class="text-muted small text-sm">
                        {{ $batches->sum('qty') }} total
                      </div>
                    </div>
                  </li>
                  @empty
                  <li class="list-group-item small text-muted">
                    Tidak ada batch untuk filter ini.
                  </li>
                  @endforelse
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12 mb-3">
          @php
          $canPurchase =
          auth()
          ->user()
          ?->can('purchases.view') ||
          auth()
          ->user()
          ?->can('transactions.view.purchase');
          @endphp

          @if ($tab === 'sale' || ! $canPurchase)
          {{-- Komponen Penjualan Lengkap (header disembunyikan) --}}
          <livewire:admin.sales :hide-header="true" />
          @elseif ($tab === 'purchase' && $canPurchase)
          <livewire:admin.purchases :hide-header="true" />
          {{-- Inline Purchase Form with same styling as create-purchase --}}
          {{-- @push('styles')
          <style>
            /* Full-screen, clean AdminLTE-style form */
            .create-purchase-fullscreen {
              min-height: calc(100vh - 120px) !important;
            }

            .create-purchase-card {
              border: none !important;
              border-radius: 6px !important;
              overflow: hidden !important;
              /* fill the entire content area width */
              width: 100% !important;
              max-width: none !important;
              margin: 0 !important;
              box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06) !important;
            }

            /* make left header full height and centered */
            .row.align-items-stretch>.col-md-3,
            .row.g-0.align-items-stretch>.col-md-3 {
              padding: 0 !important;
            }

            /* force container to allow wide content in AdminLTE layout */
            .content>.container-fluid.create-purchase-fullscreen {
              max-width: none !important;
              padding-left: 15px !important;
              padding-right: 15px !important;
              width: 100% !important;
            }

            /* remove row gutters for tighter full-width layout */
            .create-purchase-card .row {
              margin-left: -5px !important;
              margin-right: -5px !important;
            }

            .create-purchase-card .row>[class^='col-'] {
              padding-left: 5px !important;
              padding-right: 5px !important;
            }

            .create-purchase-header {
              background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%) !important;
              color: #fff !important;
              height: 100% !important;
              padding: 36px 22px !important;
              display: flex !important;
              flex-direction: column !important;
              justify-content: center !important;
            }

            .create-purchase-header .title {
              font-size: 1.15rem !important;
              font-weight: 700 !important;
              margin-bottom: 6px !important;
            }

            /* reduce default card-body padding so layout aligns with screenshot */
            .card .card-body {
              padding: 0.75rem 1rem !important;
            }

            /* Table styling */
            .create-purchase-items tfoot {
              background: linear-gradient(90deg,
                  rgba(76, 175, 80, 0.06),
                  rgba(76, 175, 80, 0.02)) !important;
              font-weight: 700 !important;
            }

            .create-purchase-items tfoot td {
              border-top: 2px solid #e9f5ec !important;
            }

            .create-purchase-items .thead-light th {
              background: #f5f7f8 !important;
              border-bottom: 1px solid #e9ecef !important;
            }

            .create-purchase-actions {
              margin-top: 18px !important;
            }

            /* Buttons match screenshot tone */
            .btn-secondary {
              background: #6c757d !important;
              border-color: #6c757d !important;
            }

            /* Responsive tweaks */
            @media (max-width: 767px) {
              .create-purchase-header .title {
                font-size: 1rem;
              }
            }
          </style>
          @endpush --}}


          @endif
        </div>
      </div>

      <!-- Owner Modal -->
      {{-- @if ($showOwnerModal)
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
              <div class="mt-2">
                <button class="btn btn-primary btn-sm" wire:click="saveOwner">Simpan</button>
                <button class="btn btn-secondary btn-sm" wire:click="closeOwnerModal">
                  Batal
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif --}}

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
                    <input type="text" wire:model="kode_supplier" class="form-control" placeholder="SUP-001" />
                    @error('kode_supplier')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" wire:model="nama_supplier" class="form-control"
                      placeholder="PT. Supplier Indonesia" />
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
                    <input type="text" wire:model="telepon" class="form-control" placeholder="08123456789" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" wire:model="email" class="form-control" placeholder="supplier@email.com" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Alamat</label>
                <textarea wire:model="alamat" class="form-control" rows="2"
                  placeholder="Alamat lengkap supplier"></textarea>
              </div>
              <div class="form-group">
                <label>Keterangan</label>
                <textarea wire:model="supplier_keterangan" class="form-control" rows="2"
                  placeholder="Catatan tambahan"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" wire:click="closeSupplierModal">
                Batal
              </button>
              <button type="button" class="btn btn-primary" wire:click="saveSupplier">
                Simpan
              </button>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>

    <!-- Supplier Modal (duplicate) - REMOVED -->
  </div>
</div>

<script>
  function addBatchRow() {
    // This will trigger a Livewire update to add a new batch row
    @this.call('addBatchCreatorRow');
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-batch-row')) {
        const index = e.target.getAttribute('data-index');
        @this.call('removeBatchCreatorRow', index);
    }
});
</script>
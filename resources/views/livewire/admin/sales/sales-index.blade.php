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
                <div class="row">
                  <div class="col-md-4">
                    <select wire:model="tanggal"
                      class="form-control form-control-sm @error('tanggal_penjualan') is-invalid @enderror">
                      <option value="">-- Hari --</option>
                      @for ($d = 1; $d <= 31; $d++) <option value="{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}">
                        {{ str_pad($d, 2, '0', STR_PAD_LEFT) }}
                        </option>
                        @endfor
                    </select>
                  </div>
                  <div class="col-md-4">
                    <select wire:model="bulan"
                      class="form-control form-control-sm @error('tanggal_penjualan') is-invalid @enderror">
                      <option value="">-- Bulan --</option>
                      <option value="01">Januari</option>
                      <option value="02">Februari</option>
                      <option value="03">Maret</option>
                      <option value="04">April</option>
                      <option value="05">Mei</option>
                      <option value="06">Juni</option>
                      <option value="07">Juli</option>
                      <option value="08">Agustus</option>
                      <option value="09">September</option>
                      <option value="10">Oktober</option>
                      <option value="11">November</option>
                      <option value="12">Desember</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <select wire:model="tahun"
                      class="form-control form-control-sm @error('tanggal_penjualan') is-invalid @enderror">
                      <option value="">-- Tahun --</option>
                      @for ($y = now()->year; $y >= now()->year - 10; $y--)
                      <option value="{{ $y }}">{{ $y }}</option>
                      @endfor
                    </select>
                  </div>
                </div>
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
                  <select wire:model.live="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($customers as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->nama_pelanggan }}</option>
                    @endforeach
                  </select>
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-success" wire:click="openCreateCustomerModal"
                      title="Tambah Pelanggan">
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
                    <input class="form-check-input" type="radio" wire:model="location_source"
                      wire:change="selectLocationSource($event.target.value)" value="toko" id="locationToko" />
                    <label class="form-check-label" for="locationToko">
                      <i class="fas fa-store text-info"></i>
                      Toko
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" wire:model="location_source"
                      wire:change="selectLocationSource($event.target.value)" value="gudang" id="locationGudang" />
                    <label class="form-check-label" for="locationGudang">
                      <i class="fas fa-warehouse text-success"></i>
                      Gudang
                    </label>
                  </div>
                </div>

                @if ($location_source === 'toko')
                <select wire:model="store_id" wire:change="refreshBatchesForStoreChange()"
                  class="form-control @error('store_id') is-invalid @enderror">
                  <option value="">-- Pilih Toko --</option>
                  @foreach ($stores as $store)
                  <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                  @endforeach
                </select>
                @error('store_id')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
                @enderror
                @else
                <select wire:model="warehouse_id" wire:change="refreshBatchesForWarehouseChange()"
                  class="form-control @error('warehouse_id') is-invalid @enderror">
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
                <select wire:model="status" class="form-control @error('status') is-invalid @enderror">
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
            <textarea wire:model="keterangan" class="form-control" rows="2"
              placeholder="Catatan penjualan..."></textarea>
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
                  <button type="button" class="close" wire:click="$set('showCreateCustomerModal', false)">
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
                    <textarea wire:model.defer="new_customer_alamat" class="form-control" rows="2"></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-success" wire:click.prevent="saveNewCustomer">
                    Simpan
                  </button>
                  <button class="btn btn-secondary" wire:click="$set('showCreateCustomerModal', false)">
                    Batal
                  </button>
                </div>
              </div>
            </div>
          </div>
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
                      <select wire:model.live="saleItems.{{ $index }}.category_id" class="form-control form-control-sm">
                        <option value="">-- Pilih --</option>
                        @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select wire:model.live="saleItems.{{ $index }}.subcategory_id"
                        class="form-control form-control-sm">
                        <option value="">-- Pilih --</option>
                        @if (isset($item['category_id']) && $item['category_id'])
                        @foreach ($subcategories->where('kategori_id', $item['category_id']) as $sub)
                        <option value="{{ $sub->id }}">
                          {{ $sub->nama_subkategori }}
                        </option>
                        @endforeach
                        @endif
                      </select>
                    </td>
                    <td>
                      <input type="text" wire:model.live="saleItems.{{ $index }}.product_name"
                        class="form-control form-control-sm product-search-input" placeholder="Cari produk..."
                        autocomplete="off" />
                      @if (isset($item['product_suggestions']) && count($item['product_suggestions']) > 0)
                      <div class="product-suggestions mt-1"
                        style="position: relative; z-index: 10; background: white; border: 1px solid #ddd; border-radius: 0.25rem; max-height: 200px; overflow-y: auto;">
                        @foreach ($item['product_suggestions'] as $suggestion)
                        <div class="suggestion-item p-2 border-bottom cursor-pointer hover:bg-light"
                          wire:click="selectProduct({{ $index }}, {{ $suggestion['id'] }})"
                          style="cursor: pointer; padding: 0.5rem; border-bottom: 1px solid #eee;"
                          onmouseover="this.style.backgroundColor='#f5f5f5'"
                          onmouseout="this.style.backgroundColor='white'">
                          <strong>{{ $suggestion['nama_produk'] }}</strong><br />
                          <small class="text-muted">({{ $suggestion['kode_produk'] }})</small>
                        </div>
                        @endforeach
                      </div>
                      @endif
                    </td>
                    <td>
                      <select wire:model.live="saleItems.{{ $index }}.batch_id" class="form-control form-control-sm">
                        <option value="">-- Pilih Batch --</option>
                        @if (isset($item['available_batches']) && count($item['available_batches']) > 0)
                        @foreach ($item['available_batches'] as $batch)
                        <option value="{{ $batch['id'] }}">
                          {{ $batch['batch_number'] }} (Stok:
                          {{ $batch['available_qty'] }})
                        </option>
                        @endforeach
                        @endif
                      </select>
                    </td>
                    <td>
                      <input type="number" wire:model.live="saleItems.{{ $index }}.quantity"
                        class="form-control form-control-sm" min="0" step="0.01" />
                    </td>
                    <td>
                      <select wire:model.live="saleItems.{{ $index }}.unit_id" class="form-control form-control-sm">
                        <option value="">-- Unit --</option>
                        @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" wire:model.live="saleItems.{{ $index }}.selling_price"
                          class="form-control form-control-sm" min="0" />
                      </div>
                    </td>
                    <td class="text-right">
                      <strong>
                        Rp {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                      </strong>
                    </td>
                    <td class="text-center">
                      <button wire:click="removeItem({{ $index }})" type="button" class="btn btn-danger btn-sm">
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
                        <input type="number" wire:model.live="kuli" class="form-control form-control-sm" min="0" />
                      </div>
                    </td>
                    <td colspan="2"></td>
                  </tr>
                  <tr>
                    <td colspan="7" class="text-right pr-3">TOTAL:</td>
                    <td colspan="2" class="text-right text-success">
                      <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
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

        <!-- Action Buttons -->
        <div class="mt-3 p-2 border-top bg-light">
          <button wire:click.prevent="addItem" type="button" class="btn btn-info btn-sm mr-2">
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
              <input type="text" wire:model.live="search" class="form-control"
                placeholder="Cari no invoice, nama pelanggan..." />
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
                    <span class="badge badge-success">
                      {{ $sale->warehouse?->nama_gudang }}
                    </span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    {{ $sale->saleItems->count() }}
                  </td>
                  <td class="text-right">
                    <strong>
                      Rp {{ number_format($sale->grand_total ?? 0, 0, ',', '.') }}
                    </strong>
                  </td>
                  <td>
                    @switch($sale->status)
                    @case('pending')
                    <span class="badge badge-warning">Pending</span>

                    @break
                    @case('hold')
                    <span class="badge badge-info">Hold</span>

                    @break
                    @case('completed')
                    <span class="badge badge-success">Completed</span>

                    @break
                    @case('cancelled')
                    <span class="badge badge-danger">Cancelled</span>

                    @break
                    @endswitch
                  </td>
                  <td>
                    <div class="btn-group">
                      <button wire:click="edit({{ $sale->id }})" class="btn btn-sm btn-primary" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button wire:click="show({{ $sale->id }})" class="btn btn-sm btn-info" title="Lihat Detail">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button wire:click="delete({{ $sale->id }})" class="btn btn-sm btn-danger" title="Hapus"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus penjualan ini?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
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
  <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5)" tabindex="-1">
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
{{ $stockWarningMessage }}</pre>
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
  <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5)" tabindex="-1">
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
                      <td width="100"><strong>No. Invoice:</strong></td>
                      <td>{{ $no_invoice ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td><strong>Tanggal:</strong></td>
                      <td>
                        {{ $tanggal_penjualan ? \Carbon\Carbon::parse($tanggal_penjualan)->format('d/m/Y') : '-' }}
                      </td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-6">
                  <table class="table table-borderless table-sm">
                    <tr>
                      <td width="100"><strong>Pelanggan:</strong></td>
                      <td>{{ $customer_name ?? '-' }}</td>
                    </tr>
                    <tr>
                      <td><strong>Lokasi:</strong></td>
                      <td>{{ $location_name ?? '-' }}</td>
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
                  <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] ?? '-' }}</td>
                    <td class="text-center">{{ $item['quantity'] ?? 0 }}</td>
                    <td class="text-center">{{ $item['unit_name'] ?? '-' }}</td>
                    <td>{{ $item['batch_number'] ?? '-' }}</td>
                  </tr>
                  @endif
                  @endforeach
                </tbody>
              </table>

              <!-- Catatan -->
              <div class="form-group mt-3">
                <label><strong>Catatan Pengiriman:</strong></label>
                <textarea wire:model="deliveryNotes" class="form-control" rows="3"
                  placeholder="Masukkan catatan khusus pengiriman (opsional)"></textarea>
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
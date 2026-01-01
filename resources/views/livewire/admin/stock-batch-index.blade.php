<div>
  <style>
    /* Stock Batch Table Styling */
    .batch-table thead th {
      position: sticky;
      top: 0;
      z-index: 10;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 0.75rem 0.5rem;
      white-space: nowrap;
      border-bottom: 2px solid rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    /* Header normal state */
    .batch-table thead.header-normal th {
      background-color: #007bff;
      color: #fff;
    }
    /* Header scrolled state - darker with shadow */
    .batch-table thead.header-scrolled th {
      background: linear-gradient(135deg, #0056b3 0%, #004494 100%);
      color: #fff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
      border-bottom: 3px solid #ffc107;
    }
    .batch-table tbody td {
      font-size: 0.85rem;
      padding: 0.5rem;
      vertical-align: middle;
    }
    .batch-table tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.08) !important;
    }
    .batch-table .badge {
      font-size: 0.75rem;
      padding: 0.35em 0.65em;
    }
    /* Product header row */
    .product-header-row {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
      border-left: 4px solid #007bff !important;
    }
    .product-header-row:hover {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }
    /* Batch row indent */
    .batch-row {
      background-color: #fff;
    }
    .batch-row:nth-child(even) {
      background-color: #fafbfc;
    }
    /* Scrollable table container */
    .table-scroll-wrapper {
      max-height: 600px;
      overflow-y: auto;
    }
    /* Card improvements */
    .card-tools .btn {
      margin-left: 5px;
    }
  </style>

  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('message') }}
    </div>
  @endif

  <!-- Content Header with Navigation -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center"
      >
        <div>
          <h2 class="mb-0">
            <i class="fas fa-layer-group mr-2"></i>
            Manajemen Tumpukan Stok
          </h2>
          <small class="text-muted">Kelola stok per tumpukan (A, B, C, dst)</small>
        </div>
        <div class="mt-2 mt-md-0">
          <a href="{{ route('admin.stock-reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i>
            Kembali ke Laporan
          </a>
        </div>
      </div>
      <hr />
    </div>
  </div>

  <!-- Daftar Stok Tumpukan Card -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-boxes mr-2"></i>
            Daftar Stok Tumpukan
          </h3>
          <div class="card-tools">
            @if (count($this->selectedBatches) > 0)
              <button
                class="btn btn-sm btn-danger"
                onclick="if(confirm('Hapus {{ count($this->selectedBatches) }} batch secara permanen?')) { @this.deleteSelected(); }"
                title="Hapus pilihan"
              >
                <i class="fas fa-trash"></i>
                Hapus {{ count($this->selectedBatches) }}
              </button>
              <button class="btn btn-sm btn-secondary" wire:click="clearSelection">
                <i class="fas fa-times"></i>
                Batal
              </button>
            @endif

            @if (! $this->showCreateForm && ! $this->showCreateHoldForm)
              <button class="btn btn-sm btn-warning" wire:click="openCreateHoldForm">
                <i class="fas fa-hand-paper"></i>
                Buat Tumpukan Hold
              </button>
              <button class="btn btn-sm btn-success" wire:click="openCreateForm">
                <i class="fas fa-plus-circle"></i>
                Buat Tumpukan Baru
              </button>
            @else
              <button class="btn btn-sm btn-secondary" wire:click="closeCreateForm">
                <i class="fas fa-times"></i>
                Batal
              </button>
            @endif
          </div>
        </div>
        <div class="card-body">
          <!-- Inline Create Form -->
          @if ($this->showCreateForm)
            <div class="alert alert-info alert-dismissible mb-3">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
              </button>
              <h5>
                <i class="fas fa-info-circle"></i>
                Form Buat Tumpukan Baru
              </h5>
            </div>

            <form wire:submit.prevent="createStockBatch" class="mb-4">
              <div class="row">
                <!-- 1. Tanggal -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>Tanggal</strong>
                    </label>
                    <input
                      type="date"
                      wire:model="createDate"
                      class="form-control @error('createDate') is-invalid @enderror"
                    />
                    @error('createDate')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- 2. Kategori -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Kategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>

                    <div class="input-group">
                      <select
                        wire:model.live="createCategoryId"
                        class="form-control @error('createCategoryId') is-invalid @enderror"
                      >
                        <option value="">-- Pilih --</option>
                        @foreach ($categories as $cat)
                          <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                        <option value="__add__">+ Tambah Kategori...</option>
                      </select>
                    </div>

                    @if ($showCreateCategoryInline)
                      <div class="mt-2 p-2 border rounded" style="background-color: #f8f9fa;">
                        <div class="form-group mb-2">
                          <label class="small"><strong>Kode (opsional)</strong></label>
                          <input type="text" wire:model.defer="newCreateCategoryCode" class="form-control form-control-sm" placeholder="Contoh: BERAS" />
                          @error('newCreateCategoryCode')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                          @enderror
                        </div>
                        <div class="form-group mb-2">
                          <label class="small"><strong>Nama Kategori</strong></label>
                          <input type="text" wire:model.defer="newCreateCategoryName" class="form-control form-control-sm" placeholder="Contoh: Beras Putih" />
                          @error('newCreateCategoryName')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                          @enderror
                        </div>
                        <div class="form-group mb-0">
                          <button class="btn btn-success btn-sm" wire:click.prevent="createInlineCategory">
                            <i class="fas fa-check"></i> Tambah
                          </button>
                          <button class="btn btn-secondary btn-sm" wire:click.prevent="$set('showCreateCategoryInline', false)">
                            <i class="fas fa-times"></i> Batal
                          </button>
                        </div>
                      </div>
                    @endif

                    @error('createCategoryId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 3. Subkategori -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Subkategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>

                    <div class="input-group">
                      <select
                        wire:model="createSubcategoryId"
                        class="form-control @error('createSubcategoryId') is-invalid @enderror"
                      >
                        <option value="">-- Pilih --</option>
                        @forelse ($this->filteredSubcategories as $sub)
                          <option value="{{ $sub->id }}">{{ $sub->nama_subkategori }}</option>
                        @empty
                          @if ($createCategoryId)
                            <option value="" disabled>Tidak ada subkategori</option>
                          @endif
                        @endforelse
                        <option value="__add__">+ Tambah Subkategori...</option>
                      </select>
                    </div>

                    @if ($showCreateSubcategoryInline)
                      <div class="mt-2 p-2 border rounded" style="background-color: #f8f9fa;">
                        <div class="form-group mb-2">
                          <label class="small"><strong>Kode (opsional)</strong></label>
                          <input type="text" wire:model.defer="newCreateSubcategoryCode" class="form-control form-control-sm" placeholder="Contoh: BR_PUTIH" />
                          @error('newCreateSubcategoryCode')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                          @enderror
                        </div>
                        <div class="form-group mb-2">
                          <label class="small"><strong>Nama Subkategori</strong></label>
                          <input type="text" wire:model.defer="newCreateSubcategoryName" class="form-control form-control-sm" placeholder="Contoh: Beras Putih Premium" />
                          @error('newCreateSubcategoryName')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                          @enderror
                        </div>
                        @if (!$createCategoryId)
                          <div class="alert alert-info alert-sm mb-2" role="alert">
                            <small>Pilih kategori terlebih dahulu agar subkategori terkait dibuat.</small>
                          </div>
                        @endif
                        <div class="form-group mb-0">
                          <button class="btn btn-success btn-sm" wire:click.prevent="createInlineSubcategory">
                            <i class="fas fa-check"></i> Tambah
                          </button>
                          <button class="btn btn-secondary btn-sm" wire:click.prevent="$set('showCreateSubcategoryInline', false)">
                            <i class="fas fa-times"></i> Batal
                          </button>
                        </div>
                      </div>
                    @endif

                    @error('createSubcategoryId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 4. Produk -->
                <div
                  class="col-12 col-md-2"
                  x-data="productDropdown()"
                  @click.outside="showDropdown = false"
                >
                  <div class="form-group">
                    <label>
                      <strong>
                        Produk
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <div class="position-relative">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input
                          type="text"
                          id="productSearch"
                          @input="search = $event.target.value; filter(); showDropdown = search.length > 0;"
                          @focus="filter(); showDropdown = true;"
                          @blur="setTimeout(() => showDropdown = false, 200)"
                          @keydown.enter="selectFirst()"
                          class="form-control @error('createProductId') is-invalid @enderror"
                          placeholder="Cari produk... (nama/kode)"
                          autocomplete="off"
                        />
                      </div>
                      <!-- Dropdown suggestions -->
                      <div
                        class="position-absolute w-100 mt-0 bg-white border border-top-0 rounded-bottom shadow-sm"
                        style="
                          top: 100%;
                          left: 0;
                          max-height: 250px;
                          overflow-y: auto;
                          z-index: 1000;
                        "
                        x-show="showDropdown && filtered.length > 0"
                        id="productDropdown"
                      >
                        <template x-for="product in filtered" :key="product.id">
                          <div
                            class="product-option p-2 cursor-pointer"
                            @click="selectProduct(product); showDropdown = false;"
                            style="border-bottom: 1px solid #e3e6f0; padding: 0.75rem 1rem"
                            @mouseover="$el.classList.add('bg-light')"
                            @mouseout="$el.classList.remove('bg-light')"
                          >
                            <div
                              style="font-size: 0.875rem; font-weight: 500"
                              x-text="product.nama_produk"
                            ></div>
                          </div>
                        </template>
                        <!-- Tambah Produk Button -->
                        <div class="p-2 text-center border-top" style="background-color: #f8f9fa">
                          <button
                            type="button"
                            class="btn btn-sm btn-success"
                            @click="showDropdown = false; $wire.openQuickAddProductModal()"
                          >
                            <i class="fas fa-plus-circle mr-1"></i>
                            Tambah Item Produk Baru
                          </button>
                        </div>
                      </div>
                    </div>
                    @if ($createProductId)
                      <small class="text-success d-block mt-1">
                        <i class="fas fa-check-circle"></i>
                        Produk terpilih
                      </small>
                    @endif

                    @error('createProductId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
                <!-- 5. Lokasi (Toko/Gudang) -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Lokasi
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="createLocationType"
                      class="form-control @error('createLocationType') is-invalid @enderror"
                    >
                      <option value="store">Toko</option>
                      <option value="warehouse">Gudang</option>
                    </select>
                    @error('createLocationType')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 6. Toko atau Gudang -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        {{ $createLocationType === 'store' ? 'Toko' : 'Gudang' }}
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model="createLocationId"
                      class="form-control @error('createLocationId') is-invalid @enderror"
                    >
                      <option value="">
                        -- Pilih {{ $createLocationType === 'store' ? 'Toko' : 'Gudang' }} --
                      </option>
                      @if ($createLocationType === 'store')
                        @foreach ($stores as $store)
                          <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                        @endforeach
                      @else
                        @foreach ($warehouses as $warehouse)
                          <option value="{{ $warehouse->id }}">
                            {{ $warehouse->nama_gudang }}
                          </option>
                        @endforeach
                      @endif
                    </select>
                    @error('createLocationId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- 7. Nama Tumpukan -->
                <div class="col-12 col-md-3">
                  <div class="form-group">
                    <label>
                      <strong>
                        Nama Tumpukan
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="text"
                      wire:model="createNamaTumpukan"
                      class="form-control @error('createNamaTumpukan') is-invalid @enderror"
                      placeholder="A, B, C..."
                      maxlength="50"
                    />
                    @error('createNamaTumpukan')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 8. Qty -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Qty
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="number"
                      wire:model="createQty"
                      class="form-control @error('createQty') is-invalid @enderror"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                    />
                    @error('createQty')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 9. Catatan -->
                <div class="col-12 col-md-7">
                  <div class="form-group">
                    <label>
                      <strong>Catatan</strong>
                    </label>
                    <input
                      type="text"
                      wire:model="createNote"
                      class="form-control"
                      placeholder="Catatan (optional)"
                    />
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-success btn-sm px-4">
                    <i class="fas fa-save"></i>
                    Simpan
                  </button>
                  <button
                    type="button"
                    class="btn btn-secondary btn-sm px-4"
                    wire:click="closeCreateForm"
                  >
                    <i class="fas fa-times"></i>
                    Batal
                  </button>
                </div>
              </div>
            </form>

            <hr />
          @endif

          <!-- Inline Create Hold Form -->
          @if ($this->showCreateHoldForm)
            <div class="alert alert-warning alert-dismissible mb-3">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                &times;
              </button>
              <h5>
                <i class="fas fa-hand-paper"></i>
                Form Buat Tumpukan Hold
              </h5>
              <p class="mb-0">Tumpukan ini akan dibuat dengan status HOLD untuk menyimpan stok yang ditahan sementara.</p>
            </div>

            <form wire:submit.prevent="createHoldBatch" class="mb-4">
              <div class="row">
                <!-- 1. Tanggal -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>Tanggal</strong>
                    </label>
                    <input
                      type="date"
                      wire:model="createDate"
                      class="form-control"
                      value="{{ date('Y-m-d') }}"
                    />
                  </div>
                </div>

                <!-- 2. Kategori -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Kategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="holdCategoryId"
                      class="form-control @error('holdCategoryId') is-invalid @enderror"
                    >
                      <option value="">Pilih Kategori</option>
                      @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                      @endforeach
                      <option value="__add__">+ Tambah Kategori...</option>
                    </select>
                    @error('holdCategoryId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 3. Subkategori -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Subkategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="holdSubcategoryId"
                      class="form-control @error('holdSubcategoryId') is-invalid @enderror"
                      @if(!$holdCategoryId) disabled @endif
                    >
                      <option value="">Pilih Subkategori</option>
                      @if($holdCategoryId && $holdCategoryId !== '__add__')
                        @foreach($subcategories->where('category_id', $holdCategoryId) as $subcategory)
                          <option value="{{ $subcategory->id }}">{{ $subcategory->nama_subkategori }}</option>
                        @endforeach
                      @endif
                      <option value="__add__">+ Tambah Subkategori...</option>
                    </select>
                    @error('holdSubcategoryId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 4. Produk -->
                <div
                  class="col-12 col-md-3"
                  x-data="holdProductDropdown()"
                  @click.outside="showDropdown = false"
                >
                  <div class="form-group">
                    <label>
                      <strong>
                        Produk
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <div class="position-relative">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input
                          type="text"
                          id="holdProductSearch"
                          @input="search = $event.target.value; filter(); showDropdown = search.length > 0;"
                          @focus="filter(); showDropdown = true;"
                          @blur="setTimeout(() => showDropdown = false, 200)"
                          @keydown.enter="selectFirst()"
                          class="form-control @error('holdProductId') is-invalid @enderror"
                          placeholder="Cari produk... (nama/kode)"
                          autocomplete="off"
                        />
                      </div>
                      <!-- Dropdown suggestions -->
                      <div
                        class="position-absolute w-100 mt-0 bg-white border border-top-0 rounded-bottom shadow-sm"
                        style="
                          top: 100%;
                          left: 0;
                          max-height: 250px;
                          overflow-y: auto;
                          z-index: 1000;
                        "
                        x-show="showDropdown && filtered.length > 0"
                        id="holdProductDropdown"
                      >
                        <template x-for="product in filtered" :key="product.id">
                          <div
                            class="product-option p-2 cursor-pointer"
                            @click="selectProduct(product); showDropdown = false;"
                            style="border-bottom: 1px solid #e3e6f0; padding: 0.75rem 1rem"
                            @mouseover="$el.classList.add('bg-light')"
                            @mouseout="$el.classList.remove('bg-light')"
                          >
                            <div
                              style="font-size: 0.875rem; font-weight: 500"
                              x-text="product.nama_produk"
                            ></div>
                          </div>
                        </template>
                      </div>
                    </div>
                    @if ($holdProductId)
                      <small class="text-success d-block mt-1">
                        <i class="fas fa-check-circle"></i>
                        Produk terpilih
                      </small>
                    @endif

                    @error('holdProductId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 5. Lokasi (Toko/Gudang) -->
                <div class="col-12 col-md-1">
                  <div class="form-group">
                    <label>
                      <strong>
                        Lokasi
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="holdLocationType"
                      class="form-control @error('holdLocationType') is-invalid @enderror"
                    >
                      <option value="store">Toko</option>
                      <option value="warehouse">Gudang</option>
                    </select>
                    @error('holdLocationType')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 6. Toko atau Gudang -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        <span x-text="$wire.holdLocationType === 'store' ? 'Toko' : 'Gudang'"></span>
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="holdLocationId"
                      class="form-control @error('holdLocationId') is-invalid @enderror"
                    >
                      <option value="">
                        <span x-text="$wire.holdLocationType === 'store' ? 'Pilih Toko' : 'Pilih Gudang'"></span>
                      </option>
                      @if($holdLocationType === 'store')
                        @foreach($stores as $store)
                          <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                        @endforeach
                      @elseif($holdLocationType === 'warehouse')
                        @foreach($warehouses as $warehouse)
                          <option value="{{ $warehouse->id }}">{{ $warehouse->nama_gudang }}</option>
                        @endforeach
                      @endif
                    </select>
                    @error('holdLocationId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- 7. Nama Tumpukan -->
                <div class="col-12 col-md-3">
                  <div class="form-group">
                    <label>
                      <strong>
                        Nama Tumpukan
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model="holdNamaTumpukan"
                      class="form-control @error('holdNamaTumpukan') is-invalid @enderror"
                    >
                      <option value="">
                        @if($holdProductId)
                          Pilih Nama Tumpukan
                        @else
                          Pilih produk terlebih dahulu
                        @endif
                      </option>
                      @if($holdProductId)
                        @foreach($holdBatchOptions as $batchName)
                          <option value="{{ $batchName }}">{{ $batchName }}</option>
                        @endforeach
                      @endif
                    </select>
                    @error('holdNamaTumpukan')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 8. Qty -->
                <div class="col-12 col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Qty
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="number"
                      wire:model="holdQty"
                      class="form-control @error('holdQty') is-invalid @enderror"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                    />
                    @error('holdQty')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 9. Satuan -->
                <div class="col-12 col-md-1">
                  <div class="form-group">
                    <label>
                      <strong>Satuan</strong>
                    </label>
                    <input
                      type="text"
                      wire:model="holdSatuan"
                      class="form-control"
                      placeholder="kg"
                      maxlength="50"
                    />
                  </div>
                </div>

                <!-- 10. Alasan Hold -->
                <div class="col-12 col-md-3">
                  <div class="form-group">
                    <label>
                      <strong>
                        Alasan Hold
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="text"
                      wire:model="holdReason"
                      class="form-control @error('holdReason') is-invalid @enderror"
                      placeholder="Contoh: Menunggu konfirmasi pembayaran"
                      maxlength="255"
                    />
                    @error('holdReason')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <!-- 11. Catatan -->
                <div class="col-12 col-md-3">
                  <div class="form-group">
                    <label>
                      <strong>Catatan</strong>
                    </label>
                    <input
                      type="text"
                      wire:model="holdNote"
                      class="form-control"
                      placeholder="Catatan tambahan (optional)"
                    />
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <button 
                    type="submit" 
                    class="btn btn-warning btn-sm px-4"
                    wire:loading.attr="disabled"
                    wire:target="createHoldBatch"
                    :disabled="$wire.isCreatingHoldBatch"
                  >
                    <i class="fas fa-hand-paper"></i>
                    <span wire:loading.remove wire:target="createHoldBatch">Buat Tumpukan Hold</span>
                    <span wire:loading wire:target="createHoldBatch">
                      <i class="fas fa-spinner fa-spin"></i> Menyimpan...
                    </span>
                  </button>
                  <button
                    type="button"
                    class="btn btn-secondary btn-sm px-4"
                    wire:click="closeCreateHoldForm"
                    wire:loading.attr="disabled"
                    wire:target="createHoldBatch"
                  >
                    <i class="fas fa-times"></i>
                    Batal
                  </button>
                </div>
              </div>
            </form>

            <hr />
          @endif

          <!-- Filter & Search Card (always shown) -->
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="card card-outline card-primary shadow-sm">
                <div class="card-header py-2">
                  <h3 class="card-title">
                    <i class="fas fa-filter mr-2"></i>
                    Filter & Pencarian
                  </h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body py-3">
                  <div class="row align-items-end">
                    <!-- Search Input -->
                    <div class="col-lg-6 col-md-6 mb-2 mb-lg-0">
                      <label class="mb-1 text-sm font-weight-bold">
                        <i class="fas fa-search text-muted mr-1"></i>
                        Cari Produk
                      </label>
                      <div class="input-group">
                        <input
                          type="text"
                          wire:model.live.debounce.300ms="search"
                          class="form-control"
                          placeholder="Ketik nama atau kode produk..."
                        />
                        @if ($search)
                          <div class="input-group-append">
                            <button
                              class="btn btn-outline-secondary"
                              type="button"
                              wire:click="$set('search', '')"
                            >
                              <i class="fas fa-times"></i>
                            </button>
                          </div>
                        @endif
                      </div>
                    </div>

                    <!-- Location Filter -->
                    <div class="col-lg-3 col-md-3 mb-2 mb-lg-0">
                      <label class="mb-1 text-sm font-weight-bold">
                        <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                        Lokasi
                      </label>
                      <select wire:model.live="location" class="form-control">
                        <option value="">Semua Lokasi</option>
                        @foreach ($locations as $key => $label)
                          <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                      </select>
                    </div>

                    <!-- Per Page -->
                    <div class="col-lg-2 col-md-2 mb-2 mb-lg-0">
                      <label class="mb-1 text-sm font-weight-bold">
                        <i class="fas fa-list-ol text-muted mr-1"></i>
                        Baris
                      </label>
                      <select wire:model.live="per_page" class="form-control">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                      </select>
                    </div>

                    <!-- Reset Button -->
                    <div class="col-lg-1 col-md-1 mb-2 mb-lg-0">
                      <label class="mb-1 text-sm d-none d-lg-block">&nbsp;</label>
                      <button
                        type="button"
                        class="btn btn-outline-secondary btn-block"
                        wire:click="resetMainFilters"
                        title="Reset semua filter"
                      >
                        <i class="fas fa-redo-alt"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Active Filters Info -->
                  @if ($search || $location)
                    <div class="mt-3 pt-2 border-top">
                      <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Filter aktif:
                        @if ($search)
                          <span class="badge badge-primary ml-1">
                            Pencarian: "{{ $search }}"
                            <a
                              href="#"
                              wire:click.prevent="$set('search', '')"
                              class="text-white ml-1"
                            >
                              &times;
                            </a>
                          </span>
                        @endif

                        @if ($location)
                          <span class="badge badge-info ml-1">
                            Lokasi: {{ $locations[$location] ?? $location }}
                            <a
                              href="#"
                              wire:click.prevent="$set('location', '')"
                              class="text-white ml-1"
                            >
                              &times;
                            </a>
                          </span>
                        @endif
                      </small>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          @if ($batches->count() > 0)
            <div
              class="table-responsive table-scroll-wrapper"
              x-data="{ isScrolled: false }"
              x-on:scroll="isScrolled = $el.scrollTop > 10"
            >
              <table class="table table-sm table-bordered mb-0 batch-table">
                <thead :class="isScrolled ? 'header-scrolled' : 'header-normal'">
                  <tr>
                    <th class="text-center" style="width: 40px">
                      <input
                        type="checkbox"
                        wire:click="$toggle('selectAll')"
                        wire:model="selectAll"
                        title="Select all"
                      />
                    </th>
                    <th class="text-center" style="width: 50px">No</th>
                    <th style="min-width: 180px">Nama Tumpukan</th>
                    <th class="text-center" style="width: 100px">Lokasi</th>
                    <th class="text-center" style="width: 100px">Qty</th>
                    <th class="text-center" style="width: 80px">Satuan</th>
                    <th class="text-center" style="min-width: auto">Catatan</th>
                    <th class="text-center" style="width: 100px">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $groupedBatches = $batches->groupBy('product_id');
                  @endphp

                  @foreach ($groupedBatches as $productId => $productBatches)
                    @php
                      $firstBatch = $productBatches->first();
                      $product = $firstBatch->product;
                      $totalQty = $productBatches->sum('qty');
                    @endphp

                    <!-- Product Header Row -->
                    <tr class="product-header-row">
                      <td class="text-center" style="width: 40px">
                        <span class="badge badge-light text-dark border">{{ $productNumbers[$productId] ?? '-' }}</span>
                      </td>
                      <td colspan="7" class="py-2">
                        <div class="d-flex align-items-center justify-content-between">
                          <div>
                            <i class="fas fa-box mr-2 text-primary"></i>
                            <strong class="text-dark">{{ $product->nama_produk ?? 'N/A' }}</strong>
                          </div>
                          <div>
                            <span class="badge badge-secondary">
                              <i class="fas fa-folder fa-xs mr-1"></i>
                              {{ $product->category->nama_kategori ?? '-' }}
                            </span>
                            <span class="badge badge-info">
                              <i class="fas fa-tag fa-xs mr-1"></i>
                              {{ $product->subcategory->nama_subkategori ?? '-' }}
                            </span>
                            <span class="badge badge-primary">
                              <i class="fas fa-layer-group fa-xs mr-1"></i>
                              {{ $productBatches->count() }} Batch
                            </span>
                            <span class="badge badge-success px-2">
                              <i class="fas fa-cubes fa-xs mr-1"></i>
                              Total: {{ number_format($totalQty, 0) }}
                              {{ $product->satuan ?? '' }}
                            </span>
                          </div>
                        </div>
                      </td>
                    </tr>

                    <!-- Batch Rows -->
                    @foreach ($productBatches as $batch)
                      <tr
                        class="batch-row @if(in_array($batch->id, $this->selectedBatches)) table-warning @endif"
                      >
                        <td class="text-center">
                          <input
                            type="checkbox"
                            wire:click="toggleSelectBatch({{ $batch->id }})"
                            @if(in_array($batch->id, $this->selectedBatches)) checked @endif
                            title="Select batch"
                          />
                        </td>
                        <td class="text-center">
                          <!-- Empty for batch rows (No hanya untuk product header) -->
                        </td>
                        <td class="pl-4">
                          <i class="fas fa-angle-right mr-2 text-muted"></i>
                          <strong class="text-dark">{{ $batch->nama_tumpukan }}</strong>
                          @if($batch->status === 'hold')
                            <span class="badge badge-warning ml-2">
                              <i class="fas fa-hand-paper fa-xs mr-1"></i>
                              HOLD
                            </span>
                          @endif
                          {{--
                            @if ($batch->note)
                            <br />
                            <small class="text-muted pl-4">
                            <i class="fas fa-sticky-note fa-xs"></i>
                            {{ Str::limit($batch->note, 40) }}
                            </small>
                            @endif
                          --}}
                        </td>
                        <td class="text-center">
                          @if ($batch->location_type === 'store')
                            <span class="badge badge-primary">
                              <i class="fas fa-store fa-xs mr-1"></i>
                              Toko
                            </span>
                          @else
                            <span class="badge badge-success">
                              <i class="fas fa-warehouse fa-xs mr-1"></i>
                              Gudang
                            </span>
                          @endif
                        </td>
                        <td class="text-center">
                          <strong class="text-primary" style="font-size: 1rem">
                            {{ number_format($batch->qty, 0) }}
                          </strong>
                        </td>
                        <td class="text-center">
                          <small class="text-muted" style="font-size: 15px">
                            {{ $batch->product->satuan ?? '-' }}
                          </small>
                        </td>
                        <td>
                          @if ($batch->note)
                            <span class="text-dark">
                              <i class="fas fa-sticky-note text-warning fa-sm mr-1"></i>
                              {{ Str::limit($batch->note, 50) }}
                            </span>
                          @else
                            <small class="text-muted text-italic">-</small>
                          @endif
                        </td>
                        <td class="text-center">
                          <div class="btn-group btn-group-sm" role="group">
                            <button
                              type="button"
                              wire:click="editBatch({{ $batch->id }})"
                              class="btn btn-outline-primary"
                              title="Edit"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              onclick="confirmDelete({{ $batch->id }})"
                              class="btn btn-outline-danger"
                              title="Hapus"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="row mt-4">
              <div class="col-sm-12 col-md-5">
                <div class="dataTables_info" role="status" aria-live="polite">
                  Menampilkan {{ $batches->firstItem() }} sampai {{ $batches->lastItem() }} dari
                  {{ $batches->total() }} entri
                </div>
              </div>
              <div class="col-sm-12 col-md-7">
                <div class="dataTables_paginate paging_simple_numbers float-right">
                  @if ($batches->hasPages())
                    <ul class="pagination">
                      {{-- First Page Link --}}
                      @if ($batches->onFirstPage())
                        <li class="paginate_button page-item previous disabled">
                          <span class="page-link"><i class="fas fa-angle-double-left"></i></span>
                        </li>
                      @else
                        <li class="paginate_button page-item previous">
                          <button type="button" wire:click="gotoPage(1)" class="page-link">
                            <i class="fas fa-angle-double-left"></i>
                          </button>
                        </li>
                      @endif

                      {{-- Pagination Elements --}}
                      @php
                        $start = max($batches->currentPage() - 2, 1);
                        $end = min($start + 4, $batches->lastPage());
                        $start = max($end - 4, 1);
                      @endphp

                      @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $batches->currentPage())
                          <li class="paginate_button page-item active">
                            <span class="page-link">{{ $i }}</span>
                          </li>
                        @else
                          <li class="paginate_button page-item">
                            <button
                              type="button"
                              wire:click="gotoPage({{ $i }})"
                              class="page-link"
                            >
                              {{ $i }}
                            </button>
                          </li>
                        @endif
                      @endfor

                      {{-- Last Page Link --}}

                      @if ($batches->hasMorePages())
                        <li class="paginate_button page-item next">
                          <button
                            type="button"
                            wire:click="gotoPage({{ $batches->lastPage() }})"
                            class="page-link"
                          >
                            <i class="fas fa-angle-double-right"></i>
                          </button>
                        </li>
                      @else
                        <li class="paginate_button page-item next disabled">
                          <span class="page-link"><i class="fas fa-angle-double-right"></i></span>
                        </li>
                      @endif
                    </ul>
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="text-center py-3">
              @if ($search || $location || $dateFrom || $dateTo || $satuan)
                <div class="d-inline-flex align-items-start alert alert-info py-2 px-3" role="alert">
                  <i class="fas fa-search fa-lg mr-2 mt-1 text-info"></i>
                  <div class="text-left">
                    <div class="font-weight-semibold mb-1">Tidak ada hasil untuk filter yang diterapkan</div>
                    @if ($search)
                      <div class="small text-muted">Pencarian: <strong>"{{ $search }}"</strong></div>
                    @endif
                  </div>
                </div>
              @else
                <div class="text-muted small">Tidak ada data tumpukan stok.</div>
              @endif

              <div class="mt-2">
                @if ($search)
                  <button type="button" class="btn btn-link btn-sm text-secondary" wire:click="clearSearchFilter">
                    <i class="fas fa-times mr-1"></i> Hapus Pencarian
                  </button>
                @endif

                @if ($search || $location || $dateFrom || $dateTo || $satuan)
                  <button type="button" class="btn btn-link btn-sm text-primary" wire:click="resetMainFilters">
                    <i class="fas fa-redo-alt mr-1"></i> Reset Semua Filter
                  </button>
                @endif
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  @include('livewire.admin.stock-batch-slot-summary')

  <!-- Total Per Product Table with Tabs -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-outline card-info shadow-sm">
        <div class="card-header py-2">
          <h3 class="card-title">
            <i class="fas fa-chart-bar mr-2"></i>
            Ringkasan Stok
          </h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="stockSummaryTabs" role="tablist">
            <li class="nav-item">
              <a
                class="nav-link {{ $stockSummaryTab === 'product' ? 'active' : '' }}"
                href="#"
                wire:click.prevent="setStockSummaryTab('product')"
                role="tab"
              >
                <i class="fas fa-box mr-1"></i>
                Per Produk
              </a>
            </li>
            <li class="nav-item">
              <a
                class="nav-link {{ $stockSummaryTab === 'location' ? 'active' : '' }}"
                href="#"
                wire:click.prevent="setStockSummaryTab('location')"
                role="tab"
              >
                <i class="fas fa-map-marker-alt mr-1"></i>
                Per Lokasi
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body p-0">
          <div class="tab-content">
            <!-- Tab: Per Produk -->
            <div
              class="tab-pane {{ $stockSummaryTab === 'product' ? 'active show' : '' }}"
              id="tabProduct"
            >
              <!-- Filter bar -->
              <div class="p-3 bg-light border-bottom">
                <div class="row align-items-center">
                  <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <label class="mb-1 text-sm font-weight-bold">
                      <i class="fas fa-calendar text-muted mr-1"></i>
                      Dari Tanggal
                    </label>
                    <input
                      type="date"
                      wire:model.live="summaryDateFrom"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <label class="mb-1 text-sm font-weight-bold">
                      <i class="fas fa-calendar-alt text-muted mr-1"></i>
                      Sampai Tanggal
                    </label>
                    <input
                      type="date"
                      wire:model.live="summaryDateTo"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 col-6">
                    <label class="mb-1 text-sm font-weight-bold">
                      <i class="fas fa-list-ol text-muted mr-1"></i>
                      Tampilkan
                    </label>
                    <select wire:model.live="productPerPage" class="form-control form-control-sm">
                      <option value="5">5 baris</option>
                      <option value="10">10 baris</option>
                      <option value="25">25 baris</option>
                      <option value="50">50 baris</option>
                      <option value="100">100 baris</option>
                    </select>
                  </div>
                  <div class="col-md-3 col-6">
                    <label class="mb-1 text-sm d-none d-md-block">&nbsp;</label>
                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm btn-block"
                      wire:click="$wire.set('summaryDateFrom', ''); $wire.set('summaryDateTo', '')"
                      title="Reset filter tanggal"
                    >
                      <i class="fas fa-redo-alt mr-1"></i>
                      Reset Filter
                    </button>
                  </div>
                </div>
                @if ($summaryDateFrom || $summaryDateTo)
                  <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                      <i class="fas fa-filter mr-1"></i>
                      Filter aktif:
                      @if ($summaryDateFrom)
                        <span class="badge badge-success ml-1">
                          Dari: {{ \Carbon\Carbon::parse($summaryDateFrom)->format('d/m/Y') }}
                          <a
                            href="#"
                            wire:click.prevent="$set('summaryDateFrom', '')"
                            class="text-white ml-1"
                          >
                            &times;
                          </a>
                        </span>
                      @endif

                      @if ($summaryDateTo)
                        <span class="badge badge-warning ml-1">
                          Sampai: {{ \Carbon\Carbon::parse($summaryDateTo)->format('d/m/Y') }}
                          <a
                            href="#"
                            wire:click.prevent="$set('summaryDateTo', '')"
                            class="text-white ml-1"
                          >
                            &times;
                          </a>
                        </span>
                      @endif
                    </small>
                  </div>
                @endif

                <div class="mt-2">
                  <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    Total
                    <strong>{{ $this->totalPerProductPaginated['total'] }}</strong>
                    produk dengan stok aktif
                  </small>
                </div>
              </div>

              @if ($this->totalPerProductPaginated['total'] > 0)
                <div class="table-responsive">
                  <table class="table table-hover table-sm mb-0 stock-summary-table">
                    <thead>
                      <tr class="bg-gradient-info text-white">
                        <th class="text-center" style="width: 50px">#</th>
                        <th style="width: 120px">
                          <i class="fas fa-barcode mr-1"></i>
                          Kode
                        </th>
                        <th>
                          <i class="fas fa-box mr-1"></i>
                          Nama Produk
                        </th>
                        <th style="width: 130px">
                          <i class="fas fa-folder mr-1"></i>
                          Kategori
                        </th>
                        <th style="width: 130px">
                          <i class="fas fa-tag mr-1"></i>
                          Sub Kategori
                        </th>
                        <th class="text-center" style="width: 100px">
                          <i class="fas fa-cubes mr-1"></i>
                          Total
                        </th>
                        <th class="text-center" style="width: 70px">Satuan</th>
                        <th class="text-center" style="width: 140px">
                          <i class="fas fa-clock mr-1"></i>
                          Update
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      @php
                        $no = ($this->totalPerProductPaginated['currentPage'] - 1) * $this->totalPerProductPaginated['perPage'] + 1;
                      @endphp

                      @foreach ($this->totalPerProductPaginated['items'] as $item)
                        <tr>
                          <td class="text-center">
                            <span class="badge badge-light border">{{ $no }}</span>
                          </td>
                          <td>
                            <code class="text-info">{{ $item->product->kode_produk }}</code>
                          </td>
                          <td>
                            <strong>{{ $item->product->nama_produk }}</strong>
                          </td>
                          <td>
                            <span class="badge badge-primary">{{ $item->category }}</span>
                          </td>
                          <td>
                            <span class="badge badge-success">{{ $item->subcategory }}</span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-info px-3 py-2" style="font-size: 0.9rem">
                              {{ rtrim(rtrim(number_format($item->total_qty, 2), '0'), '.') }}
                            </span>
                          </td>
                          <td class="text-center">
                            <small class="text-muted">{{ $item->satuan }}</small>
                          </td>
                          <td class="text-center">
                            <small class="text-muted">
                              <i class="fas fa-calendar-alt mr-1"></i>
                              {{ $item->latest_date ? $item->latest_date->format('d/m/Y H:i') : '-' }}
                            </small>
                          </td>
                        </tr>
                        @php
                          $no++;
                        @endphp
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <!-- Pagination -->
                @if ($this->totalPerProductPaginated['lastPage'] > 1)
                  <div class="card-footer bg-white">
                    <div class="row align-items-center">
                      <div class="col-sm-12 col-md-5">
                        <small class="text-muted">
                          Menampilkan {{ $this->totalPerProductPaginated['from'] }} -
                          {{ $this->totalPerProductPaginated['to'] }} dari
                          {{ $this->totalPerProductPaginated['total'] }} produk
                        </small>
                      </div>
                      <div class="col-sm-12 col-md-7">
                        <nav class="float-right">
                          <ul class="pagination pagination-sm m-0">
                            <li
                              class="page-item {{ $this->totalPerProductPaginated['currentPage'] == 1 ? 'disabled' : '' }}"
                            >
                              <button
                                type="button"
                                wire:click="gotoProductPage(1)"
                                class="page-link"
                                {{ $this->totalPerProductPaginated['currentPage'] == 1 ? 'disabled' : '' }}
                              >
                                <i class="fas fa-angle-double-left"></i>
                              </button>
                            </li>
                            <li
                              class="page-item {{ $this->totalPerProductPaginated['currentPage'] == 1 ? 'disabled' : '' }}"
                            >
                              <button
                                type="button"
                                wire:click="gotoProductPage({{ $this->totalPerProductPaginated['currentPage'] - 1 }})"
                                class="page-link"
                                {{ $this->totalPerProductPaginated['currentPage'] == 1 ? 'disabled' : '' }}
                              >
                                <i class="fas fa-angle-left"></i>
                              </button>
                            </li>

                            @php
                              $start = max(1, $this->totalPerProductPaginated['currentPage'] - 2);
                              $end = min($this->totalPerProductPaginated['lastPage'], $this->totalPerProductPaginated['currentPage'] + 2);
                            @endphp

                            @for ($i = $start; $i <= $end; $i++)
                              <li
                                class="page-item {{ $i == $this->totalPerProductPaginated['currentPage'] ? 'active' : '' }}"
                              >
                                <button
                                  type="button"
                                  wire:click="gotoProductPage({{ $i }})"
                                  class="page-link"
                                >
                                  {{ $i }}
                                </button>
                              </li>
                            @endfor

                            <li
                              class="page-item {{ $this->totalPerProductPaginated['currentPage'] == $this->totalPerProductPaginated['lastPage'] ? 'disabled' : '' }}"
                            >
                              <button
                                type="button"
                                wire:click="gotoProductPage({{ $this->totalPerProductPaginated['currentPage'] + 1 }})"
                                class="page-link"
                                {{ $this->totalPerProductPaginated['currentPage'] == $this->totalPerProductPaginated['lastPage'] ? 'disabled' : '' }}
                              >
                                <i class="fas fa-angle-right"></i>
                              </button>
                            </li>
                            <li
                              class="page-item {{ $this->totalPerProductPaginated['currentPage'] == $this->totalPerProductPaginated['lastPage'] ? 'disabled' : '' }}"
                            >
                              <button
                                type="button"
                                wire:click="gotoProductPage({{ $this->totalPerProductPaginated['lastPage'] }})"
                                class="page-link"
                                {{ $this->totalPerProductPaginated['currentPage'] == $this->totalPerProductPaginated['lastPage'] ? 'disabled' : '' }}
                              >
                                <i class="fas fa-angle-double-right"></i>
                              </button>
                            </li>
                          </ul>
                        </nav>
                      </div>
                    </div>
                  </div>
                @endif
              @else
                <div class="p-4 text-center">
                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                  <p class="text-muted mb-0">Tidak ada data stok produk</p>
                </div>
              @endif
            </div>

            <!-- Tab: Per Lokasi -->
            <div
              class="tab-pane {{ $stockSummaryTab === 'location' ? 'active show' : '' }}"
              id="tabLocation"
            >
              <div class="p-3 bg-light border-bottom">
                <div class="row align-items-center">
                  <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <label class="mb-1 text-sm font-weight-bold">
                      <i class="fas fa-calendar text-muted mr-1"></i>
                      Dari Tanggal
                    </label>
                    <input
                      type="date"
                      wire:model.live="summaryDateFrom"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 col-6 mb-2 mb-md-0">
                    <label class="mb-1 text-sm font-weight-bold">
                      <i class="fas fa-calendar-alt text-muted mr-1"></i>
                      Sampai Tanggal
                    </label>
                    <input
                      type="date"
                      wire:model.live="summaryDateTo"
                      class="form-control form-control-sm"
                    />
                  </div>
                  <div class="col-md-3 col-6">
                    <label class="mb-1 text-sm d-none d-md-block">&nbsp;</label>
                    <button
                      type="button"
                      class="btn btn-outline-secondary btn-sm btn-block"
                      wire:click="$wire.set('summaryDateFrom', ''); $wire.set('summaryDateTo', '')"
                      title="Reset filter tanggal"
                    >
                      <i class="fas fa-redo-alt mr-1"></i>
                      Reset Filter
                    </button>
                  </div>
                  <div class="col-md-3 col-6">
                    <label class="mb-1 text-sm d-none d-md-block">&nbsp;</label>
                    <small class="text-muted d-block">
                      <i class="fas fa-info-circle mr-1"></i>
                      Ringkasan per lokasi
                    </small>
                  </div>
                </div>
                @if ($summaryDateFrom || $summaryDateTo)
                  <div class="mt-2 pt-2 border-top">
                    <small class="text-muted">
                      <i class="fas fa-filter mr-1"></i>
                      Filter aktif:
                      @if ($summaryDateFrom)
                        <span class="badge badge-success ml-1">
                          Dari: {{ \Carbon\Carbon::parse($summaryDateFrom)->format('d/m/Y') }}
                          <a
                            href="#"
                            wire:click.prevent="$set('summaryDateFrom', '')"
                            class="text-white ml-1"
                          >
                            &times;
                          </a>
                        </span>
                      @endif

                      @if ($summaryDateTo)
                        <span class="badge badge-warning ml-1">
                          Sampai: {{ \Carbon\Carbon::parse($summaryDateTo)->format('d/m/Y') }}
                          <a
                            href="#"
                            wire:click.prevent="$set('summaryDateTo', '')"
                            class="text-white ml-1"
                          >
                            &times;
                          </a>
                        </span>
                      @endif
                    </small>
                  </div>
                @endif
              </div>

              @if ($this->totalPerLocation->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover table-sm mb-0 stock-summary-table">
                    <thead>
                      <tr class="bg-gradient-success text-white">
                        <th class="text-center" style="width: 50px">#</th>
                        <th style="width: 100px">
                          <i class="fas fa-building mr-1"></i>
                          Tipe
                        </th>
                        <th>
                          <i class="fas fa-map-marker-alt mr-1"></i>
                          Nama Lokasi
                        </th>
                        <th class="text-center" style="width: 120px">
                          <i class="fas fa-box mr-1"></i>
                          Jml Produk
                        </th>
                        <th class="text-center" style="width: 120px">
                          <i class="fas fa-layer-group mr-1"></i>
                          Jml Batch
                        </th>
                        <th class="text-center" style="width: 130px">
                          <i class="fas fa-cubes mr-1"></i>
                          Total Stok
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($this->totalPerLocation as $index => $loc)
                        <tr>
                          <td class="text-center">
                            <span class="badge badge-light border">{{ $index + 1 }}</span>
                          </td>
                          <td>
                            @if ($loc['type'] === 'store')
                              <span class="badge badge-primary">
                                <i class="fas fa-store mr-1"></i>
                                {{ $loc['type_label'] }}
                              </span>
                            @else
                              <span class="badge badge-warning">
                                <i class="fas fa-warehouse mr-1"></i>
                                {{ $loc['type_label'] }}
                              </span>
                            @endif
                          </td>
                          <td>
                            <strong>{{ $loc['name'] }}</strong>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-secondary px-2">
                              {{ $loc['total_products'] }}
                            </span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-info px-2">{{ $loc['total_batches'] }}</span>
                          </td>
                          <td class="text-center">
                            <span class="badge badge-success px-3 py-2" style="font-size: 0.9rem">
                              {{ rtrim(rtrim(number_format($loc['total_qty'], 2), '0'), '.') }}
                            </span>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                      <tr class="font-weight-bold">
                        <td colspan="3" class="text-right">Total Keseluruhan:</td>
                        <td class="text-center">
                          <span class="badge badge-dark px-2">
                            {{ $this->totalPerLocation->sum('total_products') }}
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-dark px-2">
                            {{ $this->totalPerLocation->sum('total_batches') }}
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-dark px-3 py-2" style="font-size: 0.9rem">
                            {{ rtrim(rtrim(number_format($this->totalPerLocation->sum('total_qty'), 2), '0'), '.') }}
                          </span>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              @else
                <div class="p-4 text-center">
                  <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                  <p class="text-muted mb-0">Tidak ada data stok per lokasi</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Stock Summary Table Styling */
    .stock-summary-table thead th {
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      padding: 0.75rem 0.5rem;
      white-space: nowrap;
      border: none;
    }
    .stock-summary-table tbody td {
      font-size: 0.85rem;
      padding: 0.6rem 0.5rem;
      vertical-align: middle;
      border-top: 1px solid #dee2e6;
    }
    .stock-summary-table tbody tr:hover {
      background-color: rgba(0, 123, 255, 0.05) !important;
    }
    .stock-summary-table tfoot td {
      padding: 0.75rem 0.5rem;
      border-top: 2px solid #dee2e6;
    }

    /* Tab styling */
    #stockSummaryTabs .nav-link {
      border-radius: 0;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      color: #6c757d;
      border: none;
      border-bottom: 3px solid transparent;
    }
    #stockSummaryTabs .nav-link:hover {
      color: #17a2b8;
      border-bottom-color: #dee2e6;
    }
    #stockSummaryTabs .nav-link.active {
      color: #17a2b8;
      background: transparent;
      border-bottom-color: #17a2b8;
    }

    /* Scroll container for Total Stok Per Produk */
    .product-scroll-container {
      max-height: 600px;
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 5px;
    }

    /* Custom scrollbar styling */
    .product-scroll-container::-webkit-scrollbar {
      width: 8px;
    }

    .product-scroll-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .product-scroll-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .product-scroll-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    /* AdminLTE 3 DataTables Pagination Style */
    .dataTables_info {
      padding-top: 8px;
      font-size: 14px;
      color: #6c757d;
    }

    .dataTables_paginate {
      padding-top: 0;
    }

    .pagination {
      margin: 0;
      display: inline-flex;
    }

    .paginate_button {
      margin: 0 2px;
    }

    .paginate_button.page-item .page-link {
      padding: 0.375rem 0.75rem;
      font-size: 0.875rem;
      line-height: 1.5;
      border-radius: 0.25rem;
      color: #007bff;
      background-color: #fff;
      border: 1px solid #dee2e6;
      cursor: pointer;
      transition: all 0.15s ease-in-out;
    }

    .paginate_button.page-item .page-link:hover {
      color: #0056b3;
      background-color: #e9ecef;
      border-color: #dee2e6;
    }

    .paginate_button.page-item.active .page-link {
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
      font-weight: 600;
    }

    .paginate_button.page-item.disabled .page-link {
      color: #6c757d;
      pointer-events: none;
      background-color: #fff;
      border-color: #dee2e6;
      opacity: 0.65;
      cursor: not-allowed;
    }

    .paginate_button.page-item.previous .page-link,
    .paginate_button.page-item.next .page-link {
      padding: 0.375rem 0.75rem;
    }

    .paginate_button.page-item.previous .page-link i,
    .paginate_button.page-item.next .page-link i {
      font-size: 14px;
    }

    /* Link styling for pagination */
    .paginate_button a.page-link {
      text-decoration: none;
      display: block;
    }

    .paginate_button a.page-link:focus {
      box-shadow: none;
    }
  </style>

  <!-- Edit Modal -->
  @if ($showEditModal)
    <div class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-backdrop fade show"></div>
      <div class="modal-dialog modal-lg" role="document" style="z-index: 1050; position: relative">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <h5 class="modal-title">
              <i class="fas fa-edit mr-2"></i>
              Edit Tumpukan Stok
            </h5>
            <button type="button" class="close" wire:click="closeEditModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form wire:submit.prevent="updateBatch">
            <div class="modal-body">
              @if (session()->has('error'))
                <div class="alert alert-danger">
                  <i class="icon fas fa-ban"></i>
                  {{ session('error') }}
                </div>
              @endif

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label><strong>Produk</strong></label>
                    <input
                      type="text"
                      class="form-control"
                      value="{{ $editProductId ? \App\Models\Product::find($editProductId)?->nama_produk ?? '' : '' }}"
                      readonly
                    />
                    <small class="text-muted">Produk tidak dapat diubah</small>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label><strong>Lokasi</strong></label>
                    <input
                      type="text"
                      class="form-control"
                      value="{{ $editLocationType === 'store' ? 'Toko' : 'Gudang' }}"
                      readonly
                    />
                    <small class="text-muted">Lokasi tidak dapat diubah</small>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <strong>
                        Nama Tumpukan
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="text"
                      wire:model="editNamaTumpukan"
                      class="form-control @error('editNamaTumpukan') is-invalid @enderror"
                      placeholder="A, B, C..."
                      maxlength="50"
                    />
                    @error('editNamaTumpukan')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>
                      <strong>
                        Qty
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="number"
                      wire:model="editQty"
                      class="form-control @error('editQty') is-invalid @enderror"
                      placeholder="0.00"
                      step="0.01"
                      min="0"
                    />
                    @error('editQty')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <label>
                      <strong>
                        Satuan
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model="editSatuan"
                      class="form-control @error('editSatuan') is-invalid @enderror"
                    >
                      <option value="">- Pilih -</option>
                      @foreach ($units as $unit)
                        <option value="{{ $unit->nama_unit }}">{{ $unit->nama_unit }}</option>
                      @endforeach
                    </select>
                    @error('editSatuan')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label><strong>Catatan</strong></label>
                    <textarea
                      wire:model="editNote"
                      class="form-control"
                      rows="3"
                      placeholder="Catatan opsional..."
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" wire:click="closeEditModal">
                <i class="fas fa-times mr-1"></i>
                Batal
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i>
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif

  <script>
    // Confirm delete with SweetAlert2
    function confirmDelete(batchId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus stok batch ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteBatch', batchId);
            }
        });
    }

    // Listen for batch-created event to show success message
    document.addEventListener('livewire:init', function() {
        Livewire.on('batch-created', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Stok tumpukan berhasil dibuat!',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });

        Livewire.on('notify', function(data) {
            const params = data[0] || data;
            Swal.fire({
                icon: params.type || 'info',
                title: params.type === 'success' ? 'Berhasil!' : 'Pemberitahuan',
                text: params.message,
                timer: 3000,
                timerProgressBar: true
            });
        });
    });

    // Alpine.js product dropdown handler
    function productDropdown() {
        const allProducts = @json($productsJson);

        return {
            search: '',
            filtered: [],
            showDropdown: false,

            init() {
                // Listen for productAdded event from Livewire
                window.addEventListener('productAdded', (event) => {
                    const newProduct = event.detail[0];
                    // Add to products list
                    allProducts.push(newProduct);
                    // Auto-select the new product
                    this.selectProduct(newProduct);
                    this.showDropdown = false;
                });
            },

            filter() {
                const query = this.search.toLowerCase();
                if (!query) {
                    this.filtered = allProducts;
                } else {
                    this.filtered = allProducts.filter(p =>
                        p.nama_produk.toLowerCase().includes(query) ||
                        (p.kode_produk && p.kode_produk.toLowerCase().includes(query))
                    );
                }
            },

            selectProduct(product) {
                const value = product.nama_produk;
                document.getElementById('productSearch').value = value;
                this.$wire.call('selectProduct', value);
            },

            selectFirst() {
                if (this.filtered.length > 0) {
                    this.selectProduct(this.filtered[0]);
                    this.showDropdown = false;
                }
            }
        };
    }

    // Alpine.js hold product dropdown handler
    function holdProductDropdown() {
        const allProducts = @json($productsJson);

        return {
            search: '',
            filtered: [],
            showDropdown: false,

            filter() {
                const query = this.search.toLowerCase();
                if (!query) {
                    this.filtered = [];
                } else {
                    this.filtered = allProducts.filter(p =>
                        p.nama_produk.toLowerCase().includes(query) ||
                        (p.kode_produk && p.kode_produk.toLowerCase().includes(query))
                    );
                }
            },

            selectProduct(product) {
                const value = product.nama_produk;
                document.getElementById('holdProductSearch').value = value;
                this.$wire.call('selectHoldProduct', product.id);
            },

            selectFirst() {
                if (this.filtered.length > 0) {
                    this.selectProduct(this.filtered[0]);
                    this.showDropdown = false;
                }
            }
        };
    }
  </script>

  <!-- Quick Add Product Modal -->
  @if ($showQuickAddProductModal)
    <div class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-backdrop fade show"></div>
      <div class="modal-dialog modal-lg" role="document" style="z-index: 1050; position: relative">
        <div class="modal-content">
          <div class="modal-header bg-success">
            <h5 class="modal-title text-white">
              <i class="fas fa-plus-circle mr-2"></i>
              Tambah Produk Baru
            </h5>
            <button
              type="button"
              class="close text-white"
              wire:click="closeQuickAddProductModal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form wire:submit.prevent="quickAddProduct">
            <div class="modal-body">
              @if (session()->has('error'))
                <div class="alert alert-danger">
                  <i class="icon fas fa-ban"></i>
                  {{ session('error') }}
                </div>
              @endif

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <strong>
                        Nama Produk
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="text"
                      wire:model="quickProductName"
                      class="form-control @error('quickProductName') is-invalid @enderror"
                      placeholder="Masukkan nama produk"
                    />
                    @error('quickProductName')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <strong>
                        Kode Produk
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <input
                      type="text"
                      wire:model="quickProductCode"
                      class="form-control @error('quickProductCode') is-invalid @enderror"
                      placeholder="Masukkan kode produk"
                    />
                    @error('quickProductCode')
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
                        Kategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model.live="quickProductCategoryId"
                      class="form-control @error('quickProductCategoryId') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Kategori --</option>
                      @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                      @endforeach
                    </select>
                    @error('quickProductCategoryId')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label>
                      <strong>
                        Subkategori
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model="quickProductSubcategoryId"
                      class="form-control @error('quickProductSubcategoryId') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Subkategori --</option>
                      @forelse ($this->quickAddSubcategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->nama_subkategori }}</option>
                      @empty
                        @if ($quickProductCategoryId)
                          <option value="" disabled>Tidak ada subkategori</option>
                        @endif
                      @endforelse
                    </select>
                    @error('quickProductSubcategoryId')
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
                        Satuan
                        <span class="text-danger">*</span>
                      </strong>
                    </label>
                    <select
                      wire:model="quickProductUnit"
                      class="form-control @error('quickProductUnit') is-invalid @enderror"
                    >
                      <option value="">-- Pilih Satuan --</option>
                      @foreach ($units as $unit)
                        <option value="{{ $unit->nama_unit }}">{{ $unit->nama_unit }}</option>
                      @endforeach
                    </select>
                    @error('quickProductUnit')
                      <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-secondary"
                wire:click="closeQuickAddProductModal"
              >
                <i class="fas fa-times mr-1"></i>
                Batal
              </button>
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save mr-1"></i>
                Simpan Produk
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div>

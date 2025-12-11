<div>
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

  <!-- Filter & Search Card -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">Filter & Pencarian</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <label>Cari Produk</label>
              <input
                type="text"
                wire:model.live="search"
                class="form-control"
                placeholder="Nama atau kode produk..."
              />
            </div>
            <div class="col-md-4">
              <label>Lokasi</label>
              <select wire:model.live="location" class="form-control">
                <option value="">Semua Lokasi</option>
                @foreach ($locations as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Daftar Stok Tumpukan Card -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline">
        <div class="card-header">
          <h3 class="card-title">Daftar Stok Tumpukan</h3>
          <div class="card-tools">
            <select
              wire:model.live="per_page"
              class="form-control form-control-sm"
              style="width: 120px; display: inline-block; margin-right: 10px"
            >
              <option value="10">10 baris</option>
              <option value="15">15 baris</option>
              <option value="25">25 baris</option>
              <option value="50">50 baris</option>
              <option value="100">100 baris</option>
            </select>
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

            @if (! $this->showCreateForm)
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
                    <select
                      wire:model.live="createCategoryId"
                      class="form-control @error('createCategoryId') is-invalid @enderror"
                    >
                      <option value="">-- Pilih --</option>
                      @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                      @endforeach
                    </select>
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
                    </select>
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

          @if ($batches->count() > 0)
            <div class="table-responsive">
              <table class="table table-sm table-hover align-middle">
                <thead class="bg-light">
                  <tr>
                    <th style="width: 40px">
                      <input
                        type="checkbox"
                        wire:click="$toggle('selectAll')"
                        wire:model="selectAll"
                        title="Select all"
                      />
                    </th>
                    <th>#</th>
                    <th>Nama Tumpukan</th>
                    <th>Lokasi</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $no = 1;
                    $groupedBatches = $batches->groupBy('product_id');
                  @endphp

                  @foreach ($groupedBatches as $productId => $productBatches)
                    @php
                      $firstBatch = $productBatches->first();
                      $product = $firstBatch->product;
                      $totalQty = $productBatches->sum('qty');
                    @endphp

                    <!-- Product Header Row -->
                    <tr class="bg-light">
                      <td
                        colspan="6"
                        class="font-weight-bold"
                        style="font-size: 14px; border-top: 2px solid #007bff"
                      >
                        <i class="fas fa-box mr-2 text-primary"></i>
                        <strong>{{ $product->nama_produk ?? 'N/A' }}</strong>
                        <span class="badge badge-secondary ml-2">
                          [{{ $product->kode_produk ?? 'N/A' }}]
                        </span>
                        <span class="badge badge-primary ml-2">
                          {{ $product->category->nama_kategori ?? 'N/A' }}
                        </span>
                        <span class="badge badge-success ml-2">
                          {{ $product->subcategory->nama_subkategori ?? 'N/A' }}
                        </span>
                        <span class="badge badge-info ml-2">
                          <i class="fas fa-layer-group mr-1"></i>
                          {{ $productBatches->count() }} Batch
                        </span>
                        <span class="badge badge-warning ml-2">
                          <i class="fas fa-cubes mr-1"></i>
                          Total:
                          {{ number_format($totalQty + 0, 0) === number_format($totalQty, 0) ? number_format($totalQty, 0) : rtrim(rtrim(number_format($totalQty, 2), '0'), '.') }}
                          {{ $product->satuan ?? 'N/A' }}
                        </span>
                      </td>
                    </tr>

                    <!-- Batch Rows -->
                    @foreach ($productBatches as $batch)
                      <tr
                        @if(in_array($batch->id, $this->selectedBatches)) class="table-active" @endif
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
                          <span class="badge badge-dark">{{ $no }}</span>
                        </td>
                        <td class="pl-4">
                          <i class="fas fa-angle-right mr-2 text-muted"></i>
                          <strong>{{ $batch->nama_tumpukan }}</strong>
                        </td>
                        <td>
                          <span class="badge badge-info">
                            {{ ucfirst($batch->location_type) }}
                          </span>
                        </td>
                        <td class="text-center">
                          <strong>
                            {{ rtrim(rtrim(number_format($batch->qty, 2), '0'), '.') }}
                          </strong>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-secondary">
                            {{ $batch->product->satuan ?? 'N/A' }}
                          </span>
                        </td>
                        <td class="text-center">
                          <div class="btn-group btn-group-sm" role="group">
                            <button
                              type="button"
                              wire:click="editBatch({{ $batch->id }})"
                              class="btn btn-primary"
                              title="Edit"
                            >
                              <i class="fas fa-edit"></i>
                            </button>
                            <button
                              type="button"
                              wire:click="deleteBatch({{ $batch->id }})"
                              onclick="return confirm('Apakah Anda yakin ingin menghapus?')"
                              class="btn btn-danger"
                              title="Hapus"
                            >
                              <i class="fas fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      @php
                        $no++;
                      @endphp
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
            <div class="alert alert-info">
              <i class="icon fas fa-info"></i>
              Tidak ada data
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  @include('livewire.admin.stock-batch-slot-summary')

  <!-- Total Per Product Table -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-outline card-info">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-table mr-2"></i>
            Total Stok Per Produk
          </h3>
          <div class="card-tools">
            <select
              wire:model.live="productPerPage"
              class="form-control form-control-sm"
              style="width: 120px; display: inline-block"
            >
              <option value="5">5 baris</option>
              <option value="10">10 baris</option>
              <option value="25">25 baris</option>
              <option value="50">50 baris</option>
              <option value="100">100 baris</option>
            </select>
          </div>
        </div>
        <div class="card-body">
          @if ($this->totalPerProductPaginated['total'] > 0)
            <div class="table-responsive">
              <table class="table table-striped table-hover table-sm">
                <thead class="bg-info text-white">
                  <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 15%">Kode Produk</th>
                    <th style="width: 25%">Nama Produk</th>
                    <th style="width: 12%">Kategori</th>
                    <th style="width: 12%">Sub Kategori</th>
                    <th style="width: 10%">Total Stok</th>
                    <th style="width: 5%">Satuan</th>
                    <th style="width: 16%">Tanggal Terakhir</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $no =
                      ($this->totalPerProductPaginated['currentPage'] - 1) *
                        $this->totalPerProductPaginated['perPage'] +
                      1;
                  @endphp

                  @foreach ($this->totalPerProductPaginated['items'] as $item)
                    <tr>
                      <td class="text-center">{{ $no }}</td>
                      <td>
                        <span class="badge badge-secondary">
                          {{ $item->product->kode_produk }}
                        </span>
                      </td>
                      <td><strong>{{ $item->product->nama_produk }}</strong></td>
                      <td><span class="badge badge-primary">{{ $item->category }}</span></td>
                      <td><span class="badge badge-success">{{ $item->subcategory }}</span></td>
                      <td class="text-center font-weight-bold">
                        {{ rtrim(rtrim(number_format($item->total_qty, 2), '0'), '.') }}
                      </td>
                      <td class="text-center">{{ $item->satuan }}</td>
                      <td class="text-center">
                        <small style="font-size: 15px">
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
              <div class="row mt-3">
                <div class="col-sm-12 col-md-5">
                  <div class="dataTables_info">
                    Menampilkan {{ $this->totalPerProductPaginated['from'] }} sampai
                    {{ $this->totalPerProductPaginated['to'] }} dari
                    {{ $this->totalPerProductPaginated['total'] }} produk
                  </div>
                </div>
                <div class="col-sm-12 col-md-7">
                  <nav class="float-right">
                    <ul class="pagination pagination-sm m-0">
                      @if ($this->totalPerProductPaginated['currentPage'] == 1)
                        <li class="page-item disabled">
                          <span class="page-link">&laquo;</span>
                        </li>
                      @else
                        <li class="page-item">
                          <button type="button" wire:click="gotoProductPage(1)" class="page-link">
                            &laquo;
                          </button>
                        </li>
                      @endif

                      @for ($i = 1; $i <= $this->totalPerProductPaginated['lastPage']; $i++)
                        @if ($i == $this->totalPerProductPaginated['currentPage'])
                          <li class="page-item active">
                            <span class="page-link">{{ $i }}</span>
                          </li>
                        @else
                          <li class="page-item">
                            <button
                              type="button"
                              wire:click="gotoProductPage({{ $i }})"
                              class="page-link"
                            >
                              {{ $i }}
                            </button>
                          </li>
                        @endif
                      @endfor

                      @if ($this->totalPerProductPaginated['currentPage'] == $this->totalPerProductPaginated['lastPage'])
                        <li class="page-item disabled">
                          <span class="page-link">&raquo;</span>
                        </li>
                      @else
                        <li class="page-item">
                          <button
                            type="button"
                            wire:click="gotoProductPage({{ $this->totalPerProductPaginated['lastPage'] }})"
                            class="page-link"
                          >
                            &raquo;
                          </button>
                        </li>
                      @endif
                    </ul>
                  </nav>
                </div>
              </div>
            @endif
          @else
            <div class="alert alert-info">
              <i class="icon fas fa-info"></i>
              Tidak ada data stok tumpukan
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <style>
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

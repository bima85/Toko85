<div>
  <div class="container-fluid">
    @if (session('message'))
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-check"></i>
        {{ session('message') }}
      </div>
    @endif

    @if (session('error'))
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <i class="icon fas fa-exclamation-triangle"></i>
        {{ session('error') }}
      </div>
    @endif

    <div class="row">
      <!-- Form Column -->
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-edit mr-2"></i>
              <strong>Informasi Penyesuaian</strong>
            </h3>
          </div>
          <div class="card-body" style="max-height: 800px; overflow-y: auto">
            <!-- Lokasi -->
            <div class="form-group">
              <label for="adjustment_location"><strong>Lokasi *</strong></label>
              <select
                wire:model.live="adjustment_location"
                class="form-control form-control-sm"
                id="adjustment_location"
                required
              >
                <option value="">-- Pilih Lokasi --</option>
                <option value="store">Toko</option>
                <option value="warehouse">Gudang</option>
              </select>
              @error('adjustment_location')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>

            <!-- Store Selection -->
            @if ($adjustment_location === 'store')
              <div class="form-group">
                <label for="adjustment_store_id"><strong>Nama Toko *</strong></label>
                <select
                  wire:model.live="adjustment_store_id"
                  class="form-control form-control-sm"
                  id="adjustment_store_id"
                  required
                >
                  <option value="">-- Pilih Toko --</option>
                  @foreach ($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->nama_toko }}</option>
                  @endforeach
                </select>
                @error('adjustment_store_id')
                  <small class="text-danger d-block">{{ $message }}</small>
                @enderror
              </div>
            @endif

            <!-- Warehouse Selection -->
            @if ($adjustment_location === 'warehouse')
              <div class="form-group">
                <label for="adjustment_warehouse_id"><strong>Nama Gudang *</strong></label>
                <select
                  wire:model.live="adjustment_warehouse_id"
                  class="form-control form-control-sm"
                  id="adjustment_warehouse_id"
                  required
                >
                  <option value="">-- Pilih Gudang --</option>
                  @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->nama_gudang }}</option>
                  @endforeach
                </select>
                @error('adjustment_warehouse_id')
                  <small class="text-danger d-block">{{ $message }}</small>
                @enderror
              </div>
            @endif

            <!-- Tipe Penyesuaian -->
            <div class="form-group">
              <label for="adjustment_type"><strong>Tipe Penyesuaian *</strong></label>
              <select
                wire:model.live="adjustment_type"
                class="form-control form-control-sm"
                id="adjustment_type"
                required
              >
                <option value="add">Penambahan Stok</option>
                <option value="remove">Pengurangan Stok</option>
              </select>
              @error('adjustment_type')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>

            <!-- Tanggal -->
            <div class="form-group">
              <label for="adjustment_date"><strong>Tanggal Penyesuaian *</strong></label>
              <input
                wire:model.live="adjustment_date"
                type="date"
                class="form-control form-control-sm"
                id="adjustment_date"
                required
              />
              @error('adjustment_date')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>

            <!-- Alasan -->
            <div class="form-group">
              <label for="adjustment_reason"><strong>Alasan Penyesuaian *</strong></label>
              <textarea
                wire:model.live="adjustment_reason"
                class="form-control form-control-sm"
                id="adjustment_reason"
                rows="4"
                placeholder="Contoh: Perbedaan stock opname, Kerusakan barang, dll"
                required
              ></textarea>
              @error('adjustment_reason')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>

            <hr />

            <!-- Add Item Button -->
            <button wire:click="addItem" type="button" class="btn btn-info btn-sm btn-block">
              <i class="fas fa-plus mr-1"></i>
              Tambah Item
            </button>
          </div>
        </div>
      </div>

      <!-- Preview Column -->
      <div class="col-md-8">
        <div class="card card-success card-outline">
          <div class="card-header">
            <h3 class="card-title">
              <i class="fas fa-table mr-2"></i>
              <strong>Preview Item Penyesuaian Stok</strong>
            </h3>
          </div>
          <div class="card-body" style="max-height: 800px; overflow-y: auto">
            @if (count($adjustment_items) > 0)
              <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover">
                  <thead class="bg-success text-white">
                    <tr>
                      <th style="width: 3%">#</th>
                      <th style="width: 10%">Kategori</th>
                      <th style="width: 10%">Subkategori</th>
                      <th style="width: 12%">Produk</th>
                      <th style="width: 7%">Unit</th>
                      <th style="width: 7%">Stok Awal</th>
                      <th style="width: 7%">
                        @if ($adjustment_type === 'add')
                          Stok Masuk
                        @else
                          Stok Keluar
                        @endif
                      </th>
                      <th style="width: 7%">Stok Akhir</th>
                      <th style="width: 7%">Total Stok</th>
                      <th style="width: 5%">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($adjustment_items as $index => $item)
                      <tr
                        wire:key="item-{{ $index }}"
                        @if($index % 2 == 0) class="table-light" @endif
                      >
                        <td class="text-center">{{ $index + 1 }}</td>
                        <!-- Kategori -->
                        <td>
                          <select
                            wire:model.live="adjustment_items.{{ $index }}.category_id"
                            wire:change="handleCategoryChange({{ $index }})"
                            class="form-control form-control-sm"
                          >
                            <option value="">--</option>
                            @foreach ($categories as $cat)
                              <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach

                            <option
                              value="__add_new__"
                              style="background-color: #d4edda; color: #155724; font-weight: bold"
                            >
                              + Tambah Kategori
                            </option>
                          </select>
                        </td>
                        <!-- Subkategori -->
                        <td>
                          <select
                            wire:model.live="adjustment_items.{{ $index }}.subcategory_id"
                            wire:change="handleSubcategoryChange({{ $index }}, $event.target.value)"
                            class="form-control form-control-sm"
                          >
                            <option value="">--</option>
                            @if (! empty($adjustment_items[$index]['category_id']))
                              @foreach ($filteredSubcategories as $sub)
                                @if ($sub->category_id == $adjustment_items[$index]['category_id'])
                                  <option value="{{ $sub->id }}">
                                    {{ $sub->nama_subkategori }}
                                  </option>
                                @endif
                              @endforeach
                            @endif

                            @if (! empty($adjustment_items[$index]['category_id']))
                              <option
                                value="__add_new__"
                                style="background-color: #d4edda; color: #155724; font-weight: bold"
                              >
                                + Tambah Subkategori
                              </option>
                            @endif
                          </select>
                        </td>
                        <!-- Produk -->
                        <td>
                          <select
                            wire:model.live="adjustment_items.{{ $index }}.product_id"
                            wire:change="handleProductChange({{ $index }}, $event.target.value)"
                            class="form-control form-control-sm"
                          >
                            <option value="">--</option>
                            @if (! empty($adjustment_items[$index]['subcategory_id']))
                              @foreach ($filteredProducts as $prod)
                                @if ($prod->subcategory_id == $adjustment_items[$index]['subcategory_id'])
                                  <option value="{{ $prod->id }}">
                                    {{ $prod->nama_produk }}
                                  </option>
                                @endif
                              @endforeach
                            @endif

                            @if (! empty($adjustment_items[$index]['subcategory_id']))
                              <option
                                value="__add_new__"
                                style="background-color: #d4edda; color: #155724; font-weight: bold"
                              >
                                + Tambah Produk
                              </option>
                            @endif
                          </select>
                        </td>
                        <!-- Unit -->
                        <td>
                          <select
                            wire:model.live="adjustment_items.{{ $index }}.unit_id"
                            wire:change="handleUnitChange({{ $index }}, $event.target.value)"
                            class="form-control form-control-sm"
                          >
                            <option value="">--</option>
                            @foreach ($units as $unit)
                              <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                            @endforeach

                            <option
                              value="__add_new__"
                              style="background-color: #d4edda; color: #155724; font-weight: bold"
                            >
                              + Tambah Unit
                            </option>
                          </select>
                        </td>
                        <!-- Stok Awal (Editable untuk Penambahan, Readonly untuk Pengurangan) -->
                        <td>
                          @if ($adjustment_type === 'add')
                            <!-- Input untuk Penambahan Stok -->
                            <input
                              type="number"
                              wire:model.live.debounce-500ms="adjustment_items.{{ $index }}.stok_awal"
                              wire:change="calculateTotal({{ $index }})"
                              class="form-control form-control-sm text-center"
                              min="0"
                              placeholder="0"
                            />
                          @else
                            <!-- Display untuk Pengurangan Stok (menggunakan wire:ignore untuk menampilkan value) -->
                            <div wire:ignore>
                              <input
                                type="number"
                                value="{{ $item['stok_awal'] ?? 0 }}"
                                class="form-control form-control-sm bg-light text-center font-weight-bold"
                                readonly
                              />
                            </div>
                          @endif
                        </td>
                        <!-- Stok Masuk/Keluar (Quantity Input) -->
                        <td>
                          <input
                            type="number"
                            wire:model.live.debounce-500ms="adjustment_items.{{ $index }}.quantity"
                            wire:change="calculateTotal({{ $index }})"
                            class="form-control form-control-sm text-center"
                            min="0"
                            required
                          />
                        </td>
                        <!-- Stok Akhir (Auto-calculated) -->
                        <td>
                          @php
                            $stok_awal = (int) ($item['stok_awal'] ?? 0);
                            $quantity = (int) ($item['quantity'] ?? 0);
                            if ($adjustment_type === 'add') {
                              $stok_akhir = $stok_awal + $quantity;
                              $warna = 'text-success';
                            } else {
                              $stok_akhir = $stok_awal - $quantity;
                              $warna = 'text-danger';
                            }
                          @endphp

                          <input
                            type="number"
                            value="{{ $stok_akhir }}"
                            class="form-control form-control-sm bg-light {{ $warna }} text-center font-weight-bold"
                            disabled
                            readonly
                          />
                        </td>
                        <!-- Total Stok (Auto-calculated, displayed as badge) -->
                        <td class="text-center">
                          <span class="badge badge-info" style="font-size: 12px">
                            {{ $stok_akhir }}
                          </span>
                        </td>
                        <!-- Aksi -->
                        <td>
                          <button
                            type="button"
                            wire:click="removeItem({{ $index }})"
                            class="btn btn-danger btn-sm btn-block"
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
                <i class="fas fa-info-circle mr-2"></i>
                Belum ada item penyesuaian stok. Silakan tambahkan item terlebih dahulu dengan klik
                tombol "Tambah Item".
              </div>
            @endif

            @if (count($adjustment_items) > 0)
              <div class="alert alert-primary mt-3 mb-0">
                <div class="row">
                  <div class="col-6">
                    <strong>
                      <i class="fas fa-calculator mr-2"></i>
                      Total Stok Toko:
                    </strong>
                  </div>
                  <div class="col-6 text-right">
                    <h5 class="mb-0">
                      <span class="badge badge-primary">{{ $totalStokToko }}</span>
                    </h5>
                  </div>
                </div>
              </div>
            @endif
          </div>
          <div class="card-footer">
            <button type="submit" wire:click="save" class="btn btn-success">
              <i class="fas fa-save mr-1"></i>
              Simpan Penyesuaian Stok
            </button>
            <a href="{{ route('admin.stock-reports') }}" class="btn btn-secondary">
              <i class="fas fa-times mr-1"></i>
              Batal
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah Kategori -->
  @if ($showModalCreateCategory)
    <div class="modal d-block" style="background-color: rgba(0, 0, 0, 0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">
              <i class="fas fa-plus mr-2"></i>
              Tambah Kategori
            </h4>
            <button type="button" class="close text-white" wire:click="closeModalCreateCategory()">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="newCategoryCode"><strong>Kode Kategori</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model="newCategoryCode"
                id="newCategoryCode"
                placeholder="Contoh: KAT001"
                required
              />
              @error('newCategoryCode')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
            <div class="form-group">
              <label for="newCategoryName"><strong>Nama Kategori</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model="newCategoryName"
                id="newCategoryName"
                placeholder="Masukkan nama kategori"
                required
              />
              @error('newCategoryName')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModalCreateCategory()">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="button" class="btn btn-primary" wire:click="saveNewCategory()">
              <i class="fas fa-save mr-1"></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Modal Tambah Subkategori -->
  @if ($showModalCreateSubcategory)
    <div class="modal d-block" style="background-color: rgba(0, 0, 0, 0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
            <h4 class="modal-title">
              <i class="fas fa-plus mr-2"></i>
              Tambah Subkategori
            </h4>
            <button
              type="button"
              class="close text-white"
              wire:click="closeModalCreateSubcategory()"
            >
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="newSubcategoryCategory"><strong>Kategori</strong></label>
              <select
                class="form-control"
                wire:model="newSubcategoryCategory"
                id="newSubcategoryCategory"
                required
              >
                <option value="">-- Pilih Kategori --</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                @endforeach
              </select>
              @error('newSubcategoryCategory')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
            <div class="form-group">
              <label for="newSubcategoryName"><strong>Nama Subkategori</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model="newSubcategoryName"
                id="newSubcategoryName"
                placeholder="Masukkan nama subkategori"
                required
              />
              @error('newSubcategoryName')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              wire:click="closeModalCreateSubcategory()"
            >
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="button" class="btn btn-info" wire:click="saveNewSubcategory()">
              <i class="fas fa-save mr-1"></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Modal Tambah Produk -->
  @if ($showModalCreateProduct)
    <div class="modal d-block" style="background-color: rgba(0, 0, 0, 0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h4 class="modal-title">
              <i class="fas fa-plus mr-2"></i>
              Tambah Produk
            </h4>
            <button type="button" class="close text-white" wire:click="closeModalCreateProduct()">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="newProductSubcategory"><strong>Subkategori</strong></label>
              <select
                class="form-control"
                wire:model.live="newProductSubcategory"
                id="newProductSubcategory"
                required
              >
                <option value="">-- Pilih Subkategori --</option>
                @foreach ($categories as $cat)
                  <optgroup label="{{ $cat->nama_kategori }}">
                    @foreach ($cat->subcategories as $sub)
                      <option value="{{ $sub->id }}">{{ $sub->nama_subkategori }}</option>
                    @endforeach
                  </optgroup>
                @endforeach
              </select>
              @error('newProductSubcategory')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
            <div class="form-group">
              <label for="newProductName"><strong>Nama Produk</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model.live="newProductName"
                id="newProductName"
                placeholder="Masukkan nama produk"
                required
              />
              @error('newProductName')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
            <div class="form-group">
              <label for="newProductCode"><strong>Kode Produk</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model="newProductCode"
                id="newProductCode"
                placeholder="Auto-generate"
                readonly
              />
              @error('newProductCode')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModalCreateProduct()">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="button" class="btn btn-success" wire:click="saveNewProduct()">
              <i class="fas fa-save mr-1"></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Modal Tambah Unit -->
  @if ($showModalCreateUnit)
    <div class="modal d-block" style="background-color: rgba(0, 0, 0, 0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-warning text-white">
            <h4 class="modal-title">
              <i class="fas fa-plus mr-2"></i>
              Tambah Unit
            </h4>
            <button type="button" class="close text-white" wire:click="closeModalCreateUnit()">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="newUnitName"><strong>Nama Unit</strong></label>
              <input
                type="text"
                class="form-control"
                wire:model="newUnitName"
                id="newUnitName"
                placeholder="Contoh: pcs, box, karton, kg, dll"
                required
              />
              @error('newUnitName')
                <small class="text-danger d-block">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModalCreateUnit()">
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>
            <button type="button" class="btn btn-warning" wire:click="saveNewUnit()">
              <i class="fas fa-save mr-1"></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

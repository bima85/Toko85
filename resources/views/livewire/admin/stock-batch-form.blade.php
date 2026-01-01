<div class="card card-success card-outline">
  <div class="card-header">
    <h3 class="card-title">
      <i class="fas fa-cube"></i>
      Form Manajemen Stok Tumpukan
    </h3>
  </div>

  <div class="card-body">
    <!-- Tab Selector -->
    <div class="nav-tabs-custom mb-3">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a
            class="nav-link {{ $actionType === 'add' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('actionType', 'add')"
          >
            <i class="fas fa-plus-circle"></i>
            Tambah Stok
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link {{ $actionType === 'reduce' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('actionType', 'reduce')"
          >
            <i class="fas fa-minus-circle"></i>
            Kurangi Stok
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link {{ $actionType === 'move' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('actionType', 'move')"
          >
            <i class="fas fa-arrows-alt"></i>
            Pindah Stok
          </a>
        </li>
      </ul>
    </div>

    <!-- Form Tambah Stok -->
    @if ($actionType === 'add')
      <form wire:submit="submit">
        <div class="form-group">
          <label for="addProduct">
            Produk
            <span class="text-danger">*</span>
          </label>
          <select wire:model="productId" id="addProduct" class="form-control" required>
            <option value="">-- Pilih Produk --</option>
            @foreach ($products as $product)
              <option value="{{ $product->id }}">
                {{ $product->nama_produk }} ({{ $product->kode_produk }})
              </option>
            @endforeach
          </select>
          @error('productId')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <!-- Kategori & Subkategori -->
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="addCategory">
              Kategori
            </label>
            <div class="input-group">
              <select wire:model="categoryId" id="addCategory" class="form-control">
                <option value="">-- Pilih Kategori (opsional) --</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                @endforeach
                <option value="__add__">+ Tambah Kategori...</option>
              </select>
            </div>
            @error('categoryId')
              <span class="text-danger small">{{ $message }}</span>
            @enderror

            @if ($showCategoryModal)
              <div class="mt-2 p-2 border rounded" style="background-color: #f8f9fa;">
                <div class="form-group mb-2">
                  <label class="small"><strong>Kode (opsional)</strong></label>
                  <input type="text" wire:model.defer="newCategoryCode" class="form-control form-control-sm" placeholder="Contoh: BERAS" />
                  @error('newCategoryCode')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
                <div class="form-group mb-2">
                  <label class="small"><strong>Nama Kategori</strong></label>
                  <input type="text" wire:model.defer="newCategoryName" class="form-control form-control-sm" placeholder="Contoh: Beras Putih" />
                  @error('newCategoryName')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
                <div class="form-group mb-0">
                  <button class="btn btn-success btn-sm" wire:click.prevent="createCategory">
                    <i class="fas fa-check"></i> Tambah
                  </button>
                  <button class="btn btn-secondary btn-sm" wire:click.prevent="$set('showCategoryModal', false)">
                    <i class="fas fa-times"></i> Batal
                  </button>
                </div>
              </div>
            @endif
          </div>

          <div class="form-group col-md-6">
            <label for="addSubcategory">
              Subkategori
            </label>
            <div class="input-group">
              <select wire:model="subcategoryId" id="addSubcategory" class="form-control">
                <option value="">-- Pilih Subkategori (opsional) --</option>
                @foreach ($subcategories as $sub)
                  <option value="{{ $sub->id }}">{{ $sub->nama_subkategori }}</option>
                @endforeach
                <option value="__add__">+ Tambah Subkategori...</option>
              </select>
            </div>
            @error('subcategoryId')
              <span class="text-danger small">{{ $message }}</span>
            @enderror

            @if ($showSubcategoryModal)
              <div class="mt-2 p-2 border rounded" style="background-color: #f8f9fa;">
                <div class="form-group mb-2">
                  <label class="small"><strong>Kode (opsional)</strong></label>
                  <input type="text" wire:model.defer="newSubcategoryCode" class="form-control form-control-sm" placeholder="Contoh: BR_PUTIH" />
                  @error('newSubcategoryCode')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
                <div class="form-group mb-2">
                  <label class="small"><strong>Nama Subkategori</strong></label>
                  <input type="text" wire:model.defer="newSubcategoryName" class="form-control form-control-sm" placeholder="Contoh: Beras Putih Premium" />
                  @error('newSubcategoryName')
                    <small class="text-danger d-block mt-1">{{ $message }}</small>
                  @enderror
                </div>
                @if (!$categoryId)
                  <div class="alert alert-info alert-sm mb-2" role="alert">
                    <small>Pilih kategori terlebih dahulu agar subkategori terkait dibuat.</small>
                  </div>
                @endif
                <div class="form-group mb-0">
                  <button class="btn btn-success btn-sm" wire:click.prevent="createSubcategory">
                    <i class="fas fa-check"></i> Tambah
                  </button>
                  <button class="btn btn-secondary btn-sm" wire:click.prevent="$set('showSubcategoryModal', false)">
                    <i class="fas fa-times"></i> Batal
                  </button>
                </div>
              </div>
            @endif
          </div>
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="addLocationType">
              Lokasi
              <span class="text-danger">*</span>
            </label>
            <select wire:model="locationType" id="addLocationType" class="form-control" required>
              @foreach ($locations as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('locationType')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group col-md-6">
            <label for="addNamaTumpukan">
              Nama Tumpukan
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              wire:model="namaTumpukan"
              id="addNamaTumpukan"
              class="form-control"
              placeholder="Contoh: Tumpukan A"
              required
            />
            @error('namaTumpukan')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <label for="addQty">
            Jumlah
            @if ($this->selectedProduct && $this->selectedProduct->satuan)
              <span class="text-muted">({{ $this->selectedProduct->satuan }})</span>
            @endif
            <span class="text-danger">*</span>
          </label>
          <input
            type="number"
            wire:model="qty"
            id="addQty"
            class="form-control"
            step="0.01"
            placeholder="0"
            required
          />
          @error('qty')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="addNote">Catatan</label>
          <textarea
            wire:model="note"
            id="addNote"
            class="form-control"
            rows="3"
            placeholder="Catatan tambahan (opsional)"
          ></textarea>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-success">
            <i class="fas fa-check"></i>
            Tambah Stok
          </button>
        </div>
      </form>
    @endif

    <!-- Form Kurangi Stok -->
    @if ($actionType === 'reduce')
      <form wire:submit="submit">
        <div class="form-group">
          <label for="reduceBatch">
            Pilih Batch
            <span class="text-danger">*</span>
          </label>
          <select wire:model="batchId" id="reduceBatch" class="form-control" required>
            <option value="">-- Pilih Batch --</option>
            @foreach ($batches as $batch)
              <option value="{{ $batch->id }}">
                {{ $batch->product->nama_produk }}
                ({{ $batch->nama_tumpukan }} - Qty: {{ number_format($batch->qty, 2) }})
              </option>
            @endforeach
          </select>
          @error('batchId')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="reduceQty">
            Jumlah Pengurangan
            @if ($this->selectedProduct && $this->selectedProduct->satuan)
              <span class="text-muted">({{ $this->selectedProduct->satuan }})</span>
            @endif
            <span class="text-danger">*</span>
          </label>
          <input
            type="number"
            wire:model="qty"
            id="reduceQty"
            class="form-control"
            step="0.01"
            placeholder="0"
            required
          />
          @error('qty')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="reduceNote">Alasan Pengurangan</label>
          <textarea
            wire:model="note"
            id="reduceNote"
            class="form-control"
            rows="3"
            placeholder="Alasan pengurangan (opsional)"
          ></textarea>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-check"></i>
            Kurangi Stok
          </button>
        </div>
      </form>
    @endif

    <!-- Form Pindah Stok -->
    @if ($actionType === 'move')
      <form wire:submit="submit">
        <div class="form-group">
          <label for="moveBatch">
            Pilih Batch Asal
            <span class="text-danger">*</span>
          </label>
          <select wire:model="batchId" id="moveBatch" class="form-control" required>
            <option value="">-- Pilih Batch --</option>
            @foreach ($batches as $batch)
              <option value="{{ $batch->id }}">
                {{ $batch->product->nama_produk }}
                ({{ $batch->nama_tumpukan }} - Qty: {{ number_format($batch->qty, 2) }})
              </option>
            @endforeach
          </select>
          @error('batchId')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="moveQty">
            Jumlah Pemindahan
            @if ($this->selectedProduct && $this->selectedProduct->satuan)
              <span class="text-muted">({{ $this->selectedProduct->satuan }})</span>
            @endif
            <span class="text-danger">*</span>
          </label>
          <input
            type="number"
            wire:model="qty"
            id="moveQty"
            class="form-control"
            step="0.01"
            placeholder="0"
            required
          />
          @error('qty')
            <span class="text-danger small">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="moveToLocationType">
              Lokasi Tujuan
              <span class="text-danger">*</span>
            </label>
            <select
              wire:model="toLocationType"
              id="moveToLocationType"
              class="form-control"
              required
            >
              @foreach ($locations as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('toLocationType')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>

          <div class="form-group col-md-6">
            <label for="moveToNamaTumpukan">
              Nama Tumpukan Tujuan
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              wire:model="toNamaTumpukan"
              id="moveToNamaTumpukan"
              class="form-control"
              placeholder="Contoh: Tumpukan B"
              required
            />
            @error('toNamaTumpukan')
              <span class="text-danger small">{{ $message }}</span>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <label for="moveNote">Alasan Pemindahan</label>
          <textarea
            wire:model="note"
            id="moveNote"
            class="form-control"
            rows="3"
            placeholder="Alasan pemindahan (opsional)"
          ></textarea>
        </div>

        <div class="form-group">
          <button type="submit" class="btn btn-info">
            <i class="fas fa-check"></i>
            Pindah Stok
          </button>
        </div>
      </form>
    @endif
  </div>
</div>

<style>
  .nav-tabs-custom {
    margin-bottom: 1rem;
  }

  .nav-tabs-custom .nav-link {
    color: #6c757d;
    border-bottom: 3px solid transparent;
    padding: 0.5rem 1rem;
    cursor: pointer;
  }

  .nav-tabs-custom .nav-link.active {
    color: #007bff;
    border-bottom: 3px solid #007bff;
  }

  .nav-tabs-custom .nav-link:hover {
    color: #0056b3;
  }
</style>

<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Produk</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Produk
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input
              wire:model.live.debounce.300ms="search"
              class="form-control"
              placeholder="Cari berdasarkan kode, nama produk, kategori atau subkategori..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">{{ $editingProductId ? 'Edit Produk' : 'Buat Produk' }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Kode Produk
                    @if (! $editingProductId)
                      <small class="text-muted">(Otomatis dari nama produk)</small>
                    @endif
                  </label>
                  <input
                    wire:model.defer="kode_produk"
                    class="form-control"
                    @if(!$editingProductId) readonly @endif
                  />
                  @error('kode_produk')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>
                    Nama Produk
                    <span class="text-danger">*</span>
                  </label>
                  <input wire:model.live="nama_produk" class="form-control" />
                  @error('nama_produk')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kategori</label>
                  <select wire:model.live="category_id" class="form-control">
                    <option value="">Pilih Kategori</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                    @endforeach
                  </select>
                  @error('category_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Subkategori</label>
                  <select wire:model.defer="subcategory_id" class="form-control">
                    <option value="">Pilih Subkategori (Opsional)</option>
                    @if ($subcategories)
                      @foreach ($subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}">
                          {{ $subcategory->nama_subkategori }}
                        </option>
                      @endforeach
                    @endif
                  </select>
                  @error('subcategory_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <textarea wire:model.defer="description" class="form-control" rows="3"></textarea>
              @error('description')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <button wire:click.prevent="save" class="btn btn-success">Simpan</button>
              <button wire:click.prevent="resetForm" class="btn btn-default">Batal</button>
            </div>
          </div>
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th class="text-center" style="width: 50px">#</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Subkategori</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($products as $product)
              <tr>
                <td class="text-center">{{ $products->firstItem() + $loop->index }}</td>
                <td><code>{{ $product->kode_produk }}</code></td>
                <td>{{ $product->nama_produk }}</td>
                <td>
                  <span class="badge badge-primary">{{ $product->category->nama_kategori }}</span>
                </td>
                <td>
                  @if ($product->subcategory)
                    <span class="badge badge-secondary">
                      {{ $product->subcategory->nama_subkategori }}
                    </span>
                  @else
                    <em class="text-muted">-</em>
                  @endif
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $product->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $product->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $product->id }})"
                      onclick="return confirm('Hapus produk?')"
                      class="btn btn-danger btn-sm"
                      title="Hapus"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div>
        {{ $products->links() }}
      </div>
    </div>
  </div>

  <!-- Modal Detail Produk -->
  @if ($showModal && $selectedProduct)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Produk</h5>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>ID:</strong></label>
                  <p>{{ $selectedProduct->id }}</p>
                </div>
                <div class="form-group">
                  <label><strong>Kode Produk:</strong></label>
                  <p><code>{{ $selectedProduct->kode_produk }}</code></p>
                </div>
                <div class="form-group">
                  <label><strong>Nama Produk:</strong></label>
                  <p>{{ $selectedProduct->nama_produk }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Kategori:</strong></label>
                  <p>
                    <span class="badge badge-primary">
                      {{ $selectedProduct->category->nama_kategori }}
                    </span>
                  </p>
                </div>
                <div class="form-group">
                  <label><strong>Subkategori:</strong></label>
                  <p>
                    @if ($selectedProduct->subcategory)
                      <span class="badge badge-secondary">
                        {{ $selectedProduct->subcategory->nama_subkategori }}
                      </span>
                    @else
                      <em class="text-muted">Tidak ada subkategori</em>
                    @endif
                  </p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label><strong>Deskripsi:</strong></label>
              <p>{{ $selectedProduct->description ?: '-' }}</p>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Dibuat:</strong></label>
                  <p>{{ $selectedProduct->created_at->format('d/m/Y H:i') }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Diubah:</strong></label>
                  <p>{{ $selectedProduct->updated_at->format('d/m/Y H:i') }}</p>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  @endif
</div>

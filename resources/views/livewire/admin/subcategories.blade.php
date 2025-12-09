<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Subkategori</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Subkategori
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
              wire:model.debounce.300ms="search"
              class="form-control"
              placeholder="Cari berdasarkan kode, nama subkategori, deskripsi atau kategori..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">
              {{ $editingSubcategoryId ? 'Edit Subkategori' : 'Buat Subkategori' }}
            </h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Kode Subkategori</label>
              <input wire:model.defer="kode_subkategori" class="form-control" />
              @error('kode_subkategori')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Nama Subkategori</label>
              <input wire:model.defer="nama_subkategori" class="form-control" />
              @error('nama_subkategori')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Kategori</label>
              <select wire:model.defer="category_id" class="form-control">
                <option value="">Pilih Kategori</option>
                @foreach ($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                @endforeach
              </select>
              @error('category_id')
                <span class="text-danger">{{ $message }}</span>
              @enderror
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
              <th>Kode Subkategori</th>
              <th>Nama Subkategori</th>
              <th>Kategori</th>
              <th>Deskripsi</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($subcategories as $subcategory)
              <tr>
                <td class="text-center">{{ $subcategory->id }}</td>
                <td><code>{{ $subcategory->kode_subkategori }}</code></td>
                <td>{{ $subcategory->nama_subkategori }}</td>
                <td>
                  <span class="badge badge-primary">
                    {{ $subcategory->category->nama_kategori }}
                  </span>
                </td>
                <td>
                  {{ $subcategory->description ?: '<em class="text-muted">Tidak ada deskripsi</em>' }}
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $subcategory->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $subcategory->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $subcategory->id }})"
                      onclick="return confirm('Hapus subkategori?')"
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
        {{ $subcategories->links() }}
      </div>
    </div>
  </div>

  <!-- Modal Detail Subkategori -->
  @if ($showModal && $selectedSubcategory)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Subkategori</h5>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label><strong>ID:</strong></label>
              <p>{{ $selectedSubcategory->id }}</p>
            </div>
            <div class="form-group">
              <label><strong>Kode Subkategori:</strong></label>
              <p>{{ $selectedSubcategory->kode_subkategori }}</p>
            </div>
            <div class="form-group">
              <label><strong>Nama Subkategori:</strong></label>
              <p>{{ $selectedSubcategory->nama_subkategori }}</p>
            </div>
            <div class="form-group">
              <label><strong>Kategori:</strong></label>
              <p>
                <span class="badge badge-primary">
                  {{ $selectedSubcategory->category->nama_kategori }}
                </span>
              </p>
            </div>
            <div class="form-group">
              <label><strong>Deskripsi:</strong></label>
              <p>{{ $selectedSubcategory->description ?: '-' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Dibuat:</strong></label>
              <p>{{ $selectedSubcategory->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="form-group">
              <label><strong>Diubah:</strong></label>
              <p>{{ $selectedSubcategory->updated_at->format('d/m/Y H:i') }}</p>
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

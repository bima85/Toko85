<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Kategori</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Kategori
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
              placeholder="Cari berdasarkan kode, nama kategori atau deskripsi..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">
              {{ $editingCategoryId ? 'Edit Kategori' : 'Buat Kategori' }}
            </h5>
          </div>
          <div class="card-body">
            <div class="form-group">
              <label>Kode Kategori</label>
              <input wire:model.defer="kode_kategori" class="form-control" />
              @error('kode_kategori')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Nama Kategori</label>
              <input wire:model.defer="nama_kategori" class="form-control" />
              @error('nama_kategori')
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
              <th>Kode Kategori</th>
              <th>Nama Kategori</th>
              <th>Deskripsi</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($categories as $category)
              <tr>
                <td class="text-center">{{ $category->id }}</td>
                <td><code>{{ $category->kode_kategori }}</code></td>
                <td>{{ $category->nama_kategori }}</td>
                <td>
                  {{ $category->description ?: '<em class="text-muted">Tidak ada deskripsi</em>' }}
                </td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $category->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $category->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $category->id }})"
                      onclick="return confirm('Hapus kategori?')"
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
        {{ $categories->links() }}
      </div>
    </div>
  </div>

  <!-- Modal Detail Kategori -->
  @if ($showModal && $selectedCategory)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Kategori</h5>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label><strong>ID:</strong></label>
              <p>{{ $selectedCategory->id }}</p>
            </div>
            <div class="form-group">
              <label><strong>Kode Kategori:</strong></label>
              <p>{{ $selectedCategory->kode_kategori }}</p>
            </div>
            <div class="form-group">
              <label><strong>Nama Kategori:</strong></label>
              <p>{{ $selectedCategory->nama_kategori }}</p>
            </div>
            <div class="form-group">
              <label><strong>Deskripsi:</strong></label>
              <p>{{ $selectedCategory->description ?: '-' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Dibuat:</strong></label>
              <p>{{ $selectedCategory->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div class="form-group">
              <label><strong>Diubah:</strong></label>
              <p>{{ $selectedCategory->updated_at->format('d/m/Y H:i') }}</p>
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

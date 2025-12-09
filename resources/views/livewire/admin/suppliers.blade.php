<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Pemasok</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Pemasok
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
              placeholder="Cari berdasarkan kode, nama, atau email..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">{{ $editingSupplierId ? 'Edit Pemasok' : 'Buat Pemasok' }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Pemasok</label>
                  <input wire:model.defer="kode_supplier" class="form-control" />
                  @error('kode_supplier')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Pemasok</label>
                  <input wire:model.defer="nama_supplier" class="form-control" />
                  @error('nama_supplier')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Telepon</label>
                  <input wire:model.defer="telepon" class="form-control" />
                  @error('telepon')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Email</label>
                  <input wire:model.defer="email" type="email" class="form-control" />
                  @error('email')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Alamat</label>
              <textarea wire:model.defer="alamat" class="form-control" rows="3"></textarea>
              @error('alamat')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="form-group">
              <label>Keterangan</label>
              <textarea wire:model.defer="keterangan" class="form-control" rows="2"></textarea>
              @error('keterangan')
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
        <table class="table table-hover table-striped elevation-1">
          <thead class="thead-light">
            <tr>
              <th class="text-center" style="width: 50px">#</th>
              <th>Kode</th>
              <th>Nama Pemasok</th>
              <th>Telepon</th>
              <th>Email</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($suppliers as $supplier)
              <tr>
                <td class="text-center">{{ $supplier->id }}</td>
                <td><code>{{ $supplier->kode_supplier }}</code></td>
                <td>{{ $supplier->nama_supplier }}</td>
                <td>{{ $supplier->telepon ?: '-' }}</td>
                <td>{{ $supplier->email ?: '-' }}</td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $supplier->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $supplier->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $supplier->id }})"
                      onclick="return confirm('Hapus pemasok?')"
                      class="btn btn-danger btn-sm"
                      title="Hapus"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data pemasok</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $suppliers->links() }}
    </div>
  </div>

  <!-- Modal Detail -->
  @if ($showModal && $selectedSupplier)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Pemasok</h4>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Kode Pemasok:</strong></label>
                  <p><code>{{ $selectedSupplier->kode_supplier }}</code></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Nama Pemasok:</strong></label>
                  <p>{{ $selectedSupplier->nama_supplier }}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Telepon:</strong></label>
                  <p>{{ $selectedSupplier->telepon ?: 'Tidak ada' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Email:</strong></label>
                  <p>{{ $selectedSupplier->email ?: 'Tidak ada' }}</p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label><strong>Alamat:</strong></label>
              <p>{{ $selectedSupplier->alamat ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Keterangan:</strong></label>
              <p>{{ $selectedSupplier->keterangan ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Dibuat:</strong></label>
              <p>{{ $selectedSupplier->created_at->format('d M Y H:i') }}</p>
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

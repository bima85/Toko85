<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Toko</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Toko
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
              placeholder="Cari berdasarkan kode, nama, atau PIC..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">{{ $editingStoreId ? 'Edit Toko' : 'Buat Toko' }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Toko</label>
                  <input wire:model.defer="kode_toko" class="form-control" />
                  @error('kode_toko')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Toko</label>
                  <input wire:model.defer="nama_toko" class="form-control" />
                  @error('nama_toko')
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
                  <label>PIC (Penanggung Jawab)</label>
                  <input wire:model.defer="pic" class="form-control" />
                  @error('pic')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipe Toko</label>
                  <select wire:model.defer="tipe_toko" class="form-control">
                    <option value="retail">Retail</option>
                    <option value="wholesale">Wholesale</option>
                    <option value="online">Online</option>
                    <option value="outlet">Outlet</option>
                  </select>
                  @error('tipe_toko')
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
              <th>Nama Toko</th>
              <th>Tipe</th>
              <th>PIC</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($stores as $store)
              <tr>
                <td class="text-center">{{ $store->id }}</td>
                <td><code>{{ $store->kode_toko }}</code></td>
                <td>{{ $store->nama_toko }}</td>
                <td>
                  <span
                    class="badge badge-{{ $store->tipe_toko === 'retail' ? 'primary' : ($store->tipe_toko === 'wholesale' ? 'success' : ($store->tipe_toko === 'online' ? 'info' : 'warning')) }}"
                  >
                    {{ ucfirst($store->tipe_toko) }}
                  </span>
                </td>
                <td>{{ $store->pic ?: '-' }}</td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $store->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $store->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $store->id }})"
                      onclick="return confirm('Hapus toko?')"
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
                <td colspan="6" class="text-center text-muted">Tidak ada data toko</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $stores->links() }}
    </div>
  </div>

  <!-- Modal Detail -->
  @if ($showModal && $selectedStore)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Toko</h4>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Kode Toko:</strong></label>
                  <p><code>{{ $selectedStore->kode_toko }}</code></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Nama Toko:</strong></label>
                  <p>{{ $selectedStore->nama_toko }}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Telepon:</strong></label>
                  <p>{{ $selectedStore->telepon ?: 'Tidak ada' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>PIC:</strong></label>
                  <p>{{ $selectedStore->pic ?: 'Tidak ada' }}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Tipe Toko:</strong></label>
                  <p>
                    <span
                      class="badge badge-{{ $selectedStore->tipe_toko === 'retail' ? 'primary' : ($selectedStore->tipe_toko === 'wholesale' ? 'success' : ($selectedStore->tipe_toko === 'online' ? 'info' : 'warning')) }}"
                    >
                      {{ ucfirst($selectedStore->tipe_toko) }}
                    </span>
                  </p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label><strong>Alamat:</strong></label>
              <p>{{ $selectedStore->alamat ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Keterangan:</strong></label>
              <p>{{ $selectedStore->keterangan ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Dibuat:</strong></label>
              <p>{{ $selectedStore->created_at->format('d M Y H:i') }}</p>
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

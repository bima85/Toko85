<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Pelanggan</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Pelanggan
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
            <h5 class="card-title">
              {{ $editingCustomerId ? 'Edit Pelanggan' : 'Buat Pelanggan' }}
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Pelanggan</label>
                  <input wire:model.defer="kode_pelanggan" class="form-control" />
                  @error('kode_pelanggan')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Pelanggan</label>
                  <input wire:model.defer="nama_pelanggan" class="form-control" />
                  @error('nama_pelanggan')
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
              <th>Nama Pelanggan</th>
              <th>Telepon</th>
              <th>Email</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($customers as $customer)
              <tr>
                <td class="text-center">{{ $customer->id }}</td>
                <td><code>{{ $customer->kode_pelanggan }}</code></td>
                <td>{{ $customer->nama_pelanggan }}</td>
                <td>{{ $customer->telepon ?: '-' }}</td>
                <td>{{ $customer->email ?: '-' }}</td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $customer->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $customer->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="delete({{ $customer->id }})"
                      onclick="return confirm('Hapus pelanggan?')"
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
                <td colspan="6" class="text-center text-muted">Tidak ada data pelanggan</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $customers->links() }}
    </div>
  </div>

  <!-- Modal Detail -->
  @if ($showModal && $selectedCustomer)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Pelanggan</h4>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Kode Pelanggan:</strong></label>
                  <p><code>{{ $selectedCustomer->kode_pelanggan }}</code></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Nama Pelanggan:</strong></label>
                  <p>{{ $selectedCustomer->nama_pelanggan }}</p>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Telepon:</strong></label>
                  <p>{{ $selectedCustomer->telepon ?: 'Tidak ada' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Email:</strong></label>
                  <p>{{ $selectedCustomer->email ?: 'Tidak ada' }}</p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label><strong>Alamat:</strong></label>
              <p>{{ $selectedCustomer->alamat ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Keterangan:</strong></label>
              <p>{{ $selectedCustomer->keterangan ?: 'Tidak ada' }}</p>
            </div>
            <div class="form-group">
              <label><strong>Dibuat:</strong></label>
              <p>{{ $selectedCustomer->created_at->format('d M Y H:i') }}</p>
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

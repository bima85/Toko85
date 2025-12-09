<div>
  @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card card-outline card-primary elevation-2">
    <div class="card-header">
      <h3 class="card-title">Manajemen Unit</h3>
      <div class="card-tools">
        <button wire:click="create" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i>
          Buat Unit
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
              placeholder="Cari berdasarkan kode atau nama unit..."
              type="search"
            />
          </div>
        </div>
      </div>

      @if ($showForm)
        <div class="card card-outline card-primary mb-3">
          <div class="card-header">
            <h5 class="card-title">{{ $editingUnitId ? 'Edit Unit' : 'Buat Unit' }}</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Kode Unit</label>
                  <input wire:model.defer="kode_unit" class="form-control" />
                  @error('kode_unit')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nama Unit</label>
                  <input wire:model.defer="nama_unit" class="form-control" />
                  @error('nama_unit')
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

            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <input
                      wire:model.defer="is_base_unit"
                      type="checkbox"
                      class="custom-control-input"
                      id="is_base_unit"
                    />
                    <label class="custom-control-label" for="is_base_unit">Unit Dasar</label>
                  </div>
                  <small class="form-text text-muted">
                    Centang jika ini adalah unit dasar (contoh: Kg, Liter)
                  </small>
                  @error('is_base_unit')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Unit Acuan</label>
                  <select
                    wire:model.defer="parent_unit_id"
                    class="form-control"
                    {{ $is_base_unit ? 'disabled' : '' }}
                  >
                    <option value="">Pilih Unit Acuan (Opsional)</option>
                    @if (isset($availableUnits))
                      @foreach ($availableUnits as $availableUnit)
                        <option value="{{ $availableUnit->id }}">
                          {{ $availableUnit->nama_unit }} ({{ $availableUnit->kode_unit }})
                        </option>
                      @endforeach
                    @endif
                  </select>
                  <small class="form-text text-muted">Unit yang menjadi acuan konversi</small>
                  @error('parent_unit_id')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Nilai Konversi</label>
                  <input
                    wire:model.defer="conversion_value"
                    type="number"
                    step="0.000001"
                    class="form-control"
                    {{ $is_base_unit ? 'disabled' : '' }}
                    placeholder="Contoh: 1000"
                  />
                  <small class="form-text text-muted">Nilai konversi ke unit acuan</small>
                  @error('conversion_value')
                    <span class="text-danger">{{ $message }}</span>
                  @enderror
                </div>
              </div>
            </div>

            <div class="form-group">
              <button wire:click.prevent="save" type="button" class="btn btn-success">
                Simpan
              </button>
              <button wire:click="cancel" type="button" class="btn btn-default">Batal</button>
            </div>
          </div>
        </div>
      @endif

      <div class="table-responsive">
        <table class="table table-hover table-striped elevation-1">
          <thead class="thead-light">
            <tr>
              <th class="text-center" style="width: 50px">#</th>
              <th>Kode Unit</th>
              <th>Nama Unit</th>
              <th>Konversi</th>
              <th>Deskripsi</th>
              <th class="text-center" style="width: 150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($units as $unit)
              <tr>
                <td class="text-center">{{ $unit->id }}</td>
                <td><code>{{ $unit->kode_unit }}</code></td>
                <td>
                  {{ $unit->nama_unit }}
                  @if ($unit->is_base_unit)
                    <span class="badge badge-primary">Dasar</span>
                  @endif
                </td>
                <td>
                  @if ($unit->parent_unit_id && $unit->conversion_value)
                    1 {{ $unit->nama_unit }} =
                    {{ rtrim(rtrim(number_format($unit->conversion_value, 2, ',', '.'), '0'), ',') }}
                    {{ $unit->parentUnit->nama_unit ?? 'Unit Acuan' }}
                  @else
                    -
                  @endif
                </td>
                <td>{{ $unit->description ?: '-' }}</td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button
                      wire:click="show({{ $unit->id }})"
                      class="btn btn-info btn-sm"
                      title="Lihat Detail"
                    >
                      <i class="fas fa-eye"></i>
                    </button>
                    <button
                      wire:click="edit({{ $unit->id }})"
                      class="btn btn-primary btn-sm"
                      title="Edit"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      wire:click="confirmDelete({{ $unit->id }})"
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
                <td colspan="6" class="text-center text-muted">Tidak ada data unit</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{ $units->links() }}
    </div>
  </div>

  <!-- Modal Detail -->
  @if ($showModal && $selectedUnit)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Detail Unit</h4>
            <button type="button" class="close" wire:click="closeModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Kode Unit:</strong></label>
                  <p><code>{{ $selectedUnit->kode_unit }}</code></p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Nama Unit:</strong></label>
                  <p>{{ $selectedUnit->nama_unit }}</p>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label><strong>Deskripsi:</strong></label>
              <p>{{ $selectedUnit->description ?: 'Tidak ada deskripsi' }}</p>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Tipe Unit:</strong></label>
                  <p>
                    @if ($selectedUnit->is_base_unit)
                      <span class="badge badge-primary">Unit Dasar</span>
                    @else
                      <span class="badge badge-secondary">Unit Turunan</span>
                    @endif
                  </p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label><strong>Konversi:</strong></label>
                  <p>
                    @if ($selectedUnit->parent_unit_id && $selectedUnit->conversion_value)
                      1 {{ $selectedUnit->nama_unit }} =
                      {{ rtrim(rtrim(number_format($selectedUnit->conversion_value, 2, ',', '.'), '0'), ',') }}
                      {{ $selectedUnit->parentUnit->nama_unit ?? 'Unit Acuan' }}
                    @else
                      -
                    @endif
                  </p>
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

  <!-- Modal Konfirmasi Hapus -->
  @if ($confirmingDelete)
    <div class="modal fade show" style="display: block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Konfirmasi Hapus</h4>
            <button type="button" class="close" wire:click="cancelDelete">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus unit ini?</p>
            <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan.</strong></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Batal</button>
            <button type="button" class="btn btn-danger" wire:click="deleteConfirmed">Hapus</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show"></div>
  @endif
</div>

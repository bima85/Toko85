<div id="sales-component-root">
  @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('success') }}
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-ban"></i>
      {{ session('error') }}
    </div>
  @endif

  <!-- Create Form -->
  @if ($showCreateForm)
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">
          <i class="fas fa-plus-circle mr-2"></i>
          {{ $editingSaleId ? 'Edit Penjualan' : 'Buat Penjualan Baru' }}
        </h3>
        <div class="card-tools">
          <button wire:click="cancel" type="button" class="btn btn-tool">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <!-- Form content here -->
        <p>Form penjualan akan ditampilkan di sini.</p>
      </div>
      <div class="card-footer">
        <button wire:click="addItem" type="button" class="btn btn-info">
          <i class="fas fa-plus-circle mr-1"></i>
          Tambah Item
        </button>
        <div class="float-right">
          <button wire:click="cancel" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Batal
          </button>
          <button wire:click="save" class="btn btn-success ml-2">
            <i class="fas fa-save"></i>
            Simpan Penjualan
          </button>
        </div>
      </div>
    </div>
  @endif

  <!-- Inline Create Customer Modal -->
  @if ($showCreateCustomerModal)
    <style>
      /* Ensure modal fits inside viewport and body becomes scrollable when needed */
      .livewire-modal {
        position: fixed;
        inset: 0; /* top:0; right:0; bottom:0; left:0 */
        z-index: 99999; /* place above other app elements */
        display: block;
      }
      /* keep backdrop just under modal but above page */
      .modal-backdrop.show {
        z-index: 99998 !important;
      }
      .livewire-modal .modal-dialog {
        max-width: 720px;
        margin: 1.5rem auto;
      }
      .livewire-modal .modal-content {
        max-height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
      }
      .livewire-modal .modal-body {
        overflow-y: auto;
      }
      @media (max-width: 576px) {
        .livewire-modal .modal-dialog {
          max-width: 95%;
          margin: 0.5rem auto;
        }
      }
    </style>

    <div
      class="livewire-modal modal fade show d-block"
      tabindex="-1"
      role="dialog"
      style="
        display: block;
        background: rgba(0, 0, 0, 0.4);
        z-index: 200000 !important;
        position: fixed;
        inset: 0;
      "
    >
      <div class="modal-backdrop fade show" style="z-index: 199999 !important"></div>
      <div
        class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
        role="document"
        style="z-index: 200001; position: relative"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Buat Pelanggan Baru</h5>
            <button
              type="button"
              class="close"
              aria-label="Close"
              wire:click="$set('showCreateCustomerModal', false)"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Pelanggan</label>
              <input type="text" class="form-control" wire:model.defer="new_customer_nama" />
              @error('new_customer_nama')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label>Telepon</label>
              <input type="text" class="form-control" wire:model.defer="new_customer_telepon" />
              @error('new_customer_telepon')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control" wire:model.defer="new_customer_email" />
              @error('new_customer_email')
                <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group">
              <label>Alamat</label>
              <textarea
                class="form-control"
                rows="3"
                wire:model.defer="new_customer_alamat"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              wire:click="$set('showCreateCustomerModal', false)"
            >
              Batal
            </button>
            <button type="button" class="btn btn-primary" wire:click="saveNewCustomer">
              Simpan Pelanggan
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-backdrop fade show" style="z-index: 199999 !important"></div>
  @endif

  <!-- List View -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-list mr-2"></i>
        Daftar Penjualan
      </h3>
      <div class="card-tools">
        @if (! $showCreateForm)
          <button wire:click="create" class="btn btn-success">
            <i class="fas fa-plus-circle"></i>
            Buat Penjualan
          </button>
        @endif
      </div>
    </div>
    <div class="card-body">
      @if ($sales->count() > 0)
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sales as $sale)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $sale->no_invoice }}</td>
                  <td>{{ $sale->tanggal_penjualan?->format('d/m/Y') }}</td>
                  <td>{{ $sale->customer?->nama_pelanggan }}</td>
                  <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                  <td>
                    @if ($sale->status === 'completed')
                      <span class="badge badge-success">Completed</span>
                    @else
                      <span class="badge badge-warning">{{ ucfirst($sale->status) }}</span>
                    @endif
                  </td>
                  <td>
                    <button wire:click="edit({{ $sale->id }})" class="btn btn-sm btn-info">
                      Edit
                    </button>
                    <button wire:click="delete({{ $sale->id }})" class="btn btn-sm btn-danger">
                      Hapus
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        {{ $sales->links() }}
      @else
        <div class="alert alert-info">Belum ada data penjualan.</div>
      @endif
    </div>
  </div>
</div>

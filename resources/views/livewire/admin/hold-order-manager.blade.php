<div class="container-fluid">
  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="info-box bg-info">
        <span class="info-box-icon"><i class="fas fa-pause"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Hold Aktif</span>
          <span class="info-box-number">{{ $summary['active_holds'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-success">
        <span class="info-box-icon"><i class="fas fa-check"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Selesai Hari Ini</span>
          <span class="info-box-number">{{ $summary['completed_today'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-danger">
        <span class="info-box-icon"><i class="fas fa-times"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Dibatalkan Hari Ini</span>
          <span class="info-box-number">{{ $summary['cancelled_today'] }}</span>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="info-box bg-warning">
        <span class="info-box-icon"><i class="fas fa-cube"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Qty Hold</span>
          <span class="info-box-number">{{ $summary['total_hold_qty'] }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Search & Filter -->
  <div class="card mb-3">
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <input
            type="text"
            class="form-control"
            placeholder="Cari No Invoice atau Nama Customer..."
            wire:model.live="search"
          />
        </div>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs card-header-tabs" role="tablist">
        <li class="nav-item">
          <a
            class="nav-link {{ $activeTab === 'active_holds' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('activeTab', 'active_holds')"
          >
            <i class="fas fa-pause"></i>
            Hold Aktif ({{ $summary['active_holds'] }})
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link {{ $activeTab === 'completed' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('activeTab', 'completed')"
          >
            <i class="fas fa-check"></i>
            Selesai
          </a>
        </li>
        <li class="nav-item">
          <a
            class="nav-link {{ $activeTab === 'cancelled' ? 'active' : '' }}"
            href="#"
            wire:click.prevent="$set('activeTab', 'cancelled')"
          >
            <i class="fas fa-times"></i>
            Dibatalkan
          </a>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <!-- Table Orders -->
      @if ($orders->count())
        <div class="table-responsive">
          <table class="table table-hover">
            <thead class="table-light">
              <tr>
                <th>No Invoice</th>
                <th>Customer</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Items</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $item)
                @if ($item->hold_type === 'order')
                  <!-- Hold Order Row -->
                  <tr>
                    <td>
                      <strong>{{ $item->no_invoice }}</strong>
                      <br>
                      <small class="badge bg-info">Order</small>
                    </td>
                    <td>{{ $item->customer->nama_customer }}</td>
                    <td>
                      @if ($activeTab === 'active_holds')
                        <small class="text-muted">
                          {{ $item->held_at?->diffForHumans() }}
                        </small>
                      @elseif ($activeTab === 'completed')
                        <small class="text-success">
                          {{ $item->completed_at?->format('d/m/Y H:i') }}
                        </small>
                      @else
                        <small class="text-danger">
                          {{ $item->cancelled_at?->format('d/m/Y H:i') }}
                        </small>
                      @endif
                    </td>
                    <td>
                      @if ($item->status === 'hold')
                        <span class="badge bg-warning">
                          <i class="fas fa-pause"></i>
                          Hold
                        </span>
                      @elseif ($item->status === 'completed')
                        <span class="badge bg-success">
                          <i class="fas fa-check"></i>
                          Selesai
                        </span>
                      @elseif ($item->status === 'cancelled')
                        <span class="badge bg-danger">
                          <i class="fas fa-times"></i>
                          Batal
                        </span>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-secondary">
                        {{ $item->saleItems->sum('qty') }} item(s)
                      </span>
                    </td>
                    <td>
                      <button
                        class="btn btn-sm btn-info"
                        wire:click="viewOrder({{ $item->id }})"
                        data-bs-toggle="modal"
                        data-bs-target="#orderDetailModal"
                      >
                        <i class="fas fa-eye"></i>
                        Detail
                      </button>

                      @if ($activeTab === 'active_holds')
                        <button
                          class="btn btn-sm btn-success"
                          wire:click="confirmAction({{ $item->id }}, 'complete')"
                        >
                          <i class="fas fa-check"></i>
                          Selesai
                        </button>
                        <button
                          class="btn btn-sm btn-danger"
                          wire:click="confirmAction({{ $item->id }}, 'cancel')"
                        >
                          <i class="fas fa-times"></i>
                          Batalkan
                        </button>
                      @endif
                    </td>
                  </tr>
                @else
                  <!-- Hold Stock Batch Row -->
                  <tr class="table-light">
                    <td>
                      <strong>{{ $item->nama_tumpukan }}</strong>
                      <br>
                      <small class="badge bg-success">Stock Batch</small>
                    </td>
                    <td>
                      {{ $item->product->nama_produk }}
                      <br>
                      <small class="text-muted">{{ $item->product->kode_produk }}</small>
                    </td>
                    <td>
                      <small class="text-muted">
                        {{ $item->created_at->diffForHumans() }}
                      </small>
                    </td>
                    <td>
                      <span class="badge bg-warning">
                        <i class="fas fa-cube"></i>
                        Hold
                      </span>
                      @if ($item->location_type === 'store')
                        <br>
                        <small class="badge bg-primary">Toko</small>
                      @else
                        <br>
                        <small class="badge bg-info">Gudang</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-secondary">
                        {{ number_format($item->qty, 2) }} {{ $item->product->satuan }}
                      </span>
                    </td>
                    <td>
                      @if ($activeTab === 'active_holds')
                        <button
                          class="btn btn-sm btn-success"
                          wire:click="confirmBatchAction({{ $item->id }}, 'complete')"
                        >
                          <i class="fas fa-check"></i>
                          Selesai
                        </button>
                        <button
                          class="btn btn-sm btn-danger"
                          wire:click="confirmBatchAction({{ $item->id }}, 'cancel')"
                        >
                          <i class="fas fa-times"></i>
                          Batalkan
                        </button>
                      @else
                        <a href="{{ route('stock-batches.index') }}" class="btn btn-sm btn-primary">
                          <i class="fas fa-edit"></i>
                          Kelola
                        </a>
                      @endif
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
          {{ $orders->links() }}
        </div>
      @else
        <div class="alert alert-info text-center">
          <i class="fas fa-info-circle"></i>
          Tidak ada data untuk kategori ini
        </div>
      @endif
    </div>
  </div>

  <!-- Order Detail Modal -->
  @if ($selectedOrder && $holdType !== 'batch')
    <div class="modal fade" id="orderDetailModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-receipt"></i>
              Order #{{ $selectedOrder->no_invoice }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <!-- Customer Info -->
            <div class="row mb-3">
              <div class="col-md-6">
                <h6><strong>Customer</strong></h6>
                <p>{{ $selectedOrder->customer->nama_customer }}</p>
              </div>
              <div class="col-md-6">
                <h6><strong>Tanggal Order</strong></h6>
                <p>{{ $selectedOrder->tanggal_penjualan?->format('d/m/Y H:i') }}</p>
              </div>
            </div>

            <!-- Items Ordered -->
            <h6>
              <strong>
                <i class="fas fa-cube"></i>
                Item yang Dipesan
              </strong>
            </h6>
            <div class="table-responsive mb-3">
              <table class="table table-sm">
                <thead class="table-light">
                  <tr>
                    <th>Produk</th>
                    <th class="text-end">Qty</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($selectedOrder->saleItems as $item)
                    <tr>
                      <td>{{ $item->product->nama_produk }}</td>
                      <td class="text-end">{{ $item->qty }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Hold Status -->
            @if ($selectedOrder->status === 'hold' && isset($selectedOrder->hold_info))
              <h6>
                <strong>
                  <i class="fas fa-pause"></i>
                  Status Hold
                </strong>
              </h6>
              <div class="alert alert-warning">
                <p class="mb-2">
                  <strong>Ditahan sejak:</strong>
                  {{ $selectedOrder->held_at?->diffForHumans() }}
                </p>
                <h6 class="mt-3">Stok yang Ditahan:</h6>
                <ul class="mb-0">
                  @foreach ($selectedOrder->hold_info as $hold)
                    <li>
                      <strong>{{ $hold['product_name'] }}</strong>
                      <br />
                      <small>
                        Tumpukan: {{ $hold['tumpukan'] }} | Qty: {{ $hold['qty_hold'] }}
                      </small>
                    </li>
                  @endforeach
                </ul>
              </div>
            @endif

            <!-- Completion Info -->
            @if ($selectedOrder->status === 'completed')
              <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <strong>Transaksi Selesai</strong>
                <br />
                {{ $selectedOrder->completed_at?->format('d/m/Y H:i') }}
              </div>
            @elseif ($selectedOrder->status === 'cancelled')
              <div class="alert alert-danger">
                <i class="fas fa-times-circle"></i>
                <strong>Order Dibatalkan</strong>
                <br />
                {{ $selectedOrder->cancelled_at?->format('d/m/Y H:i') }}
              </div>
            @endif

            <!-- Notes -->
            @if ($selectedOrder->keterangan)
              <h6><strong>Catatan</strong></h6>
              <p class="text-muted">{{ $selectedOrder->keterangan }}</p>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Confirmation Dialog -->
  @if ($showConfirmDialog && $selectedOrder)
    <div
      class="modal fade show"
      id="confirmModal"
      style="display: block; background: rgba(0, 0, 0, 0.5)"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              @if ($actionType === 'complete')
                <i class="fas fa-check"></i>
                Selesaikan Hold
              @else
                <i class="fas fa-times"></i>
                Batalkan Hold
              @endif
            </h5>
            <button
              type="button"
              class="btn-close"
              wire:click="$set('showConfirmDialog', false)"
            ></button>
          </div>
          <div class="modal-body">
            @if ($actionType === 'complete')
              <p>
                Apakah Anda yakin ingin
                <strong>menyelesaikan transaksi</strong>
                untuk order ini?
              </p>
              <p class="text-muted">Stok akan dikurangi dari tumpukan hold.</p>
            @else
              <p>
                Apakah Anda yakin ingin
                <strong>membatalkan hold</strong>
                untuk order ini?
              </p>
              <p class="text-muted">Stok akan dikembalikan ke tumpukan asli.</p>
            @endif
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              wire:click="$set('showConfirmDialog', false)"
            >
              Batal
            </button>
            @if ($actionType === 'complete')
              <button type="button" class="btn btn-success" wire:click="completeHold">
                <i class="fas fa-check"></i>
                Selesaikan
              </button>
            @else
              <button type="button" class="btn btn-danger" wire:click="cancelHold">
                <i class="fas fa-times"></i>
                Batalkan
              </button>
            @endif
          </div>
        </div>
      </div>
    </div>
  @endif
</div>

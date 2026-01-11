<div>
  <div class="card card-outline card-primary">
    <div class="card-header">
      <h3 class="card-title">
        <i class="fas fa-truck mr-2"></i>
        Daftar Surat Jalan
      </h3>
      <div class="card-tools">
        <a href="{{ route('admin.sales') }}" class="btn btn-sm btn-secondary">
          Kembali ke Penjualan
        </a>
      </div>
    </div>

    <div class="card-body">
      @if ($sales->count())
        <div class="table-responsive">
          <table class="table table-sm table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>No Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Delivery No</th>
                <th>Delivery Date</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($sales as $sale)
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><strong>{{ $sale->no_invoice }}</strong></td>
                  <td>{{ $sale->tanggal_penjualan?->format('d/m/Y') ?? '-' }}</td>
                  <td>{{ $sale->customer?->nama_pelanggan ?? '-' }}</td>
                  <td>{{ $sale->delivery_note_number ?? '-' }}</td>
                  <td>{{ $sale->delivery_date?->format('d/m/Y') ?? '-' }}</td>
                  <td>
                    <a
                      href="{{ route('admin.delivery-notes.print', $sale) }}"
                      target="_blank"
                      class="btn btn-sm btn-primary"
                    >
                      <i class="fas fa-print"></i>
                      Cetak
                    </a>
                    <a
                      href="{{ route('admin.sales') }}?view={{ $sale->id }}"
                      class="btn btn-sm btn-info"
                    >
                      <i class="fas fa-eye"></i>
                      Lihat
                    </a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="alert alert-info">Belum ada surat jalan tersedia.</div>
      @endif
    </div>
  </div>
</div>

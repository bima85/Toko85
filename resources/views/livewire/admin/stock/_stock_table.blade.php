<div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead>
      <tr>
        <th style="width: 40px">#</th>
        <th>Kode</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Sub Kategori</th>
        <th>Unit</th>
        <th>Tanggal Transaksi</th>
        <th>Batch</th>
        <th>Lokasi</th>
        <th>Keterangan</th>
        <th class="text-right">Stok Akhir</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($rows as $i => $row)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $row['kode_produk'] }}</td>
          <td>{{ $row['nama_produk'] }}</td>
          <td>{{ $row['kategori'] }}</td>
          <td>{{ $row['sub_kategori'] }}</td>
          <td>{{ $row['unit'] }}</td>
          <td>
            {{ $row['last_tx'] ? \Carbon\Carbon::parse($row['last_tx'])->format('d/m/Y H:i') : '—' }}
          </td>
          <td>
            {{ $row['last_tx'] ? \Carbon\Carbon::parse($row['last_tx'])->format('d/m/Y H:i') : '—' }}
          </td>
          <td>
            @if (! empty($row['batches']))
              @foreach ($row['batches'] as $b)
                <div class="small">
                  <strong>{{ $b['nama_tumpukan'] }}</strong>
                  <span class="text-muted">
                    ({{ number_format($b['qty'], 0, ',', '.') }} {{ $row['unit'] ?? '' }})
                  </span>
                </div>
              @endforeach
            @else
              —
            @endif
          </td>
          <td>{{ $row['lokasi'] ?? '—' }}</td>
          <td>
            @if (! empty($row['has_hold']))
              <span class="badge bg-warning text-dark">
                Hold: {{ number_format($row['hold_qty'], 0, ',', '.') }}
              </span>
              <a
                href="{{ route('admin.hold-orders', ['product_id' => $row['product_id']]) }}"
                class="btn btn-sm btn-link"
              >
                Lihat Hold
              </a>
            @else
              —
            @endif
          </td>
          <td class="text-right">{{ number_format($row['stok_akhir'], 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="11" class="text-center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

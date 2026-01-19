<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Cetak Penjualan - {{ $sale->no_invoice }}</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        color: #111;
      }
      .container {
        max-width: 800px;
        margin: 0 auto;
      }
      .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
      }
      th,
      td {
        border: 1px solid #ddd;
        padding: 8px;
      }
      th {
        background: #f5f5f5;
      }
      .text-right {
        text-align: right;
      }
      @media print {
        .no-print {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="header">
        <div>
          <h2>INVOICE</h2>
          <div>
            No:
            <strong>{{ $sale->no_invoice }}</strong>
          </div>
          <div>Tanggal: {{ $sale->tanggal_penjualan?->format('d/m/Y') ?? '-' }}</div>
        </div>
        <div>
          <div>
            Pelanggan:
            <strong>{{ $sale->customer?->nama_pelanggan ?? '-' }}</strong>
          </div>
          <div>
            Lokasi: {{ $sale->store?->nama_toko ?? ($sale->warehouse?->nama_gudang ?? '-') }}
          </div>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Batch</th>
            <th>Qty</th>
            <th>Satuan</th>
            <th class="text-right">Harga</th>
            <th class="text-right">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sale->saleItems as $i => $it)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $it->product?->nama_produk ?? '-' }}</td>
              <td>{{ $it->batch?->nama_tumpukan ?? '-' }}</td>
              <td class="text-right">{{ number_format($it->qty ?? 0, 0) }}</td>
              <td>{{ $it->unit?->nama_unit ?? '-' }}</td>
              <td class="text-right">Rp {{ number_format($it->harga_jual ?? 0, 0, ',', '.') }}</td>
              <td class="text-right">
                Rp {{ number_format($it->qty * $it->harga_jual ?? 0, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="6" class="text-right"><strong>Total</strong></td>
            <td class="text-right">
              <strong>
                Rp
                {{ number_format($sale->total_amount ?? $sale->saleItems->sum(fn ($it) => $it->qty * $it->harga_jual), 0, ',', '.') }}
              </strong>
            </td>
          </tr>
        </tfoot>
      </table>

      <div style="margin-top: 20px">
        <div>Catatan: {{ $sale->keterangan ?? '-' }}</div>
      </div>

      <div style="margin-top: 30px; display: flex; justify-content: space-between">
        <div>
          Penjual,
          <br />
          <br />
          <br />
          ________________
        </div>
        <div>
          Penerima,
          <br />
          <br />
          <br />
          ________________
        </div>
      </div>

      <div style="margin-top: 20px">
        <button class="no-print" onclick="window.print()">Cetak</button>
        <button class="no-print" onclick="window.close()">Tutup</button>
      </div>
    </div>
  </body>
</html>

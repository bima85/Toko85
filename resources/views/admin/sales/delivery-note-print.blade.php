<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Surat Jalan</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        color: #222;
        padding: 20px;
      }
      .header {
        text-align: center;
        margin-bottom: 12px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      th,
      td {
        border: 1px solid #ddd;
        padding: 8px;
      }
      th {
        background: #f4f4f4;
      }
      .text-right {
        text-align: right;
      }
      .no-border {
        border: none;
      }
      .controls {
        margin-bottom: 12px;
      }
      @media print {
        .controls {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="controls">
      <button onclick="window.print()">Cetak</button>
      <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
      <h2>SURAT JALAN</h2>
      <div>PT. Your Company Name</div>
    </div>

    <table class="no-border" style="margin-bottom: 12px">
      <tr class="no-border">
        <td class="no-border" style="width: 50%">
          <strong>No. Surat Jalan:</strong>
          {{ $deliveryNoteNumber ?? '-' }}
          <br />
          <strong>Tanggal:</strong>
          {{ $deliveryDate ?? date('d/m/Y') }}
        </td>
        <td class="no-border">
          <strong>Kepada:</strong>
          {{ optional(\App\Models\Customer::find($customerId))->nama_pelanggan ?? '-' }}
          <br />
          <strong>Alamat:</strong>
          {{ optional(\App\Models\Customer::find($customerId))->alamat ?? '-' }}
        </td>
      </tr>
    </table>

    <table>
      <thead>
        <tr>
          <th style="width: 50px">No</th>
          <th>Nama Produk</th>
          <th style="width: 120px">Jumlah</th>
          <th style="width: 120px">Satuan</th>
          <th style="width: 200px">Batch</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($saleItems as $index => $item)
          @if (! empty($item['product_id']))
            @php
              $product = \App\Models\Product::find($item['product_id']);
              $unit = \App\Models\Unit::find($item['unit_id']);
              $batch = ! empty($item['batch_id']) ? \App\Models\StockBatch::find($item['batch_id']) : null;
            @endphp

            <tr>
              <td class="text-center">{{ $index + 1 }}</td>
              <td>{{ $product?->nama_produk ?? '-' }}</td>
              <td class="text-right">{{ number_format($item['qty'] ?? 0, 0) }}</td>
              <td>{{ $unit?->nama_unit ?? '-' }}</td>
              <td>{{ $batch?->nama_tumpukan ?? '-' }}</td>
            </tr>
          @endif
        @endforeach
      </tbody>
    </table>

    <div style="margin-top: 24px">
      <strong>Catatan Pengiriman:</strong>
      <div style="border: 1px solid #eee; padding: 8px; min-height: 40px">
        {{ $deliveryNotes ?? '' }}
      </div>
    </div>
  </body>
</html>

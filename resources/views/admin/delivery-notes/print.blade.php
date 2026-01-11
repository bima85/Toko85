<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Surat Jalan - {{ $sale->no_invoice }}</title>
    <link rel="stylesheet" href="/build/css/app.css" />
    <style>
      body {
        font-family: Arial, Helvetica, sans-serif;
        color: #222;
      }
      .nota {
        max-width: 800px;
        margin: 0 auto;
      }
      .nota h1 {
        text-align: center;
        margin-bottom: 0;
      }
      .nota .meta {
        margin: 1rem 0;
      }
      table {
        width: 100%;
        border-collapse: collapse;
      }
      table th,
      table td {
        border: 1px solid #ccc;
        padding: 6px;
      }
      .text-right {
        text-align: right;
      }
    </style>
  </head>
  <body onload="setTimeout(function(){ window.print(); }, 250);">
    <div class="nota">
      <h1>SURAT JALAN</h1>
      <p style="text-align: center; margin-top: 0; color: #666">PT. Your Company Name</p>

      <div class="meta">
        <table style="border: 0">
          <tr>
            <td style="border: 0"><strong>No. Surat Jalan</strong></td>
            <td style="border: 0">: {{ $sale->delivery_note_number ?? $sale->no_invoice }}</td>
            <td style="border: 0"><strong>Kepada</strong></td>
            <td style="border: 0">: {{ $sale->customer?->nama_pelanggan ?? '-' }}</td>
          </tr>
          <tr>
            <td style="border: 0"><strong>Tanggal</strong></td>
            <td style="border: 0">
              :
              {{ $sale->delivery_date?->format('d/m/Y') ?? ($sale->tanggal_penjualan?->format('d/m/Y') ?? '-') }}
            </td>
            <td style="border: 0"><strong>Alamat</strong></td>
            <td style="border: 0">: {{ $sale->customer?->alamat ?? '-' }}</td>
          </tr>
        </table>
      </div>

      <table>
        <thead>
          <tr>
            <th width="50">No</th>
            <th>Nama Produk</th>
            <th width="120">Jumlah</th>
            <th width="100">Satuan</th>
            <th width="150">Batch</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sale->saleItems as $idx => $it)
            <tr>
              <td class="text-center">{{ $idx + 1 }}</td>
              <td>{{ $it->product?->nama_produk ?? '-' }}</td>
              <td class="text-right">{{ number_format($it->qty ?? 0, 0) }}</td>
              <td>{{ $it->unit?->nama_unit ?? '-' }}</td>
              <td>
                {{ optional($it->batch)->nama_tumpukan ?? ($it->batch_id ? 'Batch #' . $it->batch_id : '-') }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div style="margin-top: 1.5rem">
        <strong>Catatan:</strong>
        <div style="min-height: 60px; border: 1px solid #eee; padding: 8px">
          {{ $sale->delivery_notes ?? '-' }}
        </div>
      </div>

      <div style="margin-top: 2rem; display: flex; justify-content: space-between">
        <div>
          <div>Pengirim</div>
          <div style="margin-top: 48px">(________________)</div>
        </div>
        <div>
          <div>Penerima</div>
          <div style="margin-top: 48px">(________________)</div>
        </div>
      </div>
    </div>
  </body>
</html>

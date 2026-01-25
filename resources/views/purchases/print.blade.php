<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembelian - {{ $purchase->no_invoice }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .receipt {
            width: 80mm;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ddd;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .info-section {
            margin-bottom: 12px;
            font-size: 11px;
        }

        .info-section label {
            font-weight: bold;
            display: inline-block;
            width: 35%;
        }

        .info-section span {
            display: inline-block;
            width: 65%;
            word-wrap: break-word;
        }

        .separator {
            border-top: 1px solid #ddd;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }

        table thead {
            background: #f5f5f5;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
        }

        table th {
            padding: 5px 2px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }

        table td {
            padding: 6px 2px;
            border-bottom: 1px solid #eee;
        }

        table tr:last-child td {
            border-bottom: 1px solid #333;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin: 10px 0;
            font-weight: bold;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed #ddd;
        }

        .total-row.grand-total {
            border-bottom: 2px solid #333;
            border-top: 2px solid #333;
            padding: 8px 0;
            font-size: 13px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .receipt {
                width: 80mm;
                margin: 0;
                padding: 10px;
                border: none;
            }

            .no-print {
                display: none;
            }
        }

        .button-section {
            text-align: center;
            margin-top: 20px;
            no-print: 1;
        }

        button {
            padding: 8px 20px;
            margin: 0 5px;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <h1>NOTA PEMBELIAN</h1>
            <p>{{ $purchase->no_invoice }}</p>
        </div>

        <!-- Info Pembelian -->
        <div class="info-section">
            <label>Tanggal:</label>
            <span>{{ $purchase->tanggal_pembelian->format('d/m/Y H:i') }}</span>
        </div>

        <div class="info-section">
            <label>Supplier:</label>
            <span>{{ $purchase->supplier?->nama_supplier ?? '-' }}</span>
        </div>

        @if ($purchase->store_id)
        <div class="info-section">
            <label>Toko:</label>
            <span>{{ $purchase->store?->nama_toko ?? '-' }}</span>
        </div>
        @elseif($purchase->warehouse_id)
        <div class="info-section">
            <label>Gudang:</label>
            <span>{{ $purchase->warehouse?->nama_gudang ?? '-' }}</span>
        </div>
        @endif

        @if ($purchase->keterangan)
        <div class="info-section">
            <label>Keterangan:</label>
            <span>{{ $purchase->keterangan }}</span>
        </div>
        @endif

        <div class="separator"></div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 40%">Produk</th>
                    <th style="width: 10%; text-align: right;">Qty</th>
                    <th style="width: 15%; text-align: right;">Harga</th>
                    <th style="width: 15%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->purchaseItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product?->nama_produk ?? '-' }}</td>
                    <td class="text-right">
                        {{ $item->qty ?? 0 }}
                        @if ($item->unit)
                        <small>{{ $item->unit->nama_unit }}</small>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->harga_beli ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @php
                        $qty = $item->qty ?? 0;
                        $harga = $item->harga_beli ?? 0;
                        $conv = $item->unit?->conversion_value ?? 1;
                        $total = ($qty * $conv) * $harga;
                        @endphp
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($purchase->purchaseItems->sum(fn($item) => ($item->qty *
                    ($item->unit?->conversion_value ?? 1)) * ($item->harga_beli ?? 0)), 0, ',', '.') }}</span>
            </div>
            <div class="total-row grand-total">
                <span>TOTAL PEMBELIAN:</span>
                <span>Rp {{ number_format($purchase->purchaseItems->sum(fn($item) => ($item->qty *
                    ($item->unit?->conversion_value ?? 1)) * ($item->harga_beli ?? 0)), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Status -->
        <div class="info-section" style="margin-top: 10px;">
            <label>Status:</label>
            <span>
                @if ($purchase->status === 'completed')
                <strong style="color: green;">Completed</strong>
                @elseif($purchase->status === 'pending')
                <strong style="color: orange;">Pending</strong>
                @else
                <strong style="color: red;">Cancelled</strong>
                @endif
            </span>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Terima kasih telah berbisnis dengan kami</p>
        </div>
    </div>

    <!-- Print Buttons -->
    <div class="button-section no-print">
        <button onclick="window.print()">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()">
            <i class="fas fa-times"></i> Tutup
        </button>
    </div>

    <script>
        // Auto print for better UX
        // window.addEventListener('load', function() {
        //     window.print();
        // });
    </script>
</body>

</html>
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromArray, WithHeadings
{
    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Kode Transaksi',
            'Tipe',
            'Tanggal',
            'Status',
            'User',
            'Produk',
            'Qty',
            'Satuan',
            'Harga Jual',
            'Harga Beli',
            'Subtotal',
            'Total Transaksi',
            'Deskripsi',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\StockCard;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class StockCardExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $search;
    protected $filterType;
    protected $groupByProduct;
    protected $headerRows = [];
    protected $tableHeaderRows = [];

    public function __construct($search = '', $filterType = '', $groupByProduct = false)
    {
        $this->search = $search;
        $this->filterType = $filterType;
        $this->groupByProduct = $groupByProduct;
    }

    public function title(): string
    {
        return 'Kartu Stok';
    }

    public function array(): array
    {
        $query = StockCard::query()
            ->with(['product', 'batch'])
            ->latest();

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('nama_produk', 'like', "%{$this->search}%")
                    ->orWhere('kode_produk', 'like', "%{$this->search}%");
            })->orWhere('note', 'like', "%{$this->search}%");
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->groupByProduct) {
            $query->orderBy('product_id')->orderBy('created_at', 'desc');
        }

        $stockCards = $query->get();

        $transactionTypes = [
            'in' => 'Masuk',
            'out' => 'Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Retur',
        ];

        $currentRow = 1;

        // Header info
        $result = [
            ['', '', '', 'LAPORAN KARTU STOK', '', '', '', '', ''],
            [''],
            ['', 'Tanggal Export:', now()->format('d/m/Y H:i:s'), '', 'Filter Tipe:', ($this->filterType ? $transactionTypes[$this->filterType] : 'Semua'), '', '', ''],
            ['', 'Pencarian:', ($this->search ?: '-'), '', '', '', '', '', ''],
            [''],
        ];
        $currentRow = 6;

        if ($this->groupByProduct) {
            // Grouped by product
            $grouped = $stockCards->groupBy('product_id');

            foreach ($grouped as $productId => $cards) {
                $product = $cards->first()->product;
                $totalIn = $cards->where('type', 'in')->sum('qty');
                $totalOut = $cards->where('type', 'out')->sum('qty');
                $totalAdj = $cards->where('type', 'adjustment')->sum('qty');
                $totalReturn = $cards->where('type', 'return')->sum('qty');
                $balance = $totalIn - $totalOut + $totalAdj + $totalReturn;

                // Product header row
                $this->headerRows[] = $currentRow;
                $result[] = [
                    'PRODUK: ' . ($product->nama_produk ?? '-'),
                    '',
                    'Kode: ' . ($product->kode_produk ?? '-'),
                    '',
                    'Masuk: ' . number_format($totalIn, 2, ',', '.'),
                    'Keluar: ' . number_format($totalOut, 2, ',', '.'),
                    'Saldo: ' . number_format($balance, 2, ',', '.'),
                    '',
                    ''
                ];
                $currentRow++;

                // Table header for this product
                $this->tableHeaderRows[] = $currentRow;
                $result[] = ['No', 'Tipe Transaksi', 'Kuantitas', 'Satuan', 'Batch', 'Tanggal', 'Jam', 'Catatan', ''];
                $currentRow++;

                $no = 1;
                foreach ($cards as $card) {
                    $result[] = [
                        $no++,
                        $transactionTypes[$card->type] ?? $card->type,
                        $card->type === 'out' ? -$card->qty : $card->qty,
                        $card->product->satuan ?? 'unit',
                        $card->batch ? ($card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id) : '-',
                        $card->created_at->format('d/m/Y'),
                        $card->created_at->format('H:i'),
                        $card->note ?? '-',
                        ''
                    ];
                    $currentRow++;
                }

                // Empty row between groups
                $result[] = [''];
                $currentRow++;
            }
        } else {
            // Flat list (ungrouped) - Table header
            $this->tableHeaderRows[] = $currentRow;
            $result[] = ['No', 'Kode Produk', 'Nama Produk', 'Tipe Transaksi', 'Kuantitas', 'Satuan', 'Batch', 'Tanggal', 'Catatan'];
            $currentRow++;

            $no = 1;
            foreach ($stockCards as $card) {
                $result[] = [
                    $no++,
                    $card->product->kode_produk ?? '-',
                    $card->product->nama_produk ?? '-',
                    $transactionTypes[$card->type] ?? $card->type,
                    $card->type === 'out' ? -$card->qty : $card->qty,
                    $card->product->satuan ?? 'unit',
                    $card->batch ? ($card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id) : '-',
                    $card->created_at->format('d/m/Y'),
                    $card->note ?? '-',
                ];
                $currentRow++;
            }
        }

        // Summary section
        $totalIn = $stockCards->where('type', 'in')->sum('qty');
        $totalOut = $stockCards->where('type', 'out')->sum('qty');
        $totalAdj = $stockCards->where('type', 'adjustment')->sum('qty');
        $totalReturn = $stockCards->where('type', 'return')->sum('qty');
        $netBalance = $totalIn - $totalOut + $totalAdj + $totalReturn;

        $result[] = [''];
        $currentRow++;

        $this->headerRows[] = $currentRow;
        $result[] = ['RINGKASAN', '', '', '', '', '', '', '', ''];
        $currentRow++;

        $result[] = ['', 'Total Transaksi', $stockCards->count(), 'transaksi', '', '', '', '', ''];
        $result[] = ['', 'Total Stok Masuk', $totalIn, '', '', '', '', '', ''];
        $result[] = ['', 'Total Stok Keluar', $totalOut, '', '', '', '', '', ''];
        $result[] = ['', 'Total Penyesuaian', $totalAdj, '', '', '', '', '', ''];
        $result[] = ['', 'Total Retur', $totalReturn, '', '', '', '', '', ''];
        $result[] = ['', 'Saldo Bersih', $netBalance, '', '', '', '', '', ''];

        return $result;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 20,
            'C' => 32,
            'D' => 16,
            'E' => 14,
            'F' => 14,
            'G' => 18,
            'H' => 12,
            'I' => 30,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'I';

                // ===== TITLE STYLING =====
                $sheet->mergeCells('A1:I1');
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 18,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '007BFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(35);

                // ===== HEADER INFO STYLING =====
                $sheet->getStyle('B3:B4')->applyFromArray([
                    'font' => ['bold' => true],
                ]);
                $sheet->getStyle('E3')->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // ===== PRODUCT HEADER ROWS (for grouped view) =====
                foreach ($this->headerRows as $row) {
                    $cellValue = $sheet->getCell("A{$row}")->getValue();

                    if (strpos($cellValue, 'PRODUK:') === 0) {
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 11,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '28A745'],
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);
                    } elseif ($cellValue === 'RINGKASAN') {
                        $sheet->mergeCells("A{$row}:I{$row}");
                        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => 'FFFFFF'],
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '17A2B8'],
                            ],
                        ]);
                        $sheet->getRowDimension($row)->setRowHeight(22);

                        // Style summary data rows
                        for ($i = 1; $i <= 6; $i++) {
                            $summaryRow = $row + $i;
                            if ($summaryRow <= $lastRow) {
                                $sheet->getStyle("B{$summaryRow}")->applyFromArray([
                                    'font' => ['bold' => true],
                                ]);
                                $sheet->getStyle("C{$summaryRow}")->applyFromArray([
                                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                                    'numberFormat' => ['formatCode' => '#,##0.00'],
                                ]);

                                // Saldo Bersih row
                                if ($i == 6) {
                                    $sheet->getStyle("A{$summaryRow}:I{$summaryRow}")->applyFromArray([
                                        'font' => ['bold' => true, 'size' => 11],
                                        'fill' => [
                                            'fillType' => Fill::FILL_SOLID,
                                            'startColor' => ['rgb' => 'D4EDDA'],
                                        ],
                                        'borders' => [
                                            'top' => ['borderStyle' => Border::BORDER_THIN],
                                            'bottom' => ['borderStyle' => Border::BORDER_DOUBLE],
                                        ],
                                    ]);
                                }
                            }
                        }
                    }
                }

                // ===== TABLE HEADER ROWS =====
                foreach ($this->tableHeaderRows as $row) {
                    $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 10,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '6C757D'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000'],
                            ],
                        ],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(20);

                    // Style data rows below this header
                    $dataRow = $row + 1;
                    while ($dataRow <= $lastRow) {
                        $cellValue = $sheet->getCell("A{$dataRow}")->getValue();
                        if (empty($cellValue) || !is_numeric($cellValue)) {
                            break;
                        }

                        // Apply border and alignment to data rows
                        $sheet->getStyle("A{$dataRow}:I{$dataRow}")->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'DDDDDD'],
                                ],
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        // Alternate row colors
                        if (($dataRow - $row) % 2 == 0) {
                            $sheet->getStyle("A{$dataRow}:I{$dataRow}")->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'F8F9FA'],
                                ],
                            ]);
                        }

                        // Center align No column
                        $sheet->getStyle("A{$dataRow}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);

                        // Right align and format quantity column
                        $qtyCol = $this->groupByProduct ? 'C' : 'E';
                        $sheet->getStyle("{$qtyCol}{$dataRow}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                            'numberFormat' => ['formatCode' => '#,##0.00'],
                        ]);

                        // Color quantity based on value
                        $qtyValue = $sheet->getCell("{$qtyCol}{$dataRow}")->getValue();
                        if ($qtyValue < 0) {
                            $sheet->getStyle("{$qtyCol}{$dataRow}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'DC3545'], 'bold' => true],
                            ]);
                        } else {
                            $sheet->getStyle("{$qtyCol}{$dataRow}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '28A745'], 'bold' => true],
                            ]);
                        }

                        // Center align date and time columns
                        $dateCol = $this->groupByProduct ? 'F' : 'H';
                        $timeCol = $this->groupByProduct ? 'G' : '';
                        $sheet->getStyle("{$dateCol}{$dataRow}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                        if ($timeCol) {
                            $sheet->getStyle("{$timeCol}{$dataRow}")->applyFromArray([
                                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                            ]);
                        }

                        $dataRow++;
                    }
                }

                // ===== GENERAL STYLING =====
                $sheet->getParent()->getDefaultStyle()->applyFromArray([
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 10,
                    ],
                ]);

                // Freeze panes
                $sheet->freezePane('A2');

                // Print settings
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class StockReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
   protected $data;
   protected $title;
   protected $reportType;
   protected $dateRange;

   public function __construct($data, $reportType = 'store', $title = 'Laporan Stok', $dateRange = null)
   {
      $this->data = $data;
      $this->title = $title;
      $this->reportType = $reportType;
      $this->dateRange = $dateRange;
   }

   public function array(): array
   {
      // Tambah baris kosong di awal untuk header info
      $result = [
         [''],
         ['LAPORAN STOK ' . ($this->reportType === 'store' ? 'TOKO' : 'GUDANG')],
         ['PT. Your Company'],
         ['Tanggal Report: ' . now()->format('d/m/Y H:i:s') . ($this->dateRange ? ' (Filter: ' . $this->dateRange . ')' : '')],
         [''],
         // Row 6 = header kolom (added Tanggal, Batch, Lokasi, Keterangan)
         ['No', 'Kode Produk', 'Nama Produk', 'Kategori', 'Sub Kategori', 'Unit', 'Tanggal Transaksi', 'Batch', 'Lokasi', 'Keterangan', 'Stok Masuk', 'Stok Keluar', 'Stok Akhir'],
      ];

      // Tambah data (mulai dari row 7)
      $result = array_merge($result, $this->data);

      // Tambah summary di akhir
      if (!empty($this->data)) {
         $result[] = [''];
         $result[] = ['RINGKASAN:'];
         $result[] = ['Total Produk', count($this->data)];
         // Provide summary totals for Masuk, Keluar, Akhir (columns indices: 10=Masuk, 11=Keluar, 12=Akhir)
         $result[] = ['Total Stok Masuk', $this->sumColumn(10)];
         $result[] = ['Total Stok Keluar', $this->sumColumn(11)];
         $result[] = ['Total Stok Akhir', $this->sumColumn(12)];
      }

      return $result;
   }
   protected function sumColumn($columnIndex)
   {
      return array_sum(array_column($this->data, $columnIndex));
   }

   public function headings(): array
   {
      // Headings dimulai di row 6 (setelah header info)
      return [];
   }

   public function columnWidths(): array
   {
      return [
         'A' => 5,    // No
         'B' => 15,   // Kode Produk
         'C' => 30,   // Nama Produk
         'D' => 15,   // Kategori
         'E' => 15,   // Sub Kategori
         'F' => 10,   // Unit
         'G' => 18,   // Tanggal Transaksi
         'H' => 30,   // Batch
         'I' => 18,   // Lokasi
         'J' => 20,   // Keterangan
         'K' => 12,   // Stok Masuk
         'L' => 12,   // Stok Keluar
         'M' => 12,   // Stok Akhir
      ];
   }

   public function styles(Worksheet $sheet)
   {
      // Style header info (rows 2-4)
      $sheet->mergeCells('A2:M2');
      $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF366092'));
      $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      $sheet->mergeCells('A3:M3');
      $sheet->getStyle('A3')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $sheet->mergeCells('A4:M4');
      $sheet->getStyle('A4')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      // Style kolom header (row 6)
      $headerStyle = [
         'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
         ],
         'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '366092'],
         ],
         'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
         ],
         'borders' => [
            'allBorders' => [
               'borderStyle' => Border::BORDER_THIN,
               'color' => ['rgb' => '000000'],
            ],
         ],
      ];

      // Apply header style
      for ($col = 'A'; $col <= 'M'; $col++) {
         $sheet->getStyle($col . '6')->applyFromArray($headerStyle);
      }
      // Ensure header texts wrap
      $sheet->getStyle('A6:M6')->getAlignment()->setWrapText(true);

      // Style data rows
      $dataStyle = [
         'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER,
         ],
         'borders' => [
            'allBorders' => [
               'borderStyle' => Border::BORDER_THIN,
               'color' => ['rgb' => 'CCCCCC'],
            ],
         ],
      ];

      // Apply striped rows (alternating colors)
      $dataRowStart = 7;
      $dataRowEnd = $dataRowStart + count($this->data) - 1;

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         if (($row - $dataRowStart) % 2 === 0) {
            $fill = [
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => 'F0F0F0'],
               ],
            ];
            for ($col = 'A'; $col <= 'M'; $col++) {
               $sheet->getStyle($col . $row)->applyFromArray($fill);
            }
         }
      }

      // Style kolom angka (center dan format)
      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         for ($col = 'A'; $col <= 'M'; $col++) {
            $sheet->getStyle($col . $row)->applyFromArray($dataStyle);

            // Format angka for numeric columns (K: Stok Masuk, L: Stok Keluar, M: Stok Akhir)
            if (in_array($col, ['K', 'L', 'M'])) {
               $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Center align No column (A)
            if ($col === 'A') {
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
         }
      }

      // Style summary section
      if (!empty($this->data)) {
         $summaryStartRow = $dataRowEnd + 2; // Row after data + blank row

         // RINGKASAN header row
         $summaryStartRow++; // Row with "RINGKASAN:"
         $sheet->mergeCells('A' . $summaryStartRow . ':B' . $summaryStartRow);
         $sheet->getStyle('A' . $summaryStartRow)
            ->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF366092'));
         $sheet->getStyle('A' . $summaryStartRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFE8F0F8'));
         $sheet->getRowDimension($summaryStartRow)->setRowHeight(18);

         // Summary detail rows
         $summaryRows = [
            ['label' => 'Total Produk', 'column' => 'B'],
            ['label' => 'Total Stok Awal', 'column' => 'B'],
            ['label' => 'Total Stok Masuk', 'column' => 'B'],
            ['label' => 'Total Stok Keluar', 'column' => 'B'],
            ['label' => 'Total Stok Akhir', 'column' => 'B'],
         ];

         for ($i = 0; $i < count($summaryRows); $i++) {
            $row = $summaryStartRow + 1 + $i;

            // Label styling
            $sheet->getStyle('A' . $row)
               ->getFont()->setBold(true)->setSize(10)->setColor(new Color('FF333333'));
            $sheet->getStyle('A' . $row)
               ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFEFF3F8'));
            $sheet->getStyle('A' . $row)
               ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $row)
               ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
               ->setColor(new Color('FFD3D3D3'));

            // Value styling
            $sheet->getStyle('B' . $row)
               ->getFont()->setBold(true)->setSize(10)->setColor(new Color('FF366092'));
            $sheet->getStyle('B' . $row)
               ->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle('B' . $row)
               ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('B' . $row)
               ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFEFF3F8'));
            $sheet->getStyle('B' . $row)
               ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
               ->setColor(new Color('FFD3D3D3'));

            $sheet->getRowDimension($row)->setRowHeight(16);
         }
      }

      // Set row height untuk header
      $sheet->getRowDimension(2)->setRowHeight(20);
      $sheet->getRowDimension(6)->setRowHeight(25);

      return [];
   }
}

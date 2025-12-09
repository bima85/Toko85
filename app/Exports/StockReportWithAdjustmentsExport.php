<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
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

class StockReportWithAdjustmentsExport implements WithMultipleSheets
{
   protected $stokTokoData;
   protected $stokGudangData;
   protected $adjustmentData;

   public function __construct($stokTokoData, $stokGudangData, $adjustmentData)
   {
      $this->stokTokoData = $stokTokoData;
      $this->stokGudangData = $stokGudangData;
      $this->adjustmentData = $adjustmentData;
   }

   public function sheets(): array
   {
      return [
         'Stok Toko' => new StockSheet($this->stokTokoData, 'Toko'),
         'Stok Gudang' => new StockSheet($this->stokGudangData, 'Gudang'),
         'Total Stok' => new TotalStockSheet($this->stokTokoData, $this->stokGudangData),
         'Riwayat Penyesuaian' => new AdjustmentSheet($this->adjustmentData),
      ];
   }
}

class StockSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
   protected $data;
   protected $lokasi;

   public function __construct($data, $lokasi = 'Toko')
   {
      $this->data = $data;
      $this->lokasi = $lokasi;
   }

   public function array(): array
   {
      $result = [
         [''],
         ['LAPORAN STOK ' . strtoupper($this->lokasi)],
         ['PT. Your Company'],
         ['Tanggal Report: ' . now()->format('d/m/Y H:i:s')],
         [''],
         ['No', 'Kode Produk', 'Nama Produk', 'Kategori', 'Sub Kategori', 'Unit', 'Stok Awal', 'Stok Masuk', 'Stok Keluar', 'Stok Akhir'],
      ];

      $result = array_merge($result, $this->data);

      if (!empty($this->data)) {
         $result[] = [''];
         $result[] = ['RINGKASAN:'];
         $result[] = ['Total Produk', count($this->data)];
         $result[] = ['Total Stok Awal', $this->sumColumn(6)];
         $result[] = ['Total Stok Masuk', $this->sumColumn(7)];
         $result[] = ['Total Stok Keluar', $this->sumColumn(8)];
         $result[] = ['Total Stok Akhir', $this->sumColumn(9)];
      }

      return $result;
   }

   protected function sumColumn($columnIndex)
   {
      return array_sum(array_column($this->data, $columnIndex));
   }

   public function headings(): array
   {
      return [];
   }

   public function columnWidths(): array
   {
      return [
         'A' => 5,
         'B' => 15,
         'C' => 25,
         'D' => 15,
         'E' => 15,
         'F' => 10,
         'G' => 12,
         'H' => 12,
         'I' => 12,
         'J' => 12,
      ];
   }

   public function styles(Worksheet $sheet)
   {
      $sheet->mergeCells('A2:J2');
      $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF366092'));
      $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      $sheet->mergeCells('A3:J3');
      $sheet->getStyle('A3')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $sheet->mergeCells('A4:J4');
      $sheet->getStyle('A4')->getFont()->setSize(10)->setColor(new Color('FF666666'));

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

      for ($col = 'A'; $col <= 'J'; $col++) {
         $sheet->getStyle($col . '6')->applyFromArray($headerStyle);
      }

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
            for ($col = 'A'; $col <= 'J'; $col++) {
               $sheet->getStyle($col . $row)->applyFromArray($fill);
            }
         }
      }

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         for ($col = 'A'; $col <= 'J'; $col++) {
            $sheet->getStyle($col . $row)->applyFromArray($dataStyle);

            if (in_array($col, ['G', 'H', 'I', 'J'])) {
               $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            if ($col === 'A') {
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
         }
      }

      if (!empty($this->data)) {
         $summaryStartRow = $dataRowEnd + 2;
         $summaryStartRow++;

         $sheet->mergeCells('A' . $summaryStartRow . ':B' . $summaryStartRow);
         $sheet->getStyle('A' . $summaryStartRow)
            ->getFont()->setBold(true)->setSize(11)->setColor(new Color('FF366092'));
         $sheet->getStyle('A' . $summaryStartRow)
            ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFE8F0F8'));
         $sheet->getRowDimension($summaryStartRow)->setRowHeight(18);

         $summaryRows = [
            ['label' => 'Total Produk', 'column' => 'B'],
            ['label' => 'Total Stok Awal', 'column' => 'B'],
            ['label' => 'Total Stok Masuk', 'column' => 'B'],
            ['label' => 'Total Stok Keluar', 'column' => 'B'],
            ['label' => 'Total Stok Akhir', 'column' => 'B'],
         ];

         for ($i = 0; $i < count($summaryRows); $i++) {
            $row = $summaryStartRow + 1 + $i;

            $sheet->getStyle('A' . $row)
               ->getFont()->setBold(true)->setSize(10)->setColor(new Color('FF333333'));
            $sheet->getStyle('A' . $row)
               ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('FFEFF3F8'));
            $sheet->getStyle('A' . $row)
               ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('A' . $row)
               ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)
               ->setColor(new Color('FFD3D3D3'));

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

      $sheet->getRowDimension(2)->setRowHeight(20);
      $sheet->getRowDimension(6)->setRowHeight(25);

      return [];
   }
}

class TotalStockSheet implements FromArray, WithStyles, WithColumnWidths
{
   protected $stokTokoData;
   protected $stokGudangData;

   public function __construct($stokTokoData, $stokGudangData)
   {
      $this->stokTokoData = $stokTokoData;
      $this->stokGudangData = $stokGudangData;
   }

   public function array(): array
   {
      $result = [
         [''],
         ['TOTAL STOK KESELURUHAN'],
         ['PT. Your Company'],
         ['Tanggal Report: ' . now()->format('d/m/Y H:i:s')],
         [''],
         ['No', 'Kode Produk', 'Nama Produk', 'Kategori', 'Sub Kategori', 'Unit', 'Stok Awal', 'Stok Masuk', 'Stok Keluar', 'Stok Akhir'],
      ];

      // Gabung data toko dan gudang, kemudian agregasi per produk
      $allData = array_merge($this->stokTokoData, $this->stokGudangData);
      $grouped = [];

      foreach ($allData as $row) {
         $key = $row[1]; // Kode produk
         if (!isset($grouped[$key])) {
            $grouped[$key] = [
               'kode' => $row[1],
               'nama' => $row[2],
               'kategori' => $row[3],
               'subkategori' => $row[4],
               'unit' => $row[5],
               'stok_awal' => 0,
               'stok_masuk' => 0,
               'stok_keluar' => 0,
               'stok_akhir' => 0,
            ];
         }
         $grouped[$key]['stok_awal'] += $row[6];
         $grouped[$key]['stok_masuk'] += $row[7];
         $grouped[$key]['stok_keluar'] += $row[8];
         $grouped[$key]['stok_akhir'] += $row[9];
      }

      $no = 1;
      foreach ($grouped as $item) {
         $result[] = [
            $no++,
            $item['kode'],
            $item['nama'],
            $item['kategori'],
            $item['subkategori'],
            $item['unit'],
            $item['stok_awal'],
            $item['stok_masuk'],
            $item['stok_keluar'],
            $item['stok_akhir'],
         ];
      }

      if (!empty($grouped)) {
         // Hitung total untuk Toko
         $tokoAwal = array_sum(array_column($this->stokTokoData, 6));
         $tokoMasuk = array_sum(array_column($this->stokTokoData, 7));
         $tokoKeluar = array_sum(array_column($this->stokTokoData, 8));
         $tokoAkhir = array_sum(array_column($this->stokTokoData, 9));

         // Hitung total untuk Gudang
         $gudangAwal = array_sum(array_column($this->stokGudangData, 6));
         $gudangMasuk = array_sum(array_column($this->stokGudangData, 7));
         $gudangKeluar = array_sum(array_column($this->stokGudangData, 8));
         $gudangAkhir = array_sum(array_column($this->stokGudangData, 9));

         $result[] = [''];
         $result[] = ['TOTAL STOK TOKO:'];
         $result[] = ['Total Produk Toko', count($this->stokTokoData)];
         $result[] = ['Total Stok Awal Toko', $tokoAwal];
         $result[] = ['Total Stok Masuk Toko', $tokoMasuk];
         $result[] = ['Total Stok Keluar Toko', $tokoKeluar];
         $result[] = ['Total Stok Akhir Toko', $tokoAkhir];

         $result[] = [''];
         $result[] = ['TOTAL STOK GUDANG:'];
         $result[] = ['Total Produk Gudang', count($this->stokGudangData)];
         $result[] = ['Total Stok Awal Gudang', $gudangAwal];
         $result[] = ['Total Stok Masuk Gudang', $gudangMasuk];
         $result[] = ['Total Stok Keluar Gudang', $gudangKeluar];
         $result[] = ['Total Stok Akhir Gudang', $gudangAkhir];

         $result[] = [''];
         $result[] = ['RINGKASAN TOTAL KESELURUHAN:'];
         $result[] = ['Total Produk (Unik)', count($grouped)];
         $result[] = ['Total Stok Awal', array_sum(array_column($grouped, 'stok_awal'))];
         $result[] = ['Total Stok Masuk', array_sum(array_column($grouped, 'stok_masuk'))];
         $result[] = ['Total Stok Keluar', array_sum(array_column($grouped, 'stok_keluar'))];
         $result[] = ['Total Stok Akhir', array_sum(array_column($grouped, 'stok_akhir'))];
      }

      return $result;
   }

   public function columnWidths(): array
   {
      return [
         'A' => 5,
         'B' => 15,
         'C' => 25,
         'D' => 15,
         'E' => 15,
         'F' => 10,
         'G' => 12,
         'H' => 12,
         'I' => 12,
         'J' => 12,
      ];
   }

   public function styles(Worksheet $sheet)
   {
      $sheet->mergeCells('A2:J2');
      $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF2E7D32'));
      $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      $sheet->mergeCells('A3:J3');
      $sheet->getStyle('A3')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $sheet->mergeCells('A4:J4');
      $sheet->getStyle('A4')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $headerStyle = [
         'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
         ],
         'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '2E7D32'],
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

      for ($col = 'A'; $col <= 'J'; $col++) {
         $sheet->getStyle($col . '6')->applyFromArray($headerStyle);
      }

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

      $dataRowStart = 7;
      $dataRowEnd = $dataRowStart + count($this->stokTokoData) + count($this->stokGudangData) - 1;

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         if (($row - $dataRowStart) % 2 === 0) {
            $fill = [
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => 'F1F8F6'],
               ],
            ];
            for ($col = 'A'; $col <= 'J'; $col++) {
               $sheet->getStyle($col . $row)->applyFromArray($fill);
            }
         }
      }

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         for ($col = 'A'; $col <= 'J'; $col++) {
            $sheet->getStyle($col . $row)->applyFromArray($dataStyle);

            if (in_array($col, ['G', 'H', 'I', 'J'])) {
               $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            if ($col === 'A') {
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
         }
      }

      $sheet->getRowDimension(2)->setRowHeight(20);
      $sheet->getRowDimension(6)->setRowHeight(25);

      return [];
   }
}

class AdjustmentSheet implements FromArray, WithStyles, WithColumnWidths
{
   protected $data;

   public function __construct($data)
   {
      $this->data = $data;
   }

   public function array(): array
   {
      $result = [
         [''],
         ['RIWAYAT PENYESUAIAN STOK'],
         ['PT. Your Company'],
         ['Tanggal Report: ' . now()->format('d/m/Y H:i:s')],
         [''],
         ['No', 'Kode Produk', 'Nama Produk', 'Lokasi', 'Tipe', 'Qty', 'Alasan', 'Tanggal', 'Jam', 'User'],
      ];

      $result = array_merge($result, $this->data);

      return $result;
   }

   public function columnWidths(): array
   {
      return [
         'A' => 5,
         'B' => 15,
         'C' => 25,
         'D' => 15,
         'E' => 12,
         'F' => 12,
         'G' => 30,
         'H' => 12,
         'I' => 10,
         'J' => 15,
      ];
   }

   public function styles(Worksheet $sheet)
   {
      $sheet->mergeCells('A2:J2');
      $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(14)->setColor(new Color('FF366092'));
      $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

      $sheet->mergeCells('A3:J3');
      $sheet->getStyle('A3')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $sheet->mergeCells('A4:J4');
      $sheet->getStyle('A4')->getFont()->setSize(10)->setColor(new Color('FF666666'));

      $headerStyle = [
         'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
         ],
         'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => 'F4A545'],
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

      for ($col = 'A'; $col <= 'J'; $col++) {
         $sheet->getStyle($col . '6')->applyFromArray($headerStyle);
      }

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

      $dataRowStart = 7;
      $dataRowEnd = $dataRowStart + count($this->data) - 1;

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         if (($row - $dataRowStart) % 2 === 0) {
            $fill = [
               'fill' => [
                  'fillType' => Fill::FILL_SOLID,
                  'startColor' => ['rgb' => 'FFF8F0'],
               ],
            ];
            for ($col = 'A'; $col <= 'J'; $col++) {
               $sheet->getStyle($col . $row)->applyFromArray($fill);
            }
         }
      }

      for ($row = $dataRowStart; $row <= $dataRowEnd; $row++) {
         for ($col = 'A'; $col <= 'J'; $col++) {
            $sheet->getStyle($col . $row)->applyFromArray($dataStyle);

            if ($col === 'F') {
               $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            if ($col === 'A') {
               $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
         }
      }

      $sheet->getRowDimension(2)->setRowHeight(20);
      $sheet->getRowDimension(6)->setRowHeight(25);

      return [];
   }
}

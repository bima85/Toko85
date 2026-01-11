<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambah kategori baru jika belum ada
        $categories = [
            ['kode_kategori' => 'LAIN', 'nama_kategori' => 'Lain-lain', 'description' => 'Produk non pangan'],
            ['kode_kategori' => 'MINUMAN', 'nama_kategori' => 'Minuman', 'description' => 'Berbagai jenis minuman'],
            ['kode_kategori' => 'SNACK', 'nama_kategori' => 'Snack', 'description' => 'Makanan ringan dan snack'],
            ['kode_kategori' => 'BAHAN', 'nama_kategori' => 'Bahan Pokok', 'description' => 'Bahan-bahan pokok rumah tangga'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['kode_kategori' => $category['kode_kategori']],
                $category
            );
        }

        // Ambil ID kategori
        $categoryIds = DB::table('categories')->pluck('id', 'nama_kategori')->toArray();
        $berasId = $categoryIds['BERAS'];
        $ketanId = $categoryIds['Ketan'];
        $lainId = $categoryIds['Lain-lain'] ?? DB::table('categories')->where('kode_kategori', 'LAIN')->value('id');
        $minumanId = $categoryIds['Minuman'] ?? DB::table('categories')->where('kode_kategori', 'MINUMAN')->value('id');
        $snackId = $categoryIds['Snack'] ?? DB::table('categories')->where('kode_kategori', 'SNACK')->value('id');
        $bahanId = $categoryIds['Bahan Pokok'] ?? DB::table('categories')->where('kode_kategori', 'BAHAN')->value('id');

        // Tambah subcategories baru
        $subcategories = [
            ['kode_subkategori' => 'NPG', 'nama_subkategori' => 'Non Pangan', 'category_id' => $lainId, 'description' => 'Produk non pangan'],
            ['kode_subkategori' => 'TEH', 'nama_subkategori' => 'Teh & Kopi', 'category_id' => $minumanId, 'description' => 'Minuman teh dan kopi'],
            ['kode_subkategori' => 'SODA', 'nama_subkategori' => 'Soda & Jus', 'category_id' => $minumanId, 'description' => 'Minuman soda dan jus'],
            ['kode_subkategori' => 'KERIPIK', 'nama_subkategori' => 'Keripik', 'category_id' => $snackId, 'description' => 'Berbagai jenis keripik'],
            ['kode_subkategori' => 'PERMEN', 'nama_subkategori' => 'Permen & Coklat', 'category_id' => $snackId, 'description' => 'Permen dan coklat'],
            ['kode_subkategori' => 'MINYAK', 'nama_subkategori' => 'Minyak & Bumbu', 'category_id' => $bahanId, 'description' => 'Minyak dan bumbu dapur'],
            ['kode_subkategori' => 'SAUS', 'nama_subkategori' => 'Pasta & Saus', 'category_id' => $bahanId, 'description' => 'Pasta dan saus'],
        ];

        foreach ($subcategories as $subcategory) {
            DB::table('subcategories')->updateOrInsert(
                ['kode_subkategori' => $subcategory['kode_subkategori']],
                $subcategory
            );
        }

        // Ambil ID subcategories
        $subcategoryIds = [];
        $allSubs = DB::table('subcategories')->get();
        foreach ($allSubs as $sub) {
            $subcategoryIds[$sub->nama_subkategori] = $sub->id;
        }

        // Ambil unit IDs
        $unitKg = DB::table('units')->where('nama_unit', 'kg')->value('id') ?? 5;
        $unitPcs = DB::table('units')->where('nama_unit', 'Piece/Buah')->value('id') ?? 9;
        $unitPack = DB::table('units')->where('nama_unit', 'Paket')->value('id') ?? 12;
        $unitBotol = DB::table('units')->where('nama_unit', 'Botol')->value('id') ?? $unitPcs;

        // Produk tambahan beras
        $additionalRiceProducts = [
            ['kode' => 'BRS_092_IR64', 'nama' => 'IR64', 'sub' => 'MENTIK'],
            ['kode' => 'BRS_093_CIREBON', 'nama' => 'CIREBON', 'sub' => 'MENTIK'],
            ['kode' => 'BRS_094_PANDAWA', 'nama' => 'PANDAWA', 'sub' => 'C4'],
            ['kode' => 'BRS_095_INPARI', 'nama' => 'INPARI', 'sub' => 'C4'],
            ['kode' => 'BRS_096_CIKUNIR', 'nama' => 'CIKUNIR', 'sub' => 'C4'],
            ['kode' => 'BRS_097_LOGOTIPO', 'nama' => 'LOGOTIPO', 'sub' => 'C4'],
            ['kode' => 'BRS_098_SANGKURIANG', 'nama' => 'SANGKURIANG', 'sub' => 'C4'],
            ['kode' => 'BRS_099_TLOGO', 'nama' => 'TLOGO', 'sub' => 'C4'],
            ['kode' => 'BRS_100_SITU', 'nama' => 'SITU', 'sub' => 'C4'],
            ['kode' => 'BRS_101_BENGKULU', 'nama' => 'BENGKULU', 'sub' => 'C4'],
            ['kode' => 'BRS_102_LAMPUNG', 'nama' => 'LAMPUNG', 'sub' => 'C4'],
            ['kode' => 'BRS_103_JAMBI', 'nama' => 'JAMBI', 'sub' => 'C4'],
            ['kode' => 'BRS_104_RIAU', 'nama' => 'RIAU', 'sub' => 'C4'],
            ['kode' => 'BRS_105_SUMSEL', 'nama' => 'SUMATERA SELATAN', 'sub' => 'C4'],
            ['kode' => 'BRS_106_SUMUT', 'nama' => 'SUMATERA UTARA', 'sub' => 'C4'],
        ];

        // Produk ketan tambahan
        $additionalKetanProducts = [
            ['kode' => 'KTN_013_HITAM', 'nama' => 'KETAN HITAM', 'sub' => 'Ketan'],
            ['kode' => 'KTN_014_PUTIH', 'nama' => 'KETAN PUTIH', 'sub' => 'Ketan'],
            ['kode' => 'KTN_015_MERAH', 'nama' => 'KETAN MERAH', 'sub' => 'Ketan'],
            ['kode' => 'KTN_016_UNGU', 'nama' => 'KETAN UNGU', 'sub' => 'Ketan'],
            ['kode' => 'KTN_017_KUNING', 'nama' => 'KETAN KUNING', 'sub' => 'Ketan'],
        ];

        // Produk minuman
        $drinkProducts = [
            ['kode' => 'MIN_001_TEHDINGIN', 'nama' => 'Teh Dingin Botol', 'sub' => 'Teh & Kopi', 'unit' => $unitBotol],
            ['kode' => 'MIN_002_KOPISUSU', 'nama' => 'Kopi Susu Sachet', 'sub' => 'Teh & Kopi', 'unit' => $unitPack],
            ['kode' => 'MIN_003_SODA500ML', 'nama' => 'Soda 500ml', 'sub' => 'Soda & Jus', 'unit' => $unitBotol],
            ['kode' => 'MIN_004_JUSJERUK', 'nama' => 'Jus Jeruk 250ml', 'sub' => 'Soda & Jus', 'unit' => $unitPack],
            ['kode' => 'MIN_005_AIRMINERAL', 'nama' => 'Air Mineral Galon', 'sub' => 'Soda & Jus', 'unit' => $unitPcs],
            ['kode' => 'MIN_006_ESBUAH', 'nama' => 'Es Buah Campur', 'sub' => 'Soda & Jus', 'unit' => $unitPack],
        ];

        // Produk snack
        $snackProducts = [
            ['kode' => 'SNK_001_KERIPIKSG', 'nama' => 'Keripik Singkong', 'sub' => 'Keripik', 'unit' => $unitPack],
            ['kode' => 'SNK_002_KERIPIKPIS', 'nama' => 'Keripik Pisang', 'sub' => 'Keripik', 'unit' => $unitPack],
            ['kode' => 'SNK_003_KERIPIKKEL', 'nama' => 'Keripik Kelapa', 'sub' => 'Keripik', 'unit' => $unitPack],
            ['kode' => 'SNK_004_PERMENJELLY', 'nama' => 'Permen Jelly', 'sub' => 'Permen & Coklat', 'unit' => $unitPack],
            ['kode' => 'SNK_005_COKLATBAT', 'nama' => 'Coklat Batang', 'sub' => 'Permen & Coklat', 'unit' => $unitPcs],
            ['kode' => 'SNK_006_BISKIT', 'nama' => 'Biskuit Kelapa', 'sub' => 'Permen & Coklat', 'unit' => $unitPack],
        ];

        // Produk bahan pokok
        $bahanProducts = [
            ['kode' => 'BHN_001_MINYAKGOR', 'nama' => 'Minyak Goreng 1L', 'sub' => 'Minyak & Bumbu', 'unit' => $unitBotol],
            ['kode' => 'BHN_002_GARAM', 'nama' => 'Garam Dapur 500g', 'sub' => 'Minyak & Bumbu', 'unit' => $unitPack],
            ['kode' => 'BHN_003_GULA', 'nama' => 'Gula Pasir 1kg', 'sub' => 'Minyak & Bumbu', 'unit' => $unitPack],
            ['kode' => 'BHN_004_TERASI', 'nama' => 'Terasi Udang', 'sub' => 'Minyak & Bumbu', 'unit' => $unitPack],
            ['kode' => 'BHN_005_SAMBAL', 'nama' => 'Sambal Terasi', 'sub' => 'Pasta & Saus', 'unit' => $unitPack],
            ['kode' => 'BHN_006_KEPITING', 'nama' => 'Saus Kepiting', 'sub' => 'Pasta & Saus', 'unit' => $unitBotol],
            ['kode' => 'BHN_007_TOMAT', 'nama' => 'Saus Tomat', 'sub' => 'Pasta & Saus', 'unit' => $unitBotol],
        ];

        // Produk non pangan
        $nonPanganProducts = [
            ['kode' => 'NPG_001_SABUN', 'nama' => 'Sabun Mandi', 'sub' => 'Non Pangan', 'unit' => $unitPcs],
            ['kode' => 'NPG_002_SHAMPO', 'nama' => 'Shampoo 100ml', 'sub' => 'Non Pangan', 'unit' => $unitBotol],
            ['kode' => 'NPG_003_PASTELEDAK', 'nama' => 'Pasta Gigi', 'sub' => 'Non Pangan', 'unit' => $unitPcs],
            ['kode' => 'NPG_004_DETERGEN', 'nama' => 'Deterjen Bubuk', 'sub' => 'Non Pangan', 'unit' => $unitPack],
            ['kode' => 'NPG_005_TISU', 'nama' => 'Tisu Basah', 'sub' => 'Non Pangan', 'unit' => $unitPack],
        ];

        // Insert semua produk tambahan
        $allProducts = array_merge(
            $this->formatProducts($additionalRiceProducts, $berasId, $subcategoryIds, $unitKg),
            $this->formatProducts($additionalKetanProducts, $ketanId, $subcategoryIds, $unitKg),
            $this->formatProducts($drinkProducts, $minumanId, $subcategoryIds, $unitBotol),
            $this->formatProducts($snackProducts, $snackId, $subcategoryIds, $unitPack),
            $this->formatProducts($bahanProducts, $bahanId, $subcategoryIds, $unitPack),
            $this->formatProducts($nonPanganProducts, $lainId, $subcategoryIds, $unitPcs)
        );

        foreach ($allProducts as $product) {
            DB::table('products')->updateOrInsert(
                ['kode_produk' => $product['kode_produk']],
                $product
            );
        }

        $this->command->info('✅ Berhasil menambahkan ' . count($allProducts) . ' produk tambahan!');
        $this->command->info('📊 Total produk sekarang: ' . DB::table('products')->count());
    }

    private function formatProducts($products, $categoryId, $subcategoryIds, $defaultUnitId)
    {
        $formatted = [];
        foreach ($products as $product) {
            $subcategoryId = $subcategoryIds[$product['sub']] ?? null;
            $unitId = $product['unit'] ?? $defaultUnitId;

            $formatted[] = [
                'kode_produk' => $product['kode'],
                'nama_produk' => $product['nama'],
                'description' => 'Produk ' . $product['nama'],
                'satuan' => 'pcs',
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'supplier_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        return $formatted;
    }
}

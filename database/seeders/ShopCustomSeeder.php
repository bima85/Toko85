<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Supplier;
use App\Models\Product;

class ShopCustomSeeder extends Seeder
{
    public function run()
    {
        // Ensure categories
        $catBeras = Category::firstOrCreate(
            ['nama_kategori' => 'Beras'],
            ['kode_kategori' => 'BR-' . Str::upper(Str::random(6)), 'description' => 'Kategori Beras']
        );

        $catKetan = Category::firstOrCreate(
            ['nama_kategori' => 'Ketan'],
            ['kode_kategori' => 'KT-' . Str::upper(Str::random(6)), 'description' => 'Kategori Ketan']
        );

        // Helper to ensure subcategory under a category
        $ensureSub = function ($category, $name) {
            return Subcategory::firstOrCreate(
                ['nama_subkategori' => $name, 'category_id' => $category->id],
                ['kode_subkategori' => Str::upper(Str::substr(Str::slug($name), 0, 6)) . '-' . Str::upper(Str::random(4)), 'description' => $name]
            );
        };

        // Subcategories used in data
        $subC4 = $ensureSub($catBeras, 'C4');
        $subRojolele = $ensureSub($catBeras, 'Rojolele');
        $subWangi = $ensureSub($catBeras, 'Wangi');
        $subKetan = $ensureSub($catKetan, 'Ketan');

        // Data: suppliers (owners) and their products with subcategory mapping
        $data = [
            [
                'supplier' => ['nama' => 'PB Jaya Abadi - Karang Pandan', 'owner' => 'Mbak Dwi Suyatmi Athaya, Mas Harsono'],
                'products' => [
                    ['nama' => 'Naga Jaya Abadi', 'sub' => $subC4],
                    ['nama' => 'Bengawan', 'sub' => $subC4],
                    ['nama' => 'Rojolele A', 'sub' => $subRojolele],
                    ['nama' => 'Mentik Wangi Kasturi', 'sub' => $subWangi],
                    ['nama' => 'Patah Dua Doro', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'CV Cakra Adhistara Sejahtera - Tasikmadu', 'owner' => 'Mbak Ayu Sigit Aribowo'],
                'products' => [
                    ['nama' => 'Prima', 'sub' => $subC4],
                    ['nama' => 'Kelinci', 'sub' => $subC4],
                    ['nama' => 'Senyum', 'sub' => $subC4],
                    ['nama' => 'Patah AY', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Sumber Rejeki - Karang Anyar', 'owner' => 'Pak Sapto Giri (Aufa)'],
                'products' => [
                    ['nama' => 'Gajah', 'sub' => $subC4],
                    ['nama' => 'Hijab', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Gatot Gondangmanis - Dompyong Karang Pandan', 'owner' => 'Bu Gatot Sumarni'],
                'products' => [
                    ['nama' => 'Lele Barokah', 'sub' => $subRojolele],
                    ['nama' => 'Mentik Gatot', 'sub' => $subWangi],
                ],
            ],
            [
                'supplier' => ['nama' => 'CV Fortuna (PB Makmur Jaya) Sragen', 'owner' => 'Bu Winarti, Dr Puji Setiawan'],
                'products' => [
                    ['nama' => 'Siip', 'sub' => $subC4],
                    ['nama' => 'Bestie', 'sub' => $subC4],
                    ['nama' => '10kg Sip', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Mitra Tani Karang Pandan', 'owner' => 'Eko Wahyono Remin'],
                'products' => [
                    ['nama' => 'Naga Bu Remin', 'sub' => $subC4],
                    ['nama' => 'Raja Remin', 'sub' => $subC4],
                    ['nama' => 'Wangi PT', 'sub' => $subWangi],
                ],
            ],
            [
                'supplier' => ['nama' => 'HMI (Himawari Group) Sragen', 'owner' => 'Setyo Bayu Haji'],
                'products' => [
                    ['nama' => 'Panda', 'sub' => $subC4],
                    ['nama' => 'Ikan Dory', 'sub' => $subC4],
                    ['nama' => 'Siomay', 'sub' => $subC4],
                    ['nama' => 'Putri Indonesia Biru', 'sub' => $subC4],
                    ['nama' => 'Naga Mas Tekad Jadi', 'sub' => $subC4],
                    ['nama' => 'WaliSongo', 'sub' => $subC4],
                    ['nama' => 'Patah Wangi', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Dewi Ayu Ngrawoh Matesih', 'owner' => 'Pak Wardi, Bu Tri Warsini'],
                'products' => [
                    ['nama' => 'Dewi Ayu', 'sub' => $subC4],
                    ['nama' => 'Mentik WT', 'sub' => $subWangi],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Sapenkec Mojolaban', 'owner' => 'Sarwanto Ngiri'],
                'products' => [
                    ['nama' => 'Pacul', 'sub' => $subC4],
                    ['nama' => 'Cething Mas', 'sub' => $subC4],
                    ['nama' => 'Wangi SW', 'sub' => $subWangi],
                    ['nama' => 'Rijek', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Ladang Mas Sukorejo Sragen', 'owner' => 'Ambar Nguwer'],
                'products' => [
                    ['nama' => 'Bambu', 'sub' => $subC4],
                    ['nama' => 'Kendil', 'sub' => $subC4],
                    ['nama' => 'Naga Mas JK', 'sub' => $subC4],
                    ['nama' => 'Patah PAB', 'sub' => $subC4],
                    ['nama' => 'Menir', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Mega Perkasa Sragen', 'owner' => 'Mas Eksan, Mas Daryono'],
                'products' => [
                    ['nama' => 'Angsa Terbang', 'sub' => $subC4],
                    ['nama' => 'Dolphin', 'sub' => $subC4],
                    ['nama' => 'Pari Ijo', 'sub' => $subC4],
                    ['nama' => 'Anggur', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Salmaira Matesih Karang Anyar', 'owner' => 'Pak Parwitto, Mbak Nanik'],
                'products' => [
                    ['nama' => 'KOI', 'sub' => $subC4],
                    ['nama' => 'Safira', 'sub' => $subC4],
                    ['nama' => 'Dimas', 'sub' => $subC4],
                    ['nama' => 'Jago Biru', 'sub' => $subC4],
                    ['nama' => 'Lele Hitam', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Citra Abadi Bermartabat Mojogedang', 'owner' => 'Pak Joko CAB'],
                'products' => [
                    ['nama' => 'Bengawan CAB', 'sub' => $subC4],
                    ['nama' => 'Mbok Ben', 'sub' => $subC4],
                    ['nama' => 'Kinclong', 'sub' => $subC4],
                    ['nama' => 'Untung', 'sub' => $subC4],
                    ['nama' => 'Pandawa', 'sub' => $subC4],
                    ['nama' => 'Indokoki', 'sub' => $subC4],
                    ['nama' => 'Mentik Semongko', 'sub' => $subWangi],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Ragil Nguripi Sragen', 'owner' => 'Pak Maryadi'],
                'products' => [
                    ['nama' => 'Bandeng Biru', 'sub' => $subC4],
                    ['nama' => 'Bandeng Pink', 'sub' => $subC4],
                    ['nama' => 'Patah Polos', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Trisno Makmur Matesih Karang Pandan', 'owner' => 'Mbak Eki Mulyani, Pak Sutrisno'],
                'products' => [
                    ['nama' => 'Naga Sandy Putra', 'sub' => $subC4],
                    ['nama' => 'Putri Indonesia Hijau', 'sub' => $subC4],
                    ['nama' => 'Alpukat', 'sub' => $subC4],
                    ['nama' => 'Stobery A', 'sub' => $subC4],
                    ['nama' => 'Raja Baru Makmur', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Gantari Karang Anyar', 'owner' => 'Pak Darto, Yeni Pebriana'],
                'products' => [
                    ['nama' => 'GNR', 'sub' => $subC4],
                    ['nama' => 'Bonsai', 'sub' => $subC4],
                    ['nama' => 'Wangi GNR', 'sub' => $subWangi],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Tomo Juwiring', 'owner' => 'Apringga Tri Nugraha'],
                'products' => [
                    ['nama' => 'Tomo', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Rukun Hasil Tani (Wonosari, Delanggu)', 'owner' => 'Bu HJ Harti Umy'],
                'products' => [
                    ['nama' => 'C4 AN', 'sub' => $subC4],
                    ['nama' => 'Raja HT', 'sub' => $subC4],
                    ['nama' => 'HT Pink', 'sub' => $subC4],
                    ['nama' => 'Apel HT', 'sub' => $subC4],
                    ['nama' => 'Mawar HT', 'sub' => $subC4],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB Ira Putri Jaya Kudus', 'owner' => 'Bu Tini Kudus'],
                'products' => [
                    ['nama' => 'Ketan Srikoyo', 'sub' => $subKetan],
                    ['nama' => 'Ketan Tawon', 'sub' => $subKetan],
                    ['nama' => 'Ketan Samurai', 'sub' => $subKetan],
                    ['nama' => 'Ketan Kembang', 'sub' => $subKetan],
                    ['nama' => 'Ketan Dua Mawar', 'sub' => $subKetan],
                ],
            ],
            [
                'supplier' => ['nama' => 'PB HD Herlina Subang', 'owner' => 'Pak Paimo HD'],
                'products' => [
                    ['nama' => 'Ketan Herlina', 'sub' => $subKetan],
                    ['nama' => 'Patah Ketan', 'sub' => $subKetan],
                ],
            ],
            [
                'supplier' => ['nama' => 'Makelar Mixed Theme!!s', 'owner' => 'Dandil, Sartono'],
                'products' => [
                    ['nama' => 'Ketan Belimbing', 'sub' => $subKetan],
                    ['nama' => 'Ketan Tulip', 'sub' => $subKetan],
                    ['nama' => 'Ketan Rajawali', 'sub' => $subKetan],
                    ['nama' => 'Ketan SJM', 'sub' => $subKetan],
                ],
            ],
            [
                'supplier' => ['nama' => 'Ketan Subang Batang', 'owner' => 'Mas Epis Rizq Zia'],
                'products' => [
                    ['nama' => 'Ketan DPJ', 'sub' => $subKetan],
                ],
            ],
            [
                'supplier' => ['nama' => 'CV Aditama Delanggu', 'owner' => 'Pak Eddy, Fendi Delanggu'],
                'products' => [
                    ['nama' => 'Kacer', 'sub' => $subC4],
                    ['nama' => 'Muray Batu', 'sub' => $subC4],
                ],
            ],
        ];

        foreach ($data as $entry) {
            $supplierData = $entry['supplier'];
            // create or update supplier; if supplier exists update owner/keterangan when provided
            $supplier = Supplier::firstOrCreate(
                ['nama_supplier' => $supplierData['nama']],
                [
                    'kode_supplier' => 'SUP-' . Str::upper(Str::random(6)),
                    'keterangan' => 'Owner: ' . ($supplierData['owner'] ?? ''),
                    'owner' => $supplierData['owner'] ?? null,
                ]
            );

            // If supplier already existed but owner is provided in the data, ensure it's persisted
            if (!empty($supplierData['owner']) && ($supplier->owner !== $supplierData['owner'] || empty($supplier->keterangan))) {
                $supplier->update([
                    'owner' => $supplierData['owner'],
                    'keterangan' => 'Owner: ' . $supplierData['owner'],
                ]);
            }

            foreach ($entry['products'] as $p) {
                $sub = $p['sub'];
                Product::firstOrCreate(
                    ['nama_produk' => $p['nama'], 'subcategory_id' => $sub->id],
                    [
                        'kode_produk' => 'PRD-' . Str::upper(Str::random(6)),
                        'description' => null,
                        'satuan' => 'Kg',
                        'category_id' => $sub->category_id,
                        'supplier_id' => $supplier->id,
                    ]
                );
            }
        }
    }
}

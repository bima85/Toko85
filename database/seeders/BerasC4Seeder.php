<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BerasC4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Category: BERAS
        $category = Category::firstOrCreate([
            'nama_kategori' => 'BERAS'
        ], [
            'kode_kategori' => 'BERAS',
            'description' => 'Kategori produk beras',
        ]);

        // Get or create Subcategory: C4
        $subcategory = Subcategory::firstOrCreate([
            'nama_subkategori' => 'C4',
            'category_id' => $category->id
        ], [
            'kode_subkategori' => 'C4',
            'description' => 'Subkategori C4 untuk beras',
        ]);

        // List of products
        $products = [
            '5KG KL',
            '5KG WG',
            '5KG PD',
            '5KG ORG',
            '5KG C4',
            '5KG PUT IJO',
            '1KG BM',
            '5KG BM',
            'JEMPOL',
            'LAK',
            '10KG LELE',
            '10KG KL',
            '10KG SIP',
            '5KG GAJAH',
            'KELINCI',
            'LA',
            'GAJAH',
            'LELE B',
            'NG REMIN',
            'PUTRI BIRU',
            'WALI 9',
            'PRIMA',
            'SIP',
            'DEWI AYU',
            'PACUL',
            'TKD JADI',
            'BENGAWAN',
            'NG JK',
            'BAMBU',
            'PUT H',
            'STROBERI A',
            'JERUK',
            'SENYUM',
            'GNR',
            'AS',
            'KOI',
            'B MERAH',
            'ALPUKAT',
            'RAJA BARU',
            'BERUANG',
            'BONSAI',
            'ANGSA',
            'SAFIRA',
            'NN',
            'PANDA',
            'TGH',
            'DORY',
            'TOMO',
            'PATAH AY',
            'BANDENG B',
            'SIMBOK',
            'PULEN',
            'HT M',
            'BRM OR',
            'AN',
            'MENIR',
            'NG PR',
            'PATAH JK',
            'NG YY',
            'DIMAS',
            'JAGO',
            'HEPPY',
            'Dokar',
            'BRM',
            'NGRPR',
            'KACER',
            'ANGSA',
            'PKD JADI',
            'DIMAS',
            'BANDENG',
            'WALI 9',
            'SAFIRA',
            'KL',
            'DEWI',
            'PUT IJO',
            'SIP',
            'DOKER',
            'SIOMAY',
            'NG MR',
            'HTM',
            'PTH WG 50',
            'NG DOL',
        ];

        $index = 0;
        foreach ($products as $productName) {
            $index++;
            Product::firstOrCreate([
                'nama_produk' => $productName,
                'subcategory_id' => $subcategory->id,
            ], [
                'kode_produk' => 'BRS_' . str_pad($index, 3, '0', STR_PAD_LEFT) . '_' . strtoupper(str_replace([' ', '-'], '', $productName)),
                'description' => 'Produk beras ' . $productName,
                'category_id' => $category->id,
                'satuan' => 'kg',
            ]);
        }
    }
}

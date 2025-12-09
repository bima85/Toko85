<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BerasMENTIKSeeder extends Seeder
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

        // Get or create Subcategory: MENTIK
        $subcategory = Subcategory::firstOrCreate([
            'nama_subkategori' => 'MENTIK',
            'category_id' => $category->id
        ], [
            'kode_subkategori' => 'MT',
            'description' => 'Subkategori MENTIK untuk beras',
        ]);

        // List of products
        $products = [
            'MAWAR',
            'WT',
            'ANGGREK',
            'KASTURI',
            'LN',
            'PD',
            'NN',
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
            ]);
        }
    }
}

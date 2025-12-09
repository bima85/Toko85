<?php

namespace Database\Seeders;

use App\Models\StockCard;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Database\Seeder;

class StockCardSeeder extends Seeder
{
    /**
     * Seed stock card table dengan data contoh
     */
    public function run(): void
    {
        // Ambil beberapa produk
        $products = Product::limit(5)->get();
        $batches = StockBatch::limit(10)->get();

        if ($products->isEmpty()) {
            $this->command->warn('Tidak ada produk ditemukan. Jalankan ProductSeeder terlebih dahulu.');
            return;
        }

        $types = ['in', 'out', 'adjustment', 'return'];
        $referenceTypes = ['purchase', 'sale', 'adjustment', 'return', 'transfer'];
        $locations = ['Gudang A', 'Gudang B', 'Gudang C', 'Rak 1', 'Rak 2', 'Rak 3'];

        foreach ($products as $product) {
            // Stok masuk dari pembelian
            StockCard::create([
                'product_id' => $product->id,
                'batch_id' => $batches->random(1)->first()?->id,
                'type' => 'in',
                'qty' => rand(50, 500),
                'from_location' => 'Supplier',
                'to_location' => $locations[array_rand($locations)],
                'reference_type' => 'purchase',
                'reference_id' => rand(1, 20),
                'note' => 'Pembelian dari supplier',
            ]);

            // Stok keluar dari penjualan
            StockCard::create([
                'product_id' => $product->id,
                'batch_id' => $batches->random(1)->first()?->id,
                'type' => 'out',
                'qty' => rand(10, 100),
                'from_location' => $locations[array_rand($locations)],
                'to_location' => 'Toko',
                'reference_type' => 'sale',
                'reference_id' => rand(1, 50),
                'note' => 'Penjualan ke pelanggan',
            ]);

            // Penyesuaian stok
            StockCard::create([
                'product_id' => $product->id,
                'batch_id' => null,
                'type' => 'adjustment',
                'qty' => rand(1, 50),
                'from_location' => null,
                'to_location' => null,
                'reference_type' => 'adjustment',
                'reference_id' => rand(1, 10),
                'note' => 'Koreksi stok opname',
            ]);

            // Retur barang
            if (rand(0, 1)) {
                StockCard::create([
                    'product_id' => $product->id,
                    'batch_id' => $batches->random(1)->first()?->id,
                    'type' => 'return',
                    'qty' => rand(1, 20),
                    'from_location' => 'Toko',
                    'to_location' => $locations[array_rand($locations)],
                    'reference_type' => 'return',
                    'reference_id' => rand(1, 30),
                    'note' => 'Retur barang tidak laku',
                ]);
            }
        }

        $this->command->info('StockCard seeder berhasil dijalankan!');
    }
}

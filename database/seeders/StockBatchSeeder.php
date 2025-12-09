<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockBatch;
use App\Models\StockCard;
use Illuminate\Database\Seeder;

class StockBatchSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      // Ambil produk yang sudah ada
      $products = Product::take(3)->get();

      if ($products->isEmpty()) {
         echo "❌ Tidak ada produk. Jalankan ProductSeeder terlebih dahulu.\n";
         return;
      }

      foreach ($products as $product) {
         // Batch 1: Toko Rak 1
         $batch1 = StockBatch::create([
            'product_id' => $product->id,
            'location_type' => 'store',
            'location_id' => null,
            'nama_tumpukan' => 'Rak 1',
            'qty' => 100.00,
         ]);

         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch1->id,
            'type' => 'IN',
            'qty' => 100.00,
            'to_location' => 'store',
            'note' => 'Penerimaan barang dari distributor',
         ]);

         // Batch 2: Toko Rak 2
         $batch2 = StockBatch::create([
            'product_id' => $product->id,
            'location_type' => 'store',
            'location_id' => null,
            'nama_tumpukan' => 'Rak 2',
            'qty' => 75.50,
         ]);

         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch2->id,
            'type' => 'IN',
            'qty' => 75.50,
            'to_location' => 'store',
            'note' => 'Penerimaan barang dari distributor',
         ]);

         // Batch 3: Gudang Tumpukan A
         $batch3 = StockBatch::create([
            'product_id' => $product->id,
            'location_type' => 'warehouse',
            'location_id' => null,
            'nama_tumpukan' => 'Tumpukan A',
            'qty' => 500.00,
         ]);

         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch3->id,
            'type' => 'IN',
            'qty' => 500.00,
            'to_location' => 'warehouse',
            'note' => 'Penerimaan dari supplier utama',
         ]);

         // Batch 4: Gudang Tumpukan B (contoh batch dengan qty kecil)
         $batch4 = StockBatch::create([
            'product_id' => $product->id,
            'location_type' => 'warehouse',
            'location_id' => null,
            'nama_tumpukan' => 'Tumpukan B',
            'qty' => 150.75,
         ]);

         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch4->id,
            'type' => 'IN',
            'qty' => 150.75,
            'to_location' => 'warehouse',
            'note' => 'Penerimaan dari supplier cadangan',
         ]);

         // Simulasi beberapa transaksi (OUT & MOVE)
         // Contoh: Penjualan dari toko
         $batch1->update(['qty' => 85.00]);
         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch1->id,
            'type' => 'OUT',
            'qty' => 15.00,
            'from_location' => 'store',
            'note' => 'Penjualan ke pelanggan',
         ]);

         // Contoh: Pemindahan dari gudang ke toko
         $batch3->update(['qty' => 450.00]);
         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => $batch3->id,
            'type' => 'MOVE',
            'qty' => 50.00,
            'from_location' => 'warehouse',
            'to_location' => 'store',
            'note' => 'Pemindahan untuk restocking toko',
         ]);

         $batch1->update(['qty' => 135.00]);
         StockCard::create([
            'product_id' => $product->id,
            'batch_id' => null,
            'type' => 'MOVE',
            'qty' => 50.00,
            'from_location' => 'warehouse',
            'to_location' => 'store',
            'note' => 'Pemindahan untuk restocking toko',
         ]);

         echo "✅ Seeded stock batches untuk produk: {$product->nama_produk}\n";
      }
   }
}

<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Warehouse;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class StoreWarehouseUnitSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      // Create Units
      $units = [
         ['nama_unit' => 'Kg', 'kode_unit' => 'KG'],
         ['nama_unit' => 'Liter', 'kode_unit' => 'L'],
         ['nama_unit' => 'Box', 'kode_unit' => 'BX'],
         ['nama_unit' => 'Pcs', 'kode_unit' => 'PCS'],
      ];

      foreach ($units as $unit) {
         Unit::firstOrCreate(
            ['kode_unit' => $unit['kode_unit']],
            $unit
         );
      }

      // Create Stores
      $stores = [
         ['nama_toko' => 'Toko Pusat', 'kode_toko' => 'TP', 'alamat' => 'Jl. Merdeka No. 1'],
         ['nama_toko' => 'Toko Cabang 1', 'kode_toko' => 'TC1', 'alamat' => 'Jl. Ahmad Yani No. 5'],
         ['nama_toko' => 'Toko Cabang 2', 'kode_toko' => 'TC2', 'alamat' => 'Jl. Sudirman No. 10'],
      ];

      foreach ($stores as $store) {
         Store::firstOrCreate(
            ['kode_toko' => $store['kode_toko']],
            $store
         );
      }

      // Create Warehouses
      $warehouses = [
         ['nama_gudang' => 'Gudang Pusat', 'kode_gudang' => 'GP', 'alamat' => 'Jl. Pusat No. 1'],
         ['nama_gudang' => 'Gudang Cabang', 'kode_gudang' => 'GC', 'alamat' => 'Jl. Cabang No. 5'],
      ];

      foreach ($warehouses as $warehouse) {
         Warehouse::firstOrCreate(
            ['kode_gudang' => $warehouse['kode_gudang']],
            $warehouse
         );
      }
   }
}

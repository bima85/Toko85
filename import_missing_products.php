<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== IMPORT PRODUK YANG HILANG ===\n";

$currentProducts = DB::table('products')->pluck('kode_produk')->toArray();
echo "Produk saat ini: " . count($currentProducts) . "\n";

$lines = file('products_insert.sql');
$values = [];
$inInsert = false;

foreach ($lines as $line) {
    $line = trim($line);
    if (strpos($line, 'INSERT INTO `products`') === 0) {
        $inInsert = true;
        continue;
    }

    if ($inInsert) {
        if (strpos($line, 'VALUES') === 0) continue;
        if (strpos($line, '(') === 0) {
            $values[] = $line;
        }
        if (strpos($line, ');') !== false) break;
    }
}

$added = 0;
$skipped = 0;

foreach ($values as $value) {
    // Parse the VALUES line using regex to handle quoted strings properly
    $value = trim($value, '(),');

    // Use regex to split by comma but not inside quotes
    $parts = preg_split('/,(?=(?:[^\'"]*[\'"][^\'"]*[\'"])*[^\'"]*$)/', $value);

    if (count($parts) >= 10) {
        $kode = trim($parts[1], "'");

        if (!in_array($kode, $currentProducts)) {
            // Extract data
            $id = trim($parts[0]);
            $nama = trim($parts[2], "'");
            $description = trim($parts[3], "'");
            $satuan = trim($parts[4], "'");
            $category_id = trim($parts[5]);
            $subcategory_id = trim($parts[6]);
            $supplier_id_raw = trim($parts[7]);
            $supplier_id = ($supplier_id_raw === 'NULL' || $supplier_id_raw === '') ? null : trim($supplier_id_raw);
            $created_at = trim($parts[8], "' \t");
            $updated_at = trim($parts[9], "' \t");

            try {
                DB::table('products')->insert([
                    'id' => $id,
                    'kode_produk' => $kode,
                    'nama_produk' => $nama,
                    'description' => $description,
                    'satuan' => $satuan,
                    'category_id' => $category_id,
                    'subcategory_id' => $subcategory_id,
                    'supplier_id' => $supplier_id,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                ]);
                $added++;
                echo "[SUCCESS] Menambahkan: $kode - $nama\n";
            } catch (Exception $e) {
                $skipped++;
                echo "[ERROR] Gagal menambahkan $kode: " . $e->getMessage() . "\n";
            }
        } else {
            $skipped++;
        }
    }
}

echo "\n=== HASIL IMPORT ===\n";
echo "Produk ditambahkan: $added\n";
echo "Produk dilewati: $skipped\n";
echo "Total produk sekarang: " . DB::table('products')->count() . "\n";

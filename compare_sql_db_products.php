<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== MEMBANDINGKAN PRODUK SQL DENGAN DATABASE ===\n\n";

$sqlPath = __DIR__ . '/bims2916_toko85.sql';
if (!file_exists($sqlPath)) {
    echo "File SQL tidak ditemukan: $sqlPath\n";
    exit(1);
}

$sql = file_get_contents($sqlPath);

// Ambil blok INSERT untuk products
if (!preg_match('/INSERT INTO `products`.*?VALUES(.*?);/s', $sql, $m)) {
    echo "Tidak menemukan blok INSERT INTO `products` di file SQL.\n";
    exit(1);
}

$valuesBlock = $m[1];

// Ambil semua kode produk (kolom ke-2) dengan regex yang mencari pola (id, 'KODE',
preg_match_all("/\(\s*\d+\s*,\s*'([^']+)'/", $valuesBlock, $matches);
$sqlCodes = array_map('trim', $matches[1] ?? []);
$sqlCodes = array_values(array_filter($sqlCodes));

echo "Produk di file SQL: " . count($sqlCodes) . "\n";

// Ambil semua kode produk dari database
$dbCodes = \App\Models\Product::query()->pluck('kode_produk')->map(fn($v) => trim($v))->toArray();

echo "Produk di database: " . count($dbCodes) . "\n\n";

$missingInDb = array_values(array_diff($sqlCodes, $dbCodes));
$extraInDb = array_values(array_diff($dbCodes, $sqlCodes));

echo "Produk yang ada di SQL tapi TIDAK ADA di DB: " . count($missingInDb) . "\n";
if (count($missingInDb) > 0) {
    echo "Contoh (maks 50):\n" . implode("\n", array_slice($missingInDb, 0, 50)) . "\n\n";
}

echo "Produk yang ada di DB tapi TIDAK ADA di SQL: " . count($extraInDb) . "\n";
if (count($extraInDb) > 0) {
    echo "Contoh (maks 50):\n" . implode("\n", array_slice($extraInDb, 0, 50)) . "\n\n";
}

if (count($missingInDb) === 0 && count($extraInDb) === 0) {
    echo "Hasil: PRODUK DATABASE SUDAH SESUAI DENGAN FILE SQL.\n";
} else {
    echo "Hasil: TERDAPAT PERBEDAAN. Jika Anda ingin, saya bisa menyinkronkan DB (menambahkan produk yang hilang dan/atau menghapus produk tambahan).\n";
}

echo "\n=== SELESAI ===\n";

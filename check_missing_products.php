<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ANALISIS PRODUK BACKUP ===\n";

$currentProducts = DB::table('products')->pluck('kode_produk')->toArray();
echo "Produk saat ini: " . count($currentProducts) . "\n";

$backupProducts = [];
$lines = file('products_insert.sql');

foreach ($lines as $line) {
    if (strpos($line, 'INSERT INTO') === 0) continue;
    if (strpos($line, '(') === 0) {
        $parts = explode(',', $line);
        if (count($parts) >= 2) {
            $kode = trim(str_replace(['(', "'"], '', $parts[1]));
            if (!in_array($kode, $currentProducts)) {
                $backupProducts[] = $kode;
            }
        }
    }
}

echo "Produk yang perlu ditambahkan: " . count($backupProducts) . "\n";
echo "Total produk di backup: " . (count($currentProducts) + count($backupProducts)) . "\n";

if (count($backupProducts) > 0) {
    echo "\n=== PRODUK YANG AKAN DITAMBAHKAN ===\n";
    foreach (array_slice($backupProducts, 0, 10) as $kode) {
        echo "- $kode\n";
    }
    if (count($backupProducts) > 10) {
        echo "... dan " . (count($backupProducts) - 10) . " produk lainnya\n";
    }
}

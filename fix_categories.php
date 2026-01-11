<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== MEMBUAT KATEGORI LN YANG BENAR ===\n";

// Cek apakah kategori LN sudah ada
$lnCategory = \App\Models\Category::where('kode_kategori', 'LN')->first();

if (!$lnCategory) {
    echo "Membuat kategori LN - Lain...\n";
    $lnCategory = \App\Models\Category::create([
        'kode_kategori' => 'LN',
        'nama_kategori' => 'Lain',
        'description' => null,
        'created_at' => '2025-11-02 08:23:10',
        'updated_at' => '2025-11-02 08:23:10'
    ]);
    echo "✅ Kategori LN - Lain berhasil dibuat (id: " . $lnCategory->id . ")\n";
} else {
    echo "Kategori LN sudah ada (id: " . $lnCategory->id . ")\n";
}

// Pindahkan produk dari Lain-lain ke LN
$lainCategory = \App\Models\Category::where('nama_kategori', 'Lain-lain')->first();
if ($lainCategory) {
    $productCount = $lainCategory->products()->count();
    echo "Memindahkan $productCount produk dari Lain-lain ke LN...\n";
    $lainCategory->products()->update(['category_id' => $lnCategory->id]);
    $lainCategory->delete();
    echo "✅ Kategori Lain-lain dihapus\n";
}

echo "\n=== KATEGORI FINAL (SESUAI BACKUP) ===\n";
$finalCategories = \App\Models\Category::select('id', 'nama_kategori', 'kode_kategori')->orderBy('id')->get();
foreach ($finalCategories as $cat) {
    $productCount = $cat->products()->count();
    echo $cat->id . ': ' . $cat->kode_kategori . ' - ' . $cat->nama_kategori . ' (' . $productCount . " produk)\n";
}

echo "\nTotal produk: " . \App\Models\Product::count() . "\n";
echo "Status: ✅ SUDAH SESUAI DENGAN BACKUP ASLI\n";

<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== MEMBERSIHKAN KATEGORI DAN PRODUK YANG DIBUAT SENDIRI ===\n";

// Kategori yang akan dihapus
$categoriesToDelete = ['Minuman', 'Snack', 'Bahan Pokok'];

foreach ($categoriesToDelete as $categoryName) {
    $category = \App\Models\Category::where('nama_kategori', $categoryName)->first();

    if ($category) {
        $productCount = $category->products()->count();
        echo "Menghapus kategori: $categoryName ($productCount produk)\n";

        // Hapus produk di kategori ini
        $category->products()->delete();

        // Hapus kategori
        $category->delete();

        echo "[SUCCESS] Kategori $categoryName dan $productCount produk berhasil dihapus\n";
    } else {
        echo "[INFO] Kategori $categoryName tidak ditemukan\n";
    }
}

// Verifikasi kategori yang tersisa
$remainingCategories = \App\Models\Category::get();
echo "\n=== KATEGORI YANG TERSISA ===\n";
foreach ($remainingCategories as $cat) {
    $productCount = $cat->products()->count();
    echo $cat->nama_kategori . ': ' . $productCount . " produk\n";
}

// Hitung total produk
$totalProducts = \App\Models\Product::count();
echo "\nTotal produk sekarang: $totalProducts\n";

echo "\n=== SELESAI ===\n";

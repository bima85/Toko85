<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check all purchases today
$todaysPurchases = \App\Models\Purchase::whereDate('tanggal_pembelian', '2026-01-24')->get();
echo "All purchases today (2026-01-24):\n";
echo "Total: " . $todaysPurchases->count() . "\n";
$todaysPurchases->each(function ($p) {
    $supplier = $p->supplier;
    echo "- " . $p->no_invoice . " | Supplier: " . ($supplier ? $supplier->nama_supplier : "Unknown") . " (ID: " . $p->supplier_id . ")\n";
});

echo "\n\nAll purchases matching PB/2026/01/24-*:\n";
$pattern = \App\Models\Purchase::where('no_invoice', 'like', 'PB/2026/01/24-%')->get();
echo "Total: " . $pattern->count() . "\n";
$pattern->each(function ($p) {
    $supplier = $p->supplier;
    echo "- " . $p->no_invoice . " | Supplier: " . ($supplier ? $supplier->nama_supplier : "Unknown") . " (ID: " . $p->supplier_id . ")\n";
});

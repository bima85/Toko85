<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "All suppliers in database:\n";
$suppliers = \App\Models\Supplier::orderBy('nama_supplier')->get();
echo "Total: " . $suppliers->count() . "\n";
$suppliers->each(function ($s, $i) {
    if ($i < 30) { // Show first 30
        $count = \App\Models\Purchase::where('supplier_id', $s->id)->count();
        echo ($i + 1) . ". ID: " . $s->id . ", Nama: " . $s->nama_supplier . ", Owner: " . ($s->owner ?? 'N/A') . ", Purchases: " . $count . "\n";
    }
});

echo "\n\nAll purchases today (2026-01-24):\n";
$today = \App\Models\Purchase::whereDate('tanggal_pembelian', '2026-01-24')->orderBy('no_invoice')->get();
echo "Total: " . $today->count() . "\n";
$today->each(function ($p) {
    $supp = $p->supplier;
    echo "- " . $p->no_invoice . " | Supplier: " . ($supp ? $supp->nama_supplier : "Unknown") . " (ID: " . $p->supplier_id . ")\n";
});

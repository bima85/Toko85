<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check supplier ID 18
$supplier = \App\Models\Supplier::find(18);
echo "Supplier ID 18:\n";
echo "Nama: " . ($supplier ? $supplier->nama_supplier : "Not found") . "\n";
echo "Owner: " . ($supplier ? ($supplier->owner ?? 'N/A') : 'N/A') . "\n\n";

if ($supplier) {
    $purchases = \App\Models\Purchase::where('supplier_id', 18)->get();
    echo "Total purchases: " . $purchases->count() . "\n";
    $purchases->each(function ($p) {
        echo "- " . $p->no_invoice . " (" . $p->tanggal_pembelian->format('Y-m-d') . ")\n";
    });
}

echo "\n\nAll invoices with PB/2026/01/24:\n";
$invoices = \App\Models\Purchase::where('no_invoice', 'like', 'PB/2026/01/24%')->get();
$invoices->each(function ($p) {
    $supp = $p->supplier;
    echo "- " . $p->no_invoice . " | Supplier ID: " . $p->supplier_id . ", Name: " . ($supp ? $supp->nama_supplier : "Unknown") . "\n";
});

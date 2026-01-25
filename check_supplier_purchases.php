<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check supplier "Bu HJ Harti Umy"
$supplier = \App\Models\Supplier::where('nama_supplier', 'like', '%Bu HJ Harti%')->first();

if ($supplier) {
    echo "Supplier Found:\n";
    echo "ID: " . $supplier->id . "\n";
    echo "Nama: " . $supplier->nama_supplier . "\n";
    echo "Owner: " . ($supplier->owner ?? 'N/A') . "\n\n";

    $purchases = \App\Models\Purchase::where('supplier_id', $supplier->id)->orderBy('id', 'desc')->get();
    echo "Total purchases: " . $purchases->count() . "\n";
    $purchases->each(function ($p) {
        echo "- " . $p->no_invoice . " (" . $p->tanggal_pembelian->format('Y-m-d') . ", Status: " . $p->status . ")\n";
    });
} else {
    echo "Supplier 'Bu HJ Harti Umy' not found\n";
}

echo "\n\nAll suppliers with 'HJ' in name:\n";
$suppliers = \App\Models\Supplier::where('nama_supplier', 'like', '%HJ%')->get();
echo "Total: " . $suppliers->count() . "\n";
$suppliers->each(function ($s) {
    $count = \App\Models\Purchase::where('supplier_id', $s->id)->count();
    echo "- ID: " . $s->id . ", Nama: " . $s->nama_supplier . ", Purchases: " . $count . "\n";
});

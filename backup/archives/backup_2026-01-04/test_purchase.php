<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Test create purchase
try {
    $purchase = \App\Models\Purchase::create([
        'no_invoice' => 'TEST/2026/01/03-001',
        'tanggal_pembelian' => '2026-01-03',
        'supplier_id' => 57,
        'store_id' => 1,
        'warehouse_id' => 1,
        'status' => 'completed',
        'keterangan' => 'Test Purchase',
    ]);

    echo "✓ Purchase created successfully: ID {$purchase->id}\n";
    echo "  No Invoice: {$purchase->no_invoice}\n";
} catch (\Exception $e) {
    echo "✗ Error creating purchase:\n";
    echo $e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$item = \App\Models\PurchaseItem::where('purchase_id', 1)->first();
if ($item) {
    echo "PurchaseItem ID: {$item->id}\n";
    echo "Destination Type: '{$item->destination_type}'\n";
    echo "Qty: {$item->qty}\n";
    echo "Qty_Gudang: {$item->qty_gudang}\n";
    echo "All data:\n";
    var_dump($item->toArray());
}

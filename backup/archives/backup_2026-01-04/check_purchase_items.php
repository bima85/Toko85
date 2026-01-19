<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$items = DB::table('purchase_items')->select('id', 'purchase_id', 'qty', 'qty_gudang', 'harga_beli', 'total')->get();

echo "=== Purchase Items Database Values ===\n";
foreach ($items as $item) {
    $calculated = ($item->qty + $item->qty_gudang) * $item->harga_beli;
    echo "ID {$item->id} (Purchase {$item->purchase_id}):\n";
    echo "  Qty={$item->qty}, Qty_Gudang={$item->qty_gudang}, Harga={$item->harga_beli}\n";
    echo "  Total di DB: {$item->total}\n";
    echo "  Calculated (qty+gudang)×harga: {$calculated}\n";
    echo '  Match: '.($item->total == $calculated ? 'YES ✓' : 'NO ✗')."\n\n";
}

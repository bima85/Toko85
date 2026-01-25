<?php
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\StockBatch;

// Create application
$app = require 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Http\Kernel::class);

// Check raw data
echo "=== RAW DATABASE DATA ===\n";
$raw = DB::table('stock_batches')->limit(5)->get();
foreach ($raw as $row) {
    echo "ID: {$row->id}, Product: {$row->product_id}, Qty: {$row->qty}\n";
    echo "  Columns present: ";
    foreach ((array)$row as $col => $val) {
        echo "$col ";
    }
    echo "\n";
}

echo "\n=== ELOQUENT MODEL DATA ===\n";
$batches = StockBatch::limit(5)->get();
foreach ($batches as $batch) {
    echo "ID: {$batch->id}, Product: {$batch->product_id}, Qty: {$batch->qty}\n";
    echo "  location_type: " . ($batch->location_type ?? 'NULL') . "\n";
    echo "  location_id: " . ($batch->location_id ?? 'NULL') . "\n";
}

echo "\n=== QUERY FOR STORE=1 ===\n";
$query = StockBatch::where('location_type', 'store')
    ->where('location_id', 1);
echo "SQL: " . $query->toSql() . "\n";
$batches = $query->get();
echo "Results: " . count($batches) . "\n";
foreach ($batches as $batch) {
    echo "  ID: {$batch->id}, Product: {$batch->product_id}, Qty: {$batch->qty}\n";
}

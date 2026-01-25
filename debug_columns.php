<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$schema = \Illuminate\Support\Facades\Schema::class;
$columns = \Illuminate\Support\Facades\DB::connection()->getSchemaBuilder()->getColumnListing('purchase_items');
echo "Columns in purchase_items:\n";
foreach ($columns as $col) {
    echo "  - $col\n";
}

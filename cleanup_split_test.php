<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Cleaning SPLIT test data...\n";
DB::delete("DELETE FROM purchases WHERE no_invoice LIKE 'SPLIT%'");
echo "✓ Cleaned!\n";

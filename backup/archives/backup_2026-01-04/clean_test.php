<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Delete test purchase
$deleted = \App\Models\Purchase::where('no_invoice', 'like', 'TEST%')->delete();
echo "✓ Deleted $deleted test purchase(s)\n";

// Also check what invoice numbers exist for today
$today = date('Y/m/d');
$invoices = \App\Models\Purchase::where('no_invoice', 'like', 'PB/'.$today.'%')
    ->pluck('no_invoice')
    ->sort()
    ->values();

echo "\nExisting invoices for today ($today):\n";
foreach ($invoices as $inv) {
    echo "  - $inv\n";
}

$nextNum = $invoices->count() + 1;
$nextInvoice = 'PB/'.$today.'-'.str_pad($nextNum, 3, '0', STR_PAD_LEFT);
echo "\nNext available invoice would be: $nextInvoice\n";

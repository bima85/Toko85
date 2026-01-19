<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Get purchase 005
$purchase = \App\Models\Purchase::where('no_invoice', 'PB/2026/01/03-005')->first();
if (! $purchase) {
    echo "Purchase not found\n";
    exit;
}

echo "Purchase: {$purchase->no_invoice} (ID {$purchase->id})\n";

// Calculate correct total from purchase_items
$correctTotal = $purchase->purchaseItems->sum('total');
echo 'Correct total from purchase_items: Rp '.number_format($correctTotal, 0)."\n";

// Update TransactionHistory
$transaction = \App\Models\TransactionHistory::where('reference_id', $purchase->id)
    ->where('reference_type', 'purchase')
    ->first();

if ($transaction) {
    echo 'Old TransactionHistory amount: Rp '.number_format($transaction->amount, 0)."\n";

    $transaction->update([
        'amount' => $correctTotal,
    ]);

    echo '✓ Updated to: Rp '.number_format($transaction->amount, 0)."\n";
} else {
    echo "TransactionHistory not found\n";
}

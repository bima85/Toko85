<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Check latest transaction history
$transactions = \App\Models\TransactionHistory::latest()
    ->limit(10)
    ->get(['id', 'transaction_code', 'amount', 'reference_id', 'reference_type', 'created_at']);

echo "=== Latest 10 TransactionHistory Entries ===\n";
foreach ($transactions as $t) {
    echo "ID {$t->id}: {$t->transaction_code} - Rp ".number_format($t->amount, 0)." (Ref: {$t->reference_type}:{$t->reference_id}) - {$t->created_at}\n";
}

echo "\n=== Check if PB/2026/01/03-005 purchase exists ===\n";
$purchases = \App\Models\Purchase::where('no_invoice', 'like', 'PB/2026/01/03-00%')
    ->get(['id', 'no_invoice', 'created_at']);

foreach ($purchases as $p) {
    $trans = \App\Models\TransactionHistory::where('reference_id', $p->id)
        ->where('reference_type', 'purchase')
        ->get(['transaction_code', 'amount']);

    echo "\nPurchase {$p->no_invoice} (ID {$p->id}):\n";
    if ($trans->count() > 0) {
        foreach ($trans as $t) {
            echo "  - {$t->transaction_code}: Rp ".number_format($t->amount, 0)."\n";
        }
    } else {
        echo "  - NO TRANSACTION HISTORY FOUND\n";
    }
}

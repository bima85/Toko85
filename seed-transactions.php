<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run the seeder
$app->make(\Illuminate\Database\Seeder::class)->call(\Database\Seeders\TransactionHistorySeeder::class);

echo "Transaction history seeded successfully!\n";

<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$db = $app->make('db');
try {
    echo "DISABLE FK\n";
    $db->statement('SET FOREIGN_KEY_CHECKS=0');
    $files = ['stock_adjustments_clean.sql', 'stock_batches_clean.sql', 'stock_cards_clean.sql'];
    foreach ($files as $f) {
        echo "IMPORTING {$f}\n";
        $sql = file_get_contents($f);
        if ($sql === false) {
            throw new RuntimeException("Cannot read {$f}");
        }
        $db->unprepared($sql);
    }
    echo "ENABLE FK\n";
    $db->statement('SET FOREIGN_KEY_CHECKS=1');
    echo "IMPORT_DONE\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

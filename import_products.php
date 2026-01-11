<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Disabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

echo "Truncating tables...\n";
DB::statement('TRUNCATE TABLE categories;');
DB::statement('TRUNCATE TABLE subcategories;');
DB::statement('TRUNCATE TABLE suppliers;');
DB::statement('TRUNCATE TABLE products;');

$files = [
    'categories_insert.sql',
    'subcategories_insert.sql',
    'suppliers_insert.sql',
    'products_insert.sql',
];

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "File $file not found.\n";
        continue;
    }

    $sql = file_get_contents($file);
    echo "Importing $file...\n";
    try {
        DB::unprepared($sql);
        echo "Success: $file\n";
    } catch (Exception $e) {
        echo "Error in $file: " . $e->getMessage() . "\n";
    }
}

echo "Enabling foreign key checks...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "Done.\n";

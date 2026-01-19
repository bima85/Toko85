<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $rows = DB::select("SHOW COLUMNS FROM `stock_cards` LIKE 'type'");
} catch (Exception $e) {
    echo 'ERROR: '.$e->getMessage().PHP_EOL;
    exit(2);
}

if (empty($rows)) {
    echo "NOT_FOUND: table or column 'type' on 'stock_cards' not found".PHP_EOL;
    exit(1);
}

$row = (array) $rows[0];
$typeDef = $row['Type'] ?? $row['type'] ?? null;
if (! $typeDef) {
    echo 'UNKNOWN: cannot read column type definition'.PHP_EOL;
    exit(3);
}

echo $typeDef.PHP_EOL;

if (stripos($typeDef, "'return'") !== false) {
    echo "HAS_RETURN: enum includes 'return'".PHP_EOL;
    exit(0);
} else {
    echo "MISSING_RETURN: enum does not include 'return'".PHP_EOL;
    exit(0);
}

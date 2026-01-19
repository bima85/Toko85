<?php
$f = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($f)) {
    echo "no log\n";
    exit;
}
$lines = file($f, FILE_IGNORE_NEW_LINES);
$tail = array_slice($lines, -300);
foreach ($tail as $l) {
    echo $l . PHP_EOL;
}

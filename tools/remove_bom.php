<?php
// Simple script to remove UTF-8 BOM from blade view files under resources/views
$dir = __DIR__ . '/../resources/views';

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$changed = 0;

foreach ($files as $file) {
    if ($file->isFile() && preg_match('/\.blade\.php$/', $file->getFilename())) {
        $path = $file->getPathname();
        $contents = file_get_contents($path);
        if ($contents === false) continue;
        // UTF-8 BOM bytes
        $bom = "\xEF\xBB\xBF";
        if (substr($contents, 0, 3) === $bom) {
            $new = substr($contents, 3);
            file_put_contents($path, $new);
            echo "Stripped BOM: {$path}\n";
            $changed++;
        }
    }
}

if ($changed === 0) {
    echo "No BOM found in blade files.\n";
} else {
    echo "Done. Removed BOM from {$changed} files.\n";
}

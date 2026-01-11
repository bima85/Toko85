<?php
require_once 'vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$products = App\Models\Product::orderBy('id')->get();
$lines = [];
foreach ($products as $p) {
    $id = (int)$p->id;
    $kode = str_replace("'", "''", (string)$p->kode_produk);
    $nama = str_replace("'", "''", (string)$p->nama_produk);
    $desc = is_null($p->description) ? 'NULL' : "'" . str_replace("'", "''", (string)$p->description) . "'";
    $satuan = str_replace("'", "''", (string)$p->satuan);
    $cat = is_null($p->category_id) ? 'NULL' : (int)$p->category_id;
    $sub = is_null($p->subcategory_id) ? 'NULL' : (int)$p->subcategory_id;
    $sup = is_null($p->supplier_id) ? 'NULL' : (int)$p->supplier_id;
    $created = $p->created_at?->format('Y-m-d H:i:s') ?? '0000-00-00 00:00:00';
    $updated = $p->updated_at?->format('Y-m-d H:i:s') ?? $created;
    $lines[] = "($id, '$kode', '$nama', $desc, '$satuan', $cat, $sub, $sup, '$created', '$updated')";
}
echo implode(",\n", $lines);

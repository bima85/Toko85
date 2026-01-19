<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoleMenuAccess;

$all = RoleMenuAccess::all()->map(fn($r) => [$r->role_name, $r->route_name])->toArray();
echo json_encode($all);

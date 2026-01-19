<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoleMenuAccess;

$routes = RoleMenuAccess::where('role_name', 'user')->pluck('route_name')->toArray();
echo json_encode($routes);

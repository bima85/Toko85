<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoleMenuAccess;

$exists = RoleMenuAccess::where('role_name', 'user')->where('route_name', 'admin.transactions.manage')->exists();
if ($exists) {
    echo "mapping exists\n";
    exit(0);
}
RoleMenuAccess::create(['role_name' => 'user', 'route_name' => 'admin.transactions.manage']);
echo "mapping added\n";

<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$users = User::role('user')->get()->map(fn($u) => [$u->id, $u->email, $u->getRoleNames()->toArray()])->toArray();

echo json_encode($users);

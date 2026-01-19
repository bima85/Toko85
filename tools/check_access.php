<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\RoleMenuAccess;
use Illuminate\Support\Facades\Config;

$email = $argv[1] ?? null;
$route = $argv[2] ?? 'admin.transactions.manage';
if (! $email) {
    echo "Usage: php tools/check_access.php user@example.com [route_name]\n";
    exit(1);
}

$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found\n";
    exit(2);
}

$roleNames = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
$allowedPatterns = [];
if (! empty($roleNames)) {
    $dbPatterns = RoleMenuAccess::whereIn('role_name', $roleNames)->pluck('route_name')->toArray();
    $allowedPatterns = array_merge($allowedPatterns, $dbPatterns);
}

if (empty($allowedPatterns)) {
    $mapping = Config::get('menu_access.roles', []);
    foreach ($roleNames as $roleName) {
        if (isset($mapping[$roleName]) && is_array($mapping[$roleName])) {
            $allowedPatterns = array_merge($allowedPatterns, $mapping[$roleName]);
        }
    }
}

echo "User: {$user->email}\n";
echo "Roles: " . implode(', ', $roleNames) . "\n";
echo "Allowed patterns: " . json_encode($allowedPatterns) . "\n";
echo "Checking route: {$route}\n";
$matched = false;
foreach ($allowedPatterns as $pattern) {
    if ($pattern === '*') {
        $matched = true;
        break;
    }
    $regex = '/^' . str_replace('\\*', '.*', preg_quote($pattern, '/')) . '$/i';
    echo "Pattern: {$pattern} => Regex: {$regex}\n";
    $res = preg_match($regex, $route);
    echo "preg_match result: " . var_export($res, true) . "\n";
    if ($res) {
        $matched = true;
        break;
    }
}

echo $matched ? "ALLOWED\n" : "DENIED\n";

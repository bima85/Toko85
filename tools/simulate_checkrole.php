<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\Http\Middleware\CheckRoleAccess;
use App\Models\User;

$email = $argv[1] ?? 'admin@admin.com';
$routeName = $argv[2] ?? 'admin.transactions.manage';

$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(2);
}

// Create request
$request = Request::create('/admin/transactions/manage', 'GET');
// Resolve route object from router
$router = $app->make('router');
$route = $router->getRoutes()->getByName($routeName);
if (! $route) {
    echo "Route not found: $routeName\n";
    exit(3);
}

// attach route resolver to request so $request->route() returns it
$request->setRouteResolver(function () use ($route) {
    return $route;
});

// Authenticate as user by setting the auth guard user resolver
// Simpler: set the user in the Auth facade's guard resolver
$auth = $app->make(Illuminate\Contracts\Auth\Factory::class);
$guard = $auth->guard();
$guard->setUser($user);

$middleware = new CheckRoleAccess();

try {
    $result = $middleware->handle($request, function ($req) {
        return 'NEXT_OK';
    });
    if ($result === 'NEXT_OK') {
        echo "MIDDLEWARE: ALLOWED\n";
    } else {
        echo "MIDDLEWARE: returned: ";
        var_export($result);
        echo "\n";
    }
} catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
    echo "MIDDLEWARE: HttpException code=" . $e->getStatusCode() . " message=" . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "MIDDLEWARE: Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
}

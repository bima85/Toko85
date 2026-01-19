<?php


namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\RoleMenuAccess;

class NavHelper
{
    /**
     * Check if current user has one of the given roles
     * @param array|string $roles
     * @return bool
     */
    public static function visibleForRole($roles): bool
    {
        if (!Auth::check()) {
            return false;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }
        // Superadmin always can see
        if (Auth::user()->hasRole('superadmin')) {
            return true;
        }
        foreach ($roles as $role) {
            if (Auth::user()->hasRole($role)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Check if route is active
     */
    public static function isRouteActive($routes)
    {
        if (is_string($routes)) {
            $routes = [$routes];
        }

        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if current user can access a specific named route according to role->menu mapping
     * Falls back to config('menu_access') when DB has no entries
     */
    public static function canAccessRoute(string $routeName): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Superadmin bypass
        if (method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
            return true;
        }

        $roles = [];
        if (method_exists($user, 'getRoleNames')) {
            $roles = $user->getRoleNames()->toArray();
        }

        // If DB has entries, prefer DB
        if (RoleMenuAccess::count() > 0) {
            return RoleMenuAccess::whereIn('role_name', $roles)
                ->where('route_name', $routeName)
                ->exists();
        }

        // Fallback to config mapping
        $mapping = config('menu_access.roles', []);
        foreach ($roles as $role) {
            if (! isset($mapping[$role])) {
                continue;
            }
            foreach ($mapping[$role] as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if any route in the list is accessible
     */
    public static function canAccessAny(array $routes): bool
    {
        foreach ($routes as $r) {
            if (self::canAccessRoute($r)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get active class for nav-link
     */
    public static function activeLink($routes, $class = 'active')
    {
        return self::isRouteActive($routes) ? $class : '';
    }

    /**
     * Get menu-open class for nav-item dropdown
     */
    public static function menuOpen($routes, $class = 'menu-open')
    {
        return self::isRouteActive($routes) ? $class : '';
    }
}

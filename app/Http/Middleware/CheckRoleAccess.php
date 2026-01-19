<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\RoleMenuAccess;
use Illuminate\Support\Facades\Auth;

class CheckRoleAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If no authenticated user, let auth middleware handle it
        $user = Auth::user();
        if (! $user) {
            return $next($request);
        }

        // Log current user and roles for debugging
        try {
            $dbgRoles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
            Log::info('CheckRoleAccess: start', [
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'roles' => $dbgRoles,
            ]);
        } catch (\Throwable $e) {
            // continue silently if roles can't be read
            Log::debug('CheckRoleAccess: could not read roles', ['err' => $e->getMessage()]);
        }

        // superadmin bypass
        if (method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
            return $next($request);
        }

        $routeName = $request->route() ? $request->route()->getName() : null;

        // Determine the target to check: route name or livewire component name
        $target = $routeName;
        if ($request->has('fingerprint') && isset($request->input('fingerprint')['name'])) {
            $componentName = $request->input('fingerprint')['name'];
            $target = $componentName;
            Log::debug('CheckRoleAccess: livewire component detected', ['component' => $componentName]);
        }

        // load config mapping
        $mapping = Config::get('menu_access.roles', []);
        // collect allowed patterns from DB first
        $allowedPatterns = [];
        if (method_exists($user, 'getRoleNames')) {
            $roleNames = $user->getRoleNames()->toArray();
            $dbPatterns = RoleMenuAccess::whereIn('role_name', $roleNames)->pluck('route_name')->toArray();
            $allowedPatterns = array_merge($allowedPatterns, $dbPatterns);
            // if found in DB and contains '*' allow all
            if (in_array('*', $allowedPatterns)) {
                return $next($request);
            }
        }

        // Fallback to config if DB has no entries
        if (empty($allowedPatterns)) {
            if (method_exists(Config::class, 'get')) {
                foreach ($user->getRoleNames() as $roleName) {
                    if (isset($mapping[$roleName]) && is_array($mapping[$roleName])) {
                        $allowedPatterns = array_merge($allowedPatterns, $mapping[$roleName]);
                    }
                }
            }
        }

        // Debug log: what route name is being evaluated and current allowed patterns
        Log::debug('CheckRoleAccess: route-eval', [
            'routeName' => $routeName,
            'target' => $target,
            'allowedPatterns' => $allowedPatterns,
        ]);
        if (! $routeName) {
            return $next($request);
        }

        // Only enforce for admin area and settings/stock-card routes
        if (! preg_match('/^(admin\.|stock-card\.|settings\.)/i', $target)) {
            return $next($request);
        }

        // Check against patterns (support '*' wildcard)
        $matched = false;
        foreach ($allowedPatterns as $pattern) {
            if ($pattern === '*') {
                $matched = true;
                break;
            }
            $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/i';
            if (preg_match($regex, $target)) {
                $matched = true;
                Log::debug('CheckRoleAccess: matched pattern', ['pattern' => $pattern, 'target' => $target]);
                break;
            }
        }

        if (! $matched) {
            // Log helpful debugging info
            Log::warning('CheckRoleAccess denied', [
                'user_id' => $user->id ?? null,
                'roles' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [],
                'route' => $routeName,
                'target' => $target,
                'allowedPatterns' => $allowedPatterns,
            ]);

            // Additional trace to help correlate which layer caused a 403 in live requests
            try {
                $rawTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $trace = [];
                foreach (array_slice($rawTrace, 0, 25) as $t) {
                    $trace[] = (isset($t['file']) ? $t['file'] : '') . ':' . (isset($t['line']) ? $t['line'] : '');
                }
            } catch (\Throwable $e) {
                $trace = ['trace_error' => $e->getMessage()];
            }

            Log::error('CheckRoleAccess abort trace', [
                'user_id' => $user->id ?? null,
                'route' => $routeName,
                'target' => $target,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
                'trace' => $trace,
            ]);

            abort(403);
        }

        return $next($request);
    }
}

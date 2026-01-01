<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow overriding Blade compiled views path via env `VIEW_COMPILED_PATH`.
        // If set, we'll create the directory (if needed) and instruct the framework
        // to use that path so compiled views aren't written under project storage.
        try {
            $envPath = env('VIEW_COMPILED_PATH', null);
            $compiledPath = $envPath ?: (sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'shop85_views');

            if ($compiledPath) {
                if (! is_dir($compiledPath)) {
                    @mkdir($compiledPath, 0755, true);
                }
                // set config so View/Blade uses this compiled path
                config(['view.compiled' => $compiledPath]);
            }
        } catch (\Throwable $e) {
            // ignore any failure here to avoid breaking boot on platforms without permissions
        }
    }
}

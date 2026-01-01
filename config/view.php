<?php

use Illuminate\Support\Arr;

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views.
    |
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. You may change this to any directory
    | that is writable by your web server. We default to a temp folder
    | to avoid filling the project `storage` folder when desired.
    |
    */
    'compiled' => env('VIEW_COMPILED_PATH') ?: (sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'shop85_views'),
];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Where Blade looks for your view templates.
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
    | Where compiled Blade templates are stored. This must be a plain path,
    | NOT wrapped in realpath() — on a fresh install the storage/framework
    | directory does not exist yet, and realpath() returns an empty value
    | which breaks the Blade compiler ("Please provide a valid cache path").
    |
    */

    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),

];
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Main domain to cloud or local disk
    |--------------------------------------------------------------------------
    */

    'domain' => env('ASSETS_DEPLOYER_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Assets engine mix/elixir
    |--------------------------------------------------------------------------
    */

    'assets_engine' => 'mix',

    /*
    |--------------------------------------------------------------------------
    | Mix manifest directories
    |--------------------------------------------------------------------------
    |
    | If you using mix, please specify all manifest directories that being used
    | on your project, parameters that specified as a second argument of
    | `mix($path, $manifestDirectory)` function, default array if you don't use
    | the parameter
    |
    */

    'mix_manifest_directories' => [''],

    /*
    |--------------------------------------------------------------------------
    | Elixir build directories
    |--------------------------------------------------------------------------
    | If you using elixir, please specify all build directories that being used
    | on your project, parameters that specified as a second argument of
    | `elixir($file, $buildDirectory)` function, default array if you don't use
    | the parameter
    */

    'elixir_build_directories' => ['build'],

    /*
    |--------------------------------------------------------------------------
    | Cloud disk to deploy
    |--------------------------------------------------------------------------
    |
    | If the disk will have local driver or will be set to false,
    | the `assets_deployer_get()` will work as standard `mix()` or `elixir()`
    |
    */

    'disk' => env('ASSETS_DEPLOYER_DISK', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Root cloud assets directory
    |--------------------------------------------------------------------------
    */

    'cloud_assets_directory' => 'deployed_assets',

    /*
    |--------------------------------------------------------------------------
    | Additional assets to deploy: fonts, images
    |--------------------------------------------------------------------------
    */

    'additional_assets' => [],

    /*
    |--------------------------------------------------------------------------
    | Cache settings
    |--------------------------------------------------------------------------
    |
    | Generate unique query string in order to glue it to assets paths to avoid
    | caching of not existing files on cloud
    |
    */

    'attach_unique_query_string' => true,
];

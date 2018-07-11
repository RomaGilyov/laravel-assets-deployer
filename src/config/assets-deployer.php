<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development mode
    |--------------------------------------------------------------------------
    |
    | During development mode the `assets_deployer_get()` will work
    | as standard `mix()` or `elixir()`
    |
    */

    'development_mode' => env('ASSETS_DEPLOYER_DEV', true),

    /*
    |--------------------------------------------------------------------------
    | Path to manifest.json
    |--------------------------------------------------------------------------
    */

    'manifest_json_path' => public_path(),

    /*
    |--------------------------------------------------------------------------
    | Cloud disk to deploy
    |--------------------------------------------------------------------------
    |
    | If the disk will have local driver, the `assets_deployer_get()` will work
    | as standard `mix()` or `elixir()`
    |
    */

    'disk' => 's3',
];

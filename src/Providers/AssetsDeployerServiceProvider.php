<?php

namespace RGilyov\AssetsDeployer\Providers;

use Illuminate\Support\ServiceProvider;
use RGilyov\AssetsDeployer\Commands\DeployAssetsCommand;

/**
 * Class AssetsDeployerServiceProvider
 * @package RGilyov\AssetsDeployer\Providers
 */
class AssetsDeployerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/assets-deployer.php' => config_path('assets-deployer.php')
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/assets-deployer.php', 'assets-deployer');

        if (function_exists('assets_deployer_get')) {
            require_once __DIR__."../helpers.php";
        }

        if (method_exists($this, 'commands')) {
            $this->commands([DeployAssetsCommand::class]);
        }
    }
}
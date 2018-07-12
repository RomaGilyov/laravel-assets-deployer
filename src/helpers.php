<?php

if (! function_exists('assets_deployer_get')) {
    /**
     * @param $path
     * @param null $directory
     * @throws Exception
     */
    function assets_deployer_get($path, $directory = null)
    {
        if (! is_null($directory)) {
            $directory = \RGilyov\AssetsDeployer\AssetsDeployer::isMixEngine() ? '' : 'build';
        }

        return \RGilyov\AssetsDeployer\AssetsDeployer::get($path, $directory);
    }
}
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
            $directory = \Hippomundo\AssetsDeployer\AssetsDeployer::isMixEngine() ? '' : 'build';
        }

        return \Hippomundo\AssetsDeployer\AssetsDeployer::get($path, $directory);
    }
}

if (! function_exists('assets_deployer_src')) {
    /**
     * @param $path
     * @return string
     */
    function assets_deployer_src($path)
    {
        return \Hippomundo\AssetsDeployer\AssetsDeployer::srcLink($path);
    }
}

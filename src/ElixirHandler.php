<?php

namespace Hippomundo\AssetsDeployer;

use Hippomundo\AssetsDeployer\Exceptions\AssetsDeployerException;

/**
 * Class ElixirHandler
 * @package Hippomundo\AssetsDeployer
 */
class ElixirHandler extends BaseAssetsHandler
{
    /**
     * @param $directory
     * @return array|mixed
     */
    public function getManifest($directory)
    {
        static $manifest = [];
        static $manifestPath;

        if (empty($manifest) || $manifestPath !== $directory) {
            $path = public_path($directory.'/rev-manifest.json');

            if (file_exists($path)) {
                $manifest = json_decode(file_get_contents($path), true);
                $manifestPath = $directory;
            }
        }

        return $manifest;
    }

    /**
     * @param $path
     * @param string $directory
     * @return mixed|string
     * @throws AssetsDeployerException
     */
    public function getDefault($path, $directory = 'build')
    {
        if (function_exists('elixir')) {
            return elixir($path, $directory);
        }

        throw new AssetsDeployerException("Elixir function does not exists");
    }
}

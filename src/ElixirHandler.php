<?php

namespace RGilyov\AssetsDeployer;

use RGilyov\AssetsDeployer\Exceptions\AssetsDeployerException;

/**
 * Class ElixirHandler
 * @package RGilyov\AssetsDeployer
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

    /**
     * @param $path
     * @param $directory
     * @return mixed|string
     */
    public function getFromCloud($path, $directory)
    {
        return $this->disk->url($this->gluePaths($directory, $path));
    }
}

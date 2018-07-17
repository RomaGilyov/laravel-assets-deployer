<?php

namespace RGilyov\AssetsDeployer\Interfaces;

/**
 * Interface AssetsHandlerInterface
 * @package RGilyov\AssetsDeployer\Interfaces
 */
interface AssetsHandlerInterface
{
    /**
     * @param $directory
     * @return array
     */
    public function getManifest($directory);

    /**
     * @param $path
     * @param $directory
     * @return mixed
     */
    public function getFromCloud($path, $directory);

    /**
     * @param $path
     * @param $directory
     * @return mixed
     */
    public function getDefault($path, $directory);

    /**
     * @param $path
     * @param $directory
     * @return mixed
     */
    public function get($path, $directory);

    /**
     * @param bool $uploadAdditionalAssets
     * @return mixed
     */
    public function upload($uploadAdditionalAssets = false);
}

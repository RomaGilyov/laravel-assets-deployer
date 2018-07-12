<?php

namespace RGilyov\AssetsDeployer\Interfaces;

/**
 * Interface AssetsHandlerInterface
 * @package RGilyov\AssetsDeployer\Interfaces
 */
interface AssetsHandlerInterface
{
    /**
     * @return array
     */
    public function getManifest();

    /**
     * @return mixed
     */
    public function getFromCloud();

    /**
     * @return mixed
     */
    public function getDefault();

    /**
     * @return mixed
     */
    public function get();

    /**
     * @return bool
     */
    public function upload();
}

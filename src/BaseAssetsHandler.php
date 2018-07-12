<?php

namespace RGilyov\AssetsDeployer;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use RGilyov\AssetsDeployer\Interfaces\AssetsHandlerInterface;

/**
 * Class BaseAssetsHandler
 * @package RGilyov\AssetsDeployer
 */
abstract class BaseAssetsHandler implements AssetsHandlerInterface
{
    /**
     * @var array
     */
    protected $directories;

    /**
     * @var FilesystemAdapter
     */
    protected $disk;

    /**
     * @var mixed
     */
    protected $config;

    /**
     * BaseAssetsHandler constructor.
     */
    public function __construct()
    {
        $this->config = config('assets-deployer');

        $this->disk = $this->resolveDisk();

        $this->directories = ( array )$this->resolveDirectories();
    }

    /**
     * @return array
     */
    protected function resolveDirectories()
    {
        $engine = $this->config['assets_engine'];

        if (strcasecmp($engine, 'elixir') === 0) {
            return $this->config['mix_build_directories'];
        }

        return $this->config['mix_manifest_directories'];
    }

    /**
     * @return null
     */
    protected function resolveDisk()
    {
        $disk = $this->config['disk'];

        return $disk ? Storage::disk($disk) : null;
    }

    /**
     * @return bool
     */
    protected function isCloud()
    {
        if ($this->disk instanceof FilesystemAdapter) {
            return ! $this->disk->getDriver() instanceof Filesystem;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        if ($this->isCloud()) {
            return $this->getFromCloud();
        }

        return $this->getDefault();
    }

    /**
     * @return bool
     */
    public function upload()
    {
        if (! $this->isCloud()) {
            return true;
        }


    }
}

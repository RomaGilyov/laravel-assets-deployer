<?php

namespace RGilyov\AssetsDeployer;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Adapter\Local;
use RGilyov\AssetsDeployer\Interfaces\AssetsHandlerInterface;

/**
 * Class BaseAssetsHandler
 * @package RGilyov\AssetsDeployer
 */
abstract class BaseAssetsHandler implements AssetsHandlerInterface
{
    /**
     * Cloud directory with asses
     *
     * @var string
     */
    protected $basePath;

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

        $this->basePath = $this->config['cloud_assets_directory'];

        $this->disk = $this->resolveDisk();

        $this->directories = ( array )$this->resolveDirectories();
    }

    /**
     * @return array
     */
    protected function resolveDirectories()
    {
        if (AssetsDeployer::isMixEngine()) {
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
            return ! $this->disk->getDriver()->getAdapter() instanceof Local;
        }

        return false;
    }

    /**
     * @param $part1
     * @param $part2
     * @return string
     */
    protected function gluePaths($part1, $part2)
    {
        return rtrim($part1) . '/' . ltrim($part2);
    }

    /**
     * @param $path
     * @param $directory
     * @return mixed
     */
    public function get($path, $directory)
    {
        if ($this->isCloud()) {
            return $this->getFromCloud($path, $directory);
        }

        return $this->getDefault($path, $directory);
    }

    /**
     * @return bool
     */
    public function upload()
    {
        if (! $this->isCloud()) {
            return true;
        }

        foreach ($this->directories as $directory) {
            $manifest = $this->getManifest($directory);

            foreach ($manifest as $file) {
                $filePath = $this->gluePaths($directory, $file);

                $path = $this->gluePaths($this->basePath, $filePath);

                $contents = Storage::disk('public')->get($filePath);

                $this->disk->put($path, $contents);
            }
        }

        return true;
    }
}

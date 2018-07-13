<?php

namespace RGilyov\AssetsDeployer;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\Local;
use RGilyov\AssetsDeployer\Exceptions\AssetsDeployerException;
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
    protected $cloudDirectory;

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

        $this->cloudDirectory = $this->makeCloudDirectory();

        $this->disk = $this->resolveDisk();

        $this->directories = ( array )$this->resolveDirectories();
    }

    /**
     * @return string
     */
    protected function makeCloudDirectory()
    {
        return $this->gluePaths(
            $this->config['cloud_assets_directory'],
            Str::slug(env('APP_URL'), '_')
        );
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
            return !$this->disk->getDriver()->getAdapter() instanceof Local;
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
        return rtrim($part1, '/') . '/' . ltrim($part2, '/');
    }

    /**
     * @param $path
     * @param $directory
     * @return mixed
     * @throws AssetsDeployerException
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
        if (!$this->isCloud()) {
            return true;
        }

        if ($this->disk->exists($this->cloudDirectory)) {
            $this->disk->deleteDirectory($this->cloudDirectory);
        }

        foreach ($this->directories as $directory) {
            $manifest = $this->getManifest($directory);

            foreach ($manifest as $file) {
                $filePath = $this->gluePaths($directory, $file);

                $path = $this->gluePaths($this->cloudDirectory, $filePath);

                $contents = file_get_contents(public_path($filePath));

                $this->disk->put($path, $contents);
            }
        }

        return true;
    }

    /**
     * @param $fullFilePath
     * @return string
     */
    protected function makeCloudUrl($fullFilePath)
    {
        $diskConfig = config('filesystems.disks')[$this->config['disk']];

        if (isset($diskConfig['url'])) {
            return $this->gluePaths($diskConfig['url'], $fullFilePath);
        }

        if ($this->disk && method_exists($this->disk, 'url')) {
            return $this->disk->url($fullFilePath);
        }

        return $fullFilePath;
    }

    /**
     * @param $path
     * @param $directory
     * @return mixed
     * @throws AssetsDeployerException
     */
    public function getFromCloud($path, $directory)
    {
        $manifest = $this->getManifest($directory);

        if (!isset($manifest[$path])) {
            throw new AssetsDeployerException("File {$path} does not exists in the manifest.json");
        }

        $filePath = $this->gluePaths($directory, $manifest[$path]);

        $fullPath = $this->gluePaths($this->cloudDirectory, $filePath);

        return $this->makeCloudUrl($fullPath);
    }
}
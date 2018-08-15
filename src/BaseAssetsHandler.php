<?php

namespace Hippomundo\AssetsDeployer;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\Local;
use Hippomundo\AssetsDeployer\Exceptions\AssetsDeployerException;
use Hippomundo\AssetsDeployer\Interfaces\AssetsHandlerInterface;

/**
 * Class BaseAssetsHandler
 * @package Hippomundo\AssetsDeployer
 */
abstract class BaseAssetsHandler implements AssetsHandlerInterface
{
    /**
     * @var string
     */
    const MANIFEST = 'assets-deployer-manifest.json';

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
     * @var null|array
     */
    protected $loadedManifest = null;

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
     * @return array
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @return string
     */
    public function getCloudDirectory()
    {
        return $this->cloudDirectory;
    }

    /**
     * @return FilesystemAdapter|null
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * @param FilesystemAdapter $disk
     * @return $this
     */
    public function setDisk(FilesystemAdapter $disk)
    {
        $this->disk = $disk;

        return $this;
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
            return $this->config['mix_manifest_directories'];
        }

        return $this->config['elixir_build_directories'];
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
     * @param bool $uploadAdditionalAssets
     * @return bool|mixed
     */
    public function upload($uploadAdditionalAssets = false)
    {
        if (!$this->isCloud()) {
            return true;
        }

        foreach ($this->directories as $directory) {
            $manifest = $this->getManifest($directory);

            foreach ($manifest as $file) {
                if (strpos($file, '?') !== false) {
                    $file = substr($file, 0, strpos($file, '?'));
                }

                $filePath = $this->gluePaths($directory, $file);

                $path = $this->gluePaths($this->cloudDirectory, $filePath);

                if ($this->disk->exists($path)) {
                    $this->disk->delete($path);
                }

                $contents = $this->getContents($filePath);

                $this->disk->put($path, $contents);
            }

            $this->makeAssetsDeployerManifest($directory);
        }

        if ($uploadAdditionalAssets) {
            $this->uploadAdditionalAssets();

            $this->generateAssetsDeployerUnique();
        }

        return true;
    }

    /**
     * @param $directory
     */
    protected function makeAssetsDeployerManifest($directory)
    {
        $manifest = $this->getManifest($directory);

        $assetsDeployerManifest = $this->getAssetsDeployerManifest(false);

        $assetsDeployerManifest['directories'][$directory] = $manifest;

        $this->putManifest($assetsDeployerManifest);
    }

    /**
     * @param bool $cache
     * @return array
     */
    public function getAssetsDeployerManifest($cache = true)
    {
        $manifest = [];

        if ($cache && $this->loadedManifest) {
            return $this->loadedManifest;
        }

        $path = public_path(static::MANIFEST);

        if (is_file($path)) {
            $manifest = json_decode(file_get_contents($path), true) ?: [];
        }

        return $this->loadedManifest = $manifest;
    }

    /**
     * @param $directory
     * @return array|mixed
     */
    public function getAssetsDeployerManifestDir($directory)
    {
        $manifest = $this->getAssetsDeployerManifest();

        $manifests = isset($manifest['directories']) ? $manifest['directories'] : [];

        return isset($manifests[$directory]) ? $manifests[$directory] : [];
    }

    /**
     * @return void
     */
    protected function generateAssetsDeployerUnique()
    {
        $manifest = $this->getAssetsDeployerManifest();

        $manifest['unique'] = Str::slug(Str::random(16), '');

        $this->putManifest($manifest);
    }

    /**
     * @param $contents
     */
    protected function putManifest($contents)
    {
        if (! is_string($contents)) {
            $contents = json_encode($contents);
        }

        file_put_contents(public_path(static::MANIFEST), $contents, LOCK_EX);
    }

    /**
     * @return mixed|string
     */
    public function getAssetsDeployerManifestUnique()
    {
        $manifest = $this->getAssetsDeployerManifest();

        return isset($manifest['unique']) ? $manifest['unique'] : '';
    }

    /**
     * @param $url
     * @return string
     */
    protected function glueUniqueIdToUrl($url)
    {
        if (! $this->config['attach_unique_query_string']) {
            return $url;
        }

        $unique = $this->getAssetsDeployerManifestUnique();

        if (! $unique) {
            return $url;
        }

        return (strpos($url, '?') === false) ? "{$url}?id={$unique}" : "{$url}&id={$unique}";
    }

    /**
     * @return void
     */
    protected function uploadAdditionalAssets()
    {
        $additionalAssets = $this->config['additional_assets'];

        if (is_array($additionalAssets)) {
            foreach ($additionalAssets as $path) {
                $this->uploadAdditionalRecursively($path);
            }
        }
    }

    /**
     * @param $path
     */
    protected function uploadAdditionalRecursively($path)
    {
        foreach ($this->files($path) as $file) {
            if (is_dir($file)) {
                $this->uploadAdditionalRecursively($file);
            }

            $contents = $this->getContents($file);

            $file = $this->gluePaths($this->cloudDirectory, $this->truncateAbsolutePath($file));

            if (! $this->disk->exists($file)) {
                $this->disk->put($file, $contents);
            }
        }
    }

    /**
     * @param $path
     * @return bool|string
     */
    protected function getContents($path)
    {
        $publicPath = public_path();

        if (strpos($path, $publicPath) === false) {
            $path = $this->gluePaths($publicPath, $path);
        }

        return file_get_contents($path);
    }

    /**
     * @param $path
     * @return mixed
     */
    protected function truncateAbsolutePath($path)
    {
        return str_replace(public_path(), '', $path);
    }

    /**
     * @param $path
     * @return array
     */
    protected function files($path)
    {
        $publicPath = public_path();

        if (strpos($path, $publicPath) === false) {
            $path = $this->gluePaths($publicPath, $path);
        }

        if (is_dir($path)) {
            return glob("$path/*");
        }

        if (is_file($path)) {
            return [$path];
        }

        return [];
    }

    /**
     * @param $fullFilePath
     * @return string
     */
    protected function makeCloudUrl($fullFilePath)
    {
        if (isset($this->config['domain'])) {
            return $this->gluePaths($this->config['domain'], $fullFilePath);
        }

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
        $manifest = $this->getAssetsDeployerManifestDir($directory);

        if (! isset($manifest[$path])) {
            throw new AssetsDeployerException("File {$path} does not exists in the " . static::MANIFEST);
        }

        $filePath = $this->gluePaths($directory, $manifest[$path]);

        return $this->srcLink($filePath, false);
    }

    /**
     * @param $path
     * @param bool $glueUnique
     * @return string
     */
    public function srcLink($path, $glueUnique = true)
    {
        $fullPath = $this->isCloud() ? $this->gluePaths($this->cloudDirectory, $path) : $path;

        $fullPath = $this->makeCloudUrl($fullPath);

        return $glueUnique ? $this->glueUniqueIdToUrl($fullPath) : $fullPath;
    }
}

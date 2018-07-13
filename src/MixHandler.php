<?php

namespace RGilyov\AssetsDeployer;

use Illuminate\Support\HtmlString;
use RGilyov\AssetsDeployer\Exceptions\AssetsDeployerException;

/**
 * Class MixHandler
 * @package RGilyov\AssetsDeployer
 */
class MixHandler extends BaseAssetsHandler
{
    /**
     * @param $directory
     * @return array|mixed
     * @throws AssetsDeployerException
     */
    public function getManifest($directory)
    {
        static $manifests = [];

        if ($directory && ! starts_with($directory, '/')) {
            $directory = "/{$directory}";
        }

        $manifestPath = public_path($directory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new AssetsDeployerException('The Mix manifest does not exist.');
            }

            return $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        return $manifests[$manifestPath];
    }

    /**
     * @param $path
     * @param string $directory
     * @return \Illuminate\Support\HtmlString|mixed
     * @throws AssetsDeployerException
     * @throws \Exception
     */
    public function getDefault($path, $directory = '')
    {
        if (function_exists('mix')) {
            return mix($path, $directory);
        }

        throw new AssetsDeployerException("Mix function does not exists");
    }

    /**
     * @param $path
     * @param $directory
     * @return HtmlString|mixed
     * @throws AssetsDeployerException
     */
    public function getFromCloud($path, $directory)
    {
        return new HtmlString(parent::getFromCloud($path, $directory));
    }
}

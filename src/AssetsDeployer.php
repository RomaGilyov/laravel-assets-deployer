<?php

namespace RGilyov\AssetsDeployer;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

/**
 * Class AssetsDeployer
 * @package RGilyov\AssetsDeployer
 */
class AssetsDeployer
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var FilesystemAdapter
     */
    protected $disk;

    /**
     * @var bool
     */
    protected $isCloud;

    /**
     * @var array
     */
    protected $manifest;

    /**
     * AssetsDeployer constructor.
     */
    public function __construct()
    {

    }
}

<?php

namespace RGilyov\AssetsDeployer;
use RGilyov\AssetsDeployer\Exceptions\AssetsDeployerException;

/**
 * Class AssetsDeployer
 *
 * @method static upload($deployAdditionalAssets = false)
 * @method static getDefault($path, $directory)
 * @method static getFromCloud($path, $directory)
 * @method static get($path, $directory)
 * @method static srcLink($path)
 *
 * @package RGilyov\AssetsDeployer
 */
class AssetsDeployer
{
    /**
     * @var BaseAssetsHandler
     */
    protected static $deployer;

    /**
     * @return void
     */
    protected static function init()
    {
        if (static::$deployer) {
            return;
        }

        static::$deployer = static::isMixEngine() ? new MixHandler() : new ElixirHandler();
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws AssetsDeployerException
     */
    public static function __callStatic($name, $arguments)
    {
        static::init();

        if (method_exists(static::$deployer, $name)) {
            return call_user_func_array([static::$deployer, $name], $arguments);
        }

        $className = get_class(static::$deployer);

        throw new AssetsDeployerException("Method {$name} does not exists in {$className}");
    }

    /**
     * @return bool
     */
    public static function isMixEngine()
    {
        $engine = config('assets-deployer')['assets_engine'];

        return strcasecmp($engine, 'mix') === 0;
    }
}

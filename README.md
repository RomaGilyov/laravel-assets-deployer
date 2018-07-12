# laravel-assets-deployer
Deploy your compiled javascript and css files into cloud.

## Installation ##

```php
composer require rgilyov/laravel-file-manager
```

Register \RGilyov\AssetsDeployer\Provides\AssetsDeployerServiceProvider inside `config/app.php`
```php
    'providers' => [
        ...
        \RGilyov\AssetsDeployer\Providers\AssetsDeployerServiceProvider::class,
    ],
```

After installation you may publish default configuration file
```
php artisan vendor:publish --tag=config
```

## Basic usage ##

Just change all `mix()` or `elixir()` functions with:

```
    {{ assets_deployer_get($path, $directory) }}
    
    // Directory set to default mix or elixir parameters if not set
```

After you compiled you styles and scripts just run the command:

```
    php artisan assets-deployer:deploy
```

If local disk driver being used `assets_deployer_get()` function will work as standard `mix()` or `elixir()` function
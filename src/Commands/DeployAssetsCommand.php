<?php

namespace RGilyov\AssetsDeployer\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RGilyov\AssetsDeployer\AssetsDeployer;

/**
 * Class DeployAssetsCommand
 * @package RGilyov\AssetsDeployer\Commands
 */
class DeployAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets-deployer:deploy {--all-assets} {--disk=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy compiled js and css';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $disk = $this->option('disk');

        if ($disk) {
            AssetsDeployer::setDisk(Storage::disk($disk));
        }

        AssetsDeployer::upload($this->option('deploy-additional-assets'));
    }
}

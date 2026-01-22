<?php

namespace DiegoDrese\StorageMigrator;

use Illuminate\Support\ServiceProvider;
use DiegoDrese\StorageMigrator\Commands\MigrateStorageCommand;

class StorageMigratorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/storage-migrator.php',
            'storage-migrator'
        );
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../config/storage-migrator.php' =>
                    config_path('storage-migrator.php'),
            ], 'storage-migrator-config');

            $this->commands([
                MigrateStorageCommand::class,
            ]);
        }
    }
}

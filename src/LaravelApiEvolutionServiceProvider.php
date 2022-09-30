<?php

namespace Ejunker\LaravelApiEvolution;

use Ejunker\LaravelApiEvolution\Commands\LaravelApiEvolutionCommand;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelApiEvolutionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-api-evolution')
            ->hasConfigFile()
            ->hasMigration('create_laravel-api-evolution_table')
            ->hasCommand(LaravelApiEvolutionCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('ejunker/laravel-api-evolution');
            });
    }
}

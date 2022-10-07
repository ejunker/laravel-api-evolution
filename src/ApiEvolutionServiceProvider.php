<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Ejunker\LaravelApiEvolution\Commands\ApiMigrationMakeCommand;
use Ejunker\LaravelApiEvolution\Version\InvalidStrategyIdException;
use Ejunker\LaravelApiEvolution\Version\Strategy;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApiEvolutionServiceProvider extends PackageServiceProvider
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
            ->hasCommand(ApiMigrationMakeCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('ejunker/laravel-api-evolution');
            });

        $this->setupVersionResolver();
    }

    private function setupVersionResolver(): void
    {
        $this->app->singleton(
            VersionResolver::class,
            static function (Container $container): VersionResolver {
                $strategiesConfig = $container->get('config')->get('api-evolution.strategies');

                $strategies = array_map(
                    static function (array $strategyConfig) use ($container) {
                        try {
                            $strategy = $container->make(
                                $strategyConfig['id'],
                                $strategyConfig['config'] ?? []
                            );
                        } catch (BindingResolutionException $e) {
                            throw new InvalidStrategyIdException($strategyConfig['id']);
                        }

                        if (! $strategy instanceof Strategy) {
                            throw new InvalidStrategyIdException($strategyConfig['id']);
                        }

                        return $strategy;
                    },
                    $strategiesConfig
                );

                return new VersionResolver($strategies);
            }
        );

        $this->app->singleton(VersionCollection::class, function (Container $container) {
            $versions = collect(config('api-evolution.versions', []))->sortKeys();

            return new VersionCollection($versions);
        });

        $this->app->singleton(ApiEvolution::class, function (Container $container) {
            $versions = $container->get(VersionCollection::class);

            return new ApiEvolution($versions);
        });
    }
}

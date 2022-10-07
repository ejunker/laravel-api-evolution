<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VersionCollection extends Collection
{
    private Collection $versionsToRun;

    private Collection $migrationsToRun;

    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->versionsToRun = collect();
        $this->migrationsToRun = collect();
    }

    public function getLatestVersion(): string
    {
        return $this->keys()->sort()->last();
    }

    public function getVersionsToRun(string $requestVersion): Collection
    {
        if (! $this->hasValidVersion($requestVersion)) {
            return collect();
        }

        if (! isset($this->versionsToRun[$requestVersion])) {
            $this->versionsToRun[$requestVersion] = $this
                ->filter(function ($versionMigrations, $version) use ($requestVersion) {
                    return $requestVersion < $version;
                });
        }

        return $this->versionsToRun[$requestVersion];
    }

    public function getMigrationsToRun(string $version, Request $request): Collection
    {
        if (! isset($this->migrationsToRun[$version])) {
            $this->migrationsToRun[$version] = $this->getVersionsToRun($version)
                ->transform(function ($versionMigrations) use ($request) {
                    return $this->migrationsForVersion($versionMigrations, $request);
                });
        }

        return $this->migrationsToRun[$version];
    }

    private function hasValidVersion(string $version): bool
    {
        return $version && $this->keys()->contains($version);
    }

    /**
     * Get the applicable migrations for a version based on the request
     */
    private function migrationsForVersion(array $migrationClasses, Request $request): Collection
    {
        return collect($migrationClasses)
            ->filter(function ($migrationClass) use ($request) {
                // filter out Bind objects
                if (! is_subclass_of($migrationClass, ApiMigration::class)) {
                    return false;
                }

                return (new $migrationClass())->isApplicable($request);
            });
    }
}

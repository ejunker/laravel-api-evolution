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
                ->filter(fn ($versionMigrations, $version) => $requestVersion < $version
                );
        }

        return $this->versionsToRun[$requestVersion];
    }

    public function getMigrationsToRun(string $version, Request $request): Collection
    {
        if (! isset($this->migrationsToRun[$version])) {
            $this->migrationsToRun[$version] = $this->getVersionsToRun($version)
                ->transform(fn (array $versionMigrations) => $this->migrationsForVersion($versionMigrations, $request)
                )
                // filter out versions with no migrations
                ->reject(fn (Collection $migrations) => $migrations->isEmpty());
        }

        return $this->migrationsToRun[$version];
    }

    private function hasValidVersion(string $version): bool
    {
        return $version && $this->keys()->contains($version);
    }

    /**
     * Get the applicable migrations for a version based on the request
     *
     * @return Collection<ApiMigration|Bind|object>
     */
    private function migrationsForVersion(array $migrations, Request $request): Collection
    {
        return collect($migrations)
            // filter out Bind objects
            ->filter(fn (string|ApiMigration|Bind $migration) => is_subclass_of($migration, ApiMigration::class))
            // create instances
            ->map(fn (string|ApiMigration $migration) => $migration instanceof ApiMigration ? $migration : new $migration())
            // filter applicable
            ->filter(fn (ApiMigration $migration) => $migration->isApplicable($request));
    }
}

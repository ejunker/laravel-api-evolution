<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class Migrator
{
    protected Request $request;

    protected Response $response;

    private readonly Collection $versions;

    /**
     * @var string desired version to migrate to
     */
    private readonly string $version;

    private Collection $migrationsToRun;

    public function setRequest(Request $request): Migrator
    {
        $this->request = $request;

        return $this;
    }

    public function setVersions(Collection $versions): Migrator
    {
        $this->versions = $versions;

        return $this;
    }

    public function setVersion(string $version): Migrator
    {
        $this->version = $version;

        return $this;
    }

    public function processRequestMigrations(): Request
    {
        $this->getMigrationsToRun()
            ->flatten()
            ->each(function ($migration) {
                $this->request = (new $migration)->migrateRequest($this->request);
            });

        return $this->request;
    }

    public function processResponseMigrations(Response $response): Migrator
    {
        $this->response = $response;

        $this->getMigrationsToRun()
            ->reverse()
            ->flatten()
            ->each(function ($migration) {
                $this->response = (new $migration())->migrateResponse($this->response);
            });

        return $this;
    }

    public function getResponse(): Response
    {
        $latestVersion = $this->getLatestVersion();

        // version could be empty string if a version was not requested
        $version = $this->version ?: $latestVersion;
        $this->response->headers->set('Api-Version', $version);

        if ($version !== $latestVersion) {
            $this->response->headers->set('Api-Version-Latest', $latestVersion);
            $this->response->headers->set('Deprecation', 'true');
        }

        return $this->response;
    }

    private function getLatestVersion(): string
    {
        return $this->versions->keys()->sort()->last();
    }

    private function getMigrationsToRun(): Collection
    {
        if (!$this->hasValidVersion()) {
            return collect();
        }

        if (! isset($this->migrationsToRun)) {
            $this->migrationsToRun = $this->versions
                ->filter(function ($versionMigrations, $version) {
                    return $this->version < $version;
                })
                ->transform(function ($versionMigrations) {
                    return $this->migrationsForVersion($versionMigrations);
                });
        }

        return $this->migrationsToRun;
    }

    private function hasValidVersion(): bool
    {
        return $this->version && $this->versions->keys()->contains($this->version);
    }

    /**
     * Get the applicable migrations for a version based on the request
     */
    private function migrationsForVersion(array $migrationClasses): Collection
    {
        return collect($migrationClasses)
            ->filter(function ($migrationClass) {
                /* @var ApiMigration $migration */
                $migration = new $migrationClass();

                return $migration->isApplicable($this->request);
            });
    }
}

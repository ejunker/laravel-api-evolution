<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiEvolution
{
    private Request $request;

    private Response $response;

    /**
     * @var string desired version to migrate to
     */
    private string $version;

    public function __construct(
        private readonly VersionCollection $versions,
    ) {}

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function validateVersion(\Closure $callback): self
    {
        if ($this->version && ! $this->versions->keys()->contains($this->version)) {
            $callback($this->version);
        }

        return $this;
    }

    /**
     * @param  class-string  $migrationClass
     */
    public function isActive(string $migrationClass): bool
    {
        return $this->versions
            ->getMigrationsToRun($this->version, $this->request)
            ->flatten()
            ->contains(
                fn (ApiMigration $migration) => is_a($migration, $migrationClass)
            );
    }

    public function processBinds(): self
    {
        $this->versions
            ->getVersionsToRun($this->version)
            ->flatten()
            ->reverse()
            ->filter(fn ($bind) => $bind instanceof Bind)
            ->each(function ($bind) {
                $bind->handle();
            });

        return $this;
    }

    public function processRequestMigrations(): Request
    {
        return $this->versions
            ->getMigrationsToRun($this->version, $this->request)
            ->flatten()
            ->reduce(
                fn (Request $carryRequest, ApiMigration $migration) => $migration->migrateRequest($carryRequest),
                $this->request
            );
    }

    public function processResponseMigrations(Response $response): ApiEvolution
    {
        $this->response = $this->versions
            ->getMigrationsToRun($this->version, $this->request)
            ->reverse()
            ->flatten()
            ->reduce(
                fn (Response $carryResponse, ApiMigration $migration) => $migration->migrateResponse($carryResponse),
                $response
            );

        return $this;
    }

    public function getResponse(): Response
    {
        $latestVersion = $this->versions->getLatestVersion();

        // version could be empty string if a version was not requested
        $version = $this->version ?: $latestVersion;
        $this->response->headers->set('API-Version', $version);

        if ($version !== $latestVersion) {
            $this->response->headers->set('API-Version-Latest', $latestVersion);
        }

        // is deprecated if there are migrations or binds for this endpoint and version
        $hasMigrations = $this->versions
            ->getMigrationsToRun($this->version, $this->request)
            ->flatten()
            ->isNotEmpty();
        if ($hasMigrations) {
            $this->response->headers->set('Deprecation', 'true');
        }

        return $this->response;
    }
}

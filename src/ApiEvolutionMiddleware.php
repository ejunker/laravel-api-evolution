<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Closure;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiEvolutionMiddleware
{
    /**
     * @var Collection<string, array<class-string<ApiMigration>>>
     */
    protected readonly Collection $versions;

    public function __construct(
        private readonly VersionResolver $versionResolver,
    ) {
        $this->versions = $this->getVersions();
    }

    public function handle(Request $request, Closure $next): Response
    {
        $version = $this->getVersion($request);

        $this->validateVersion($version);

        $migrator = (new Migrator())
            ->setRequest($request)
            ->setVersions($this->versions)
            ->setVersion($version);

        return $migrator
            ->processResponseMigrations(
                $next($migrator->processRequestMigrations())
            )
            ->getResponse();
    }

    protected function getVersion(Request $request): string
    {
        return $this->versionResolver->resolve($request)
            //?? $request->header('Api-Version')
            //?? $request->query('api_version')
            ?? $this->getDefaultVersion();
    }

    protected function getDefaultVersion(): string
    {
        return '';
    }

    /**
     * @return Collection<string, array<class-string<ApiMigration>>>
     */
    protected function getVersions(): Collection
    {
        return collect(config('api-evolution.versions', []))->sortKeys();
    }

    /**
     * @param string $version
     */
    protected function validateVersion(string $version): void
    {
        if ($version && !$this->versions->keys()->contains($version)) {
            $this->invalidVersion($version);
        }
    }

    protected function invalidVersion(string $version): void
    {
        throw new HttpException(400, 'The api version requested is invalid');
    }
}

<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Closure;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiEvolutionMiddleware
{
    public function __construct(
        private readonly VersionResolver $versionResolver,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $version = $this->getVersion($request);

        $apiEvolution = app(ApiEvolution::class)
            ->setRequest($request)
            ->setVersion($version)
            ->validateVersion($this->invalidVersion(...));

        return $apiEvolution
            ->processBinds()
            ->processResponseMigrations(
                $next($apiEvolution->processRequestMigrations())
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

    protected function invalidVersion(string $version): void
    {
        throw new HttpException(400, 'The api version requested is invalid');
    }
}

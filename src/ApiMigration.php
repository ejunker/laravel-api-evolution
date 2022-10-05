<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiMigration
{
    public static string $description;

    /**
     * The route names that this migration should be applied to.
     */
    public function forRouteNames(): array
    {
        return [];
    }

    /**
     * Determine if the migration should be applied.
     */
    public function isApplicable(Request $request): bool
    {
        return $request->routeIs($this->forRouteNames());
    }

    /**
     * Migrate up the request for the application to "read".
     */
    public function migrateRequest(Request $request): Request
    {
        return $request;
    }

    /**
     * Migrate down the response to display to the client.
     */
    public function migrateResponse(Response $response): Response
    {
        return $response;
    }

    /**
     * @throws \JsonException
     */
    protected function mapJsonResponse(Response $response, callable $callback): Response
    {
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $data = $callback($data);

        return $response->setContent(json_encode($data, JSON_THROW_ON_ERROR));
    }
}

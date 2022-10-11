<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Tests\Http\Migrations;

use Ejunker\LaravelApiEvolution\ApiMigration;
use Symfony\Component\HttpFoundation\Response;

class RenameTitleToPosition extends ApiMigration
{
    protected array $routeNames = [
        'users.show',
    ];

    public string $description = 'Rename title to position';

    public function migrateResponse(Response $response): Response
    {
        return $this->mapJsonResponse($response, function (array $data) {
            $data['position'] = $data['title'];
            unset($data['title']);

            return $data;
        });
    }
}

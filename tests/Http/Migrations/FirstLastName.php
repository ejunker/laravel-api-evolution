<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Tests\Http\Migrations;

use Ejunker\LaravelApiEvolution\ApiMigration;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class FirstLastName extends ApiMigration
{
    protected array $routeNames = [
        'users.show',
        'posts.*',
    ];

    public string $description = 'Move up name.firstname and name.lastname';

    public function migrateResponse(Response $response): Response
    {
        return $this->mapJsonResponse($response, function (array $data) {
            $data['firstname'] = Arr::get($data, 'name.firstname');
            $data['lastname'] = Arr::get($data, 'name.lastname');
            unset($data['name']);

            return $data;
        });
    }
}

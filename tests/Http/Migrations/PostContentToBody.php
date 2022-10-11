<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Tests\Http\Migrations;

use Ejunker\LaravelApiEvolution\ApiMigration;
use Illuminate\Http\Request;

class PostContentToBody extends ApiMigration
{
    protected array $routeNames = [
        'posts.*',
    ];

    public string $description = 'Rename content to body in request';

    /**
     * Migrate up the request for the application to "read".
     */
    public function migrateRequest(Request $request): Request
    {
        $request['body'] = $request['content'];
        unset($request['content']);

        return $request;
    }
}

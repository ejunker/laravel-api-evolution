<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Tests\Http\Migrations;

use Ejunker\LaravelApiEvolution\ApiMigration;
use Illuminate\Http\Request;

class PostHeadlineToTitle extends ApiMigration
{
    protected array $routeNames = [
        'posts.*',
    ];

    public string $description = 'Rename headline to title in request';

    public function migrateRequest(Request $request): Request
    {
        $request['title'] = $request['headline'];
        unset($request['headline']);

        return $request;
    }
}

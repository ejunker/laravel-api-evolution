<?php

use Ejunker\LaravelApiEvolution\ApiEvolutionMiddleware;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\FirstLastName;
use Ejunker\LaravelApiEvolution\Version\HeaderStrategy;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Ejunker\LaravelApiEvolution\VersionCollection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('can handle requests and responses', function () {
    $this->instance(VersionCollection::class, new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['users.show']),
        ],
        '2022-09-09' => [],
    ]));

    $middleware = new ApiEvolutionMiddleware(new VersionResolver([
        new HeaderStrategy('API-Version'),
    ]));

    $request = getRequest('/users', 'GET', 'users.show');
    $request->headers->set('Api-Version', '2022-09-09');
    $response = $middleware->handle($request, function (Request $request) {
        return new Response(json_encode([
            'name' => [
                'firstname' => 'Borat',
                'lastname' => 'Sagdiyev',
            ],
        ], JSON_THROW_ON_ERROR));
    });

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getContent())
        ->toBe(json_encode([
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ], JSON_THROW_ON_ERROR))
        ->and($response->headers->all())
        ->toMatchArray([
            'api-version' => ['2022-09-09'],
            'api-version-latest' => ['2022-10-10'],
            'deprecation' => ['true'],
        ]);
});

it('throws exception for invalid version', function () {
    $middleware = new ApiEvolutionMiddleware(new VersionResolver([
        new HeaderStrategy('API-Version'),
    ]));

    $request = getRequest('/users', 'GET', 'users.show');
    $request->headers->set('Api-Version', 'invalid');
    $middleware->handle($request, function (Request $request) {
        return new Response();
    });
})->throws(HttpException::class);

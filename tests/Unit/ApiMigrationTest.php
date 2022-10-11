<?php

use Ejunker\LaravelApiEvolution\ApiMigration;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\FirstLastName;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\PostHeadlineToTitle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

it('can create from config:cache state', function () {
    $migration = FirstLastName::__set_state([
        'routeNames' => ['users.show'],
        'description' => 'this is the description',
    ]);

    expect($migration->getRouteNames())->toBe(['users.show']);
});

it('can get route names', function () {
    $migration = new FirstLastName();

    expect($migration->getRouteNames())->toBe(['users.show', 'posts.*']);
});

it('can check if migration is applicable for the request', function () {
    $migration = new FirstLastName();

    expect($migration->isApplicable(getRequest('/users', 'GET', 'users.show')))->toBeTrue();
    expect($migration->isApplicable(getRequest('/home', 'GET', 'home.index')))->toBeFalse();
});

it('can migrate the request', function () {
    $migration = new PostHeadlineToTitle(['posts.store']);
    $request = getRequest('/posts', 'POST', 'posts.store')->replace([
        'headline' => 'here is some text',
    ]);

    expect($newRequest = $migration->migrateRequest($request))
        ->toBeInstanceOf(Request::class)
        ->and($newRequest->input())
        ->toBe([
            'title' => 'here is some text',
        ]);
});

it('can migrate the response', function () {
    $migration = new FirstLastName(['users.show']);
    $response = new Response(json_encode([
        'name' => [
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ],
    ], JSON_THROW_ON_ERROR));

    expect($newResponse = $migration->migrateResponse($response))
        ->toBeInstanceOf(Response::class)
        ->and($newResponse->getContent())
        ->toBe(json_encode([
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ], JSON_THROW_ON_ERROR));
});

it('does not modify request or response by default', function () {
    $migration = new class extends ApiMigration
    {
    };

    $request = getRequest('/posts', 'POST', 'posts.store')->replace([
        'headline' => 'here is some text',
    ]);
    expect($migration->migrateRequest($request)->input())
        ->toBe([
            'headline' => 'here is some text',
        ]);

    $response = new Response(json_encode([
        'firstname' => 'Borat',
        'lastname' => 'Sagdiyev',
    ], JSON_THROW_ON_ERROR));
    expect($migration->migrateResponse($response)->getContent())
        ->toBe(json_encode([
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ], JSON_THROW_ON_ERROR));
});

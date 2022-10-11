<?php

use Ejunker\LaravelApiEvolution\ApiEvolution;
use Ejunker\LaravelApiEvolution\Bind;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\FirstLastName;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\PostContentToBody;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\PostHeadlineToTitle;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\RenameTitleToPosition;
use Ejunker\LaravelApiEvolution\Tests\Http\Requests\TestRequest;
use Ejunker\LaravelApiEvolution\VersionCollection;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

it('runs callback when version validation fails', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [],
    ])))->setVersion('2022-09-09');

    $apiEvolution->validateVersion(function (string $version) {
        throw new InvalidArgumentException('Invalid Version: '.$version);
    });
})->throws(InvalidArgumentException::class);

it('does not run callback when version validation passes', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [],
    ])))->setVersion('2022-10-10');

    $newApiEvolution = $apiEvolution->validateVersion(function (string $version) {
        throw new InvalidArgumentException('Invalid Version: '.$version);
    });

    expect($newApiEvolution)->toBe($apiEvolution);
});

it('can check if a migration is active', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['users.show']),
            new PostHeadlineToTitle(['posts.show']),
        ],
        '2022-09-09' => [],
    ])))
        ->setRequest(getRequest('/users', 'GET', 'users.show'))
        ->setVersion('2022-09-09');

    expect($apiEvolution->isActive(FirstLastName::class))->toBeTrue();
    expect($apiEvolution->isActive(PostHeadlineToTitle::class))->toBeFalse();
});

it('can process Binds', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [
            new Bind(FormRequest::class, TestRequest::class),
        ],
        '2022-09-09' => [],
    ])))
        ->setRequest(getRequest('/users', 'GET', 'users.show'))
        ->setVersion('2022-09-09')
        ->processBinds();

    expect(resolve(FormRequest::class))->toBeInstanceOf(TestRequest::class);
});

it('can process request migrations', function () {
    $request = getRequest('/posts', 'POST', 'posts.store')->replace([
        'headline' => 'here is some text',
        'content' => 'this is the content',
    ]);
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [
            new PostHeadlineToTitle(['posts.store']),
            new PostContentToBody(['posts.*']),
        ],
        '2022-09-09' => [],
    ])))
        ->setRequest($request)
        ->setVersion('2022-09-09');

    expect($apiEvolution->processRequestMigrations()->input())
        ->toBe([
            'title' => 'here is some text',
            'body' => 'this is the content',
        ]);
});

it('can process response migrations', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['users.show']),
            new RenameTitleToPosition(['users.show']),
        ],
        '2022-09-09' => [],
    ])))
        ->setRequest(getRequest('/users', 'GET', 'users.show'))
        ->setVersion('2022-09-09');

    $response = new Response(json_encode([
        'name' => [
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ],
        'title' => 'TV Reporter',
    ], JSON_THROW_ON_ERROR));

    expect($apiEvolution->processResponseMigrations($response)->getResponse()->getContent())
        ->toBe(json_encode([
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
            'position' => 'TV Reporter',
        ], JSON_THROW_ON_ERROR));
});

it('sets headers on the response', function () {
    $apiEvolution = (new ApiEvolution(new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['users.show']),
        ],
        '2022-09-09' => [],
    ])))
        ->setRequest(getRequest('/users', 'GET', 'users.show'))
        ->setVersion('2022-09-09');

    $response = new Response(json_encode([
        'name' => [
            'firstname' => 'Borat',
            'lastname' => 'Sagdiyev',
        ],
        'title' => 'TV Reporter',
    ], JSON_THROW_ON_ERROR));

    expect($apiEvolution->processResponseMigrations($response)->getResponse()->headers->all())
        ->toMatchArray([
            'api-version' => ['2022-09-09'],
            'api-version-latest' => ['2022-10-10'],
            'deprecation' => ['true'],
        ]);
});

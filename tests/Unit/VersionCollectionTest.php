<?php

use Ejunker\LaravelApiEvolution\ApiMigration;
use Ejunker\LaravelApiEvolution\Bind;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\FirstLastName;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\PostContentToBody;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\PostHeadlineToTitle;
use Ejunker\LaravelApiEvolution\Tests\Http\Migrations\RenameTitleToPosition;
use Ejunker\LaravelApiEvolution\VersionCollection;
use Illuminate\Support\Facades\Route;

it('can get latest version', function () {
    $versions = new VersionCollection([
        '2022-10-10' => [],
        '2022-09-09' => [],
    ]);

    expect($versions->getLatestVersion())->toBe('2022-10-10');
});

it('returns empty collection if not a valid version', function () {
    $versions = new VersionCollection([]);

    expect($versions->getVersionsToRun('invalid-version'))->toBeCollection()->toBeEmpty();
});

it('returns empty collection if request latest version', function () {
    $versionCollection = new VersionCollection([
        '2022-10-10' => [],
        '2022-09-09' => [],
    ]);

    $versions = $versionCollection->getVersionsToRun('2022-10-10');
    expect($versions)->toBeCollection()->toBeEmpty();
});

it('can get versions to run', function () {
    $versionCollection = new VersionCollection([
        '2022-10-10' => [],
        '2022-10-03' => [],
        '2022-09-09' => [],
        '2022-08-08' => [],
    ]);

    $versions = $versionCollection->getVersionsToRun('2022-09-09');
    expect($versions->keys()->toArray())
        ->toEqual(['2022-10-10', '2022-10-03']);

    $versions = $versionCollection->getVersionsToRun('2022-10-10');
    expect($versions->keys())->toBeEmpty();
});

it('can get migrations to run', function () {
    Route::resource('users', 'UsersController');

    $versionCollection = new VersionCollection([
        '2022-10-10' => [
            FirstLastName::class,
            // handles routes defined in config
            new RenameTitleToPosition(['users.show']),
            // filters out Binds
            new Bind('Abstract', 'Concrete'),
        ],
        '2022-10-03' => [
            // handles migrations from multiple versions
            new PostContentToBody(['posts.show', 'users.show']),
        ],
        '2022-09-09' => [
            PostHeadlineToTitle::class,
        ],
        '2022-08-08' => [],
    ]);

    $request = getRequest('/users', 'GET', 'users.show');
    $versions = $versionCollection->getMigrationsToRun('2022-09-09', $request);
    expect($versions)
        ->toBeCollection()
        ->not->toBeEmpty()
        ->and($versions->flatten())
        ->map(fn (ApiMigration $migration) => $migration::class)
        ->toArray()
        ->toBe([
            FirstLastName::class,
            RenameTitleToPosition::class,
            PostContentToBody::class,
        ]);

    $versions = $versionCollection->getVersionsToRun('2022-10-10');
    expect($versions)->toBeCollection()->toBeEmpty();
});

it('can define routes in config', function () {
    Route::resource('users', 'UserController');

    $versionCollection = new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['posts.show']),
            new RenameTitleToPosition(['users.show', 'posts.*']),
        ],
        '2022-09-09' => [],
    ]);

    $request = getRequest('/users', 'GET', 'users.show');
    $versions = $versionCollection->getMigrationsToRun('2022-09-09', $request);
    expect($versions->flatten())
        ->map(fn (ApiMigration $migration) => $migration::class)
        ->toArray()
        ->toBe([
            RenameTitleToPosition::class,
        ]);
});

it('can match routes with wildcards', function () {
    Route::resource('users', 'UserController');

    $versionCollection = new VersionCollection([
        '2022-10-10' => [
            new FirstLastName(['users.*']),
        ],
        '2022-09-09' => [],
    ]);

    $request = getRequest('/users', 'GET', 'users.show');
    $versions = $versionCollection->getMigrationsToRun('2022-09-09', $request);
    expect($versions->flatten())
        ->map(fn (ApiMigration $migration) => $migration::class)
        ->toArray()
        ->toBe([
            FirstLastName::class,
        ]);
});

<?php

use Ejunker\LaravelApiEvolution\ApiEvolution;
use Ejunker\LaravelApiEvolution\Version\InvalidStrategyIdException;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Ejunker\LaravelApiEvolution\VersionCollection;

it('can resolve VersionResolver from container', function () {
    expect(resolve(VersionResolver::class))->toBeInstanceOf(VersionResolver::class);
});

it('throws InvalidStrategyIdException if cannot resolve Strategy', function () {
    app('config')->set('api-evolution.strategies', [
        [
            'id' => 'InvalidStrategy',
            'config' => [],
        ],
    ]);

    resolve(VersionResolver::class);
})->throws(InvalidStrategyIdException::class);

it('throws InvalidStrategyIdException if class is not instance of Strategy', function () {
    app('config')->set('api-evolution.strategies', [
        [
            // resolvable but not instance of Strategy
            'id' => 'config',
            'config' => [],
        ],
    ]);

    resolve(VersionResolver::class);
})->throws(InvalidStrategyIdException::class);

it('can resolve VersionCollection from container', function () {
    expect(resolve(VersionCollection::class))->toBeInstanceOf(VersionCollection::class);
});

it('can resolve ApiEvolution from container', function () {
    expect(resolve(ApiEvolution::class))->toBeInstanceOf(ApiEvolution::class);
});

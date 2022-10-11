<?php

use Ejunker\LaravelApiEvolution\Exceptions\InvalidArgumentException;
use Ejunker\LaravelApiEvolution\Version\HeaderStrategy;
use Ejunker\LaravelApiEvolution\Version\QueryStringStrategy;
use Ejunker\LaravelApiEvolution\Version\VersionResolver;
use Illuminate\Http\Request;

it('throws exception if strategies are not instance of Strategy', function () {
    new VersionResolver(['foo']);
})->throws(InvalidArgumentException::class);

it('returns null if a version was not resolved', function () {
    $resolver = new VersionResolver([
        new HeaderStrategy('API-Version'),
    ]);

    expect($resolver->resolve(new Request()))->toBeNull();
});

it('can run multiple version strategies', function () {
    $request = new Request(['api_version' => '2022-10-10']);

    $resolver = new VersionResolver([
        new HeaderStrategy('API-Version'),
        new QueryStringStrategy('api_version'),
    ]);

    expect($resolver->resolve($request))->toBe('2022-10-10');
});

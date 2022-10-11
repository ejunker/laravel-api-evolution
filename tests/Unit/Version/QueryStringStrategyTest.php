<?php

use Ejunker\LaravelApiEvolution\Version\QueryStringStrategy;
use Ejunker\LaravelApiEvolution\Version\UnresolvedVersionException;
use Illuminate\Http\Request;

it('throws UnresolvedVersionException if key is not in query string', function () {
    (new QueryStringStrategy('api_version'))->resolve(new Request());
})->throws(UnresolvedVersionException::class);

it('returns correct version', function () {
    $request = new Request(['api_version' => '2022-10-10']);

    $version = (new QueryStringStrategy('api_version'))->resolve($request);

    expect($version)->toBe('2022-10-10');
});

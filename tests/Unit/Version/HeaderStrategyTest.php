<?php

use Ejunker\LaravelApiEvolution\Version\HeaderStrategy;
use Ejunker\LaravelApiEvolution\Version\UnresolvedVersionException;
use Illuminate\Http\Request;

it('throws UnresolvedVersionException if header is not present', function () {
    (new HeaderStrategy('Api-Version'))->resolve(new Request());
})->throws(UnresolvedVersionException::class);

it('returns correct version', function () {
    $request = new Request();
    $request->headers->set('Api-Version', '2022-10-10');

    $version = (new HeaderStrategy('Api-Version'))->resolve($request);

    expect($version)->toBe('2022-10-10');
});

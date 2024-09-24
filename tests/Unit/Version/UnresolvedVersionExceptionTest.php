<?php

use Ejunker\LaravelApiEvolution\Version\UnresolvedVersionException;
use Illuminate\Http\Request;

it('can return the request', function () {
    $request = new Request;
    $exception = new UnresolvedVersionException($request);

    expect($exception->request())->toBe($request);
});

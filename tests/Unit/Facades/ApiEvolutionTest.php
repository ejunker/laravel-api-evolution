<?php

use Ejunker\LaravelApiEvolution\Facades\ApiEvolution;

it('returns correct facade root', function () {
    expect(ApiEvolution::getFacadeRoot())->toBeInstanceOf(\Ejunker\LaravelApiEvolution\ApiEvolution::class);
});

<?php

namespace Ejunker\LaravelApiEvolution\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ejunker\LaravelApiEvolution\LaravelApiEvolution
 */
class LaravelApiEvolution extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Ejunker\LaravelApiEvolution\LaravelApiEvolution::class;
    }
}

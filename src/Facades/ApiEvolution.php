<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ejunker\LaravelApiEvolution\ApiEvolution
 */
class ApiEvolution extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Ejunker\LaravelApiEvolution\ApiEvolution::class;
    }
}

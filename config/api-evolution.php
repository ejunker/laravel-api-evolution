<?php

use Ejunker\LaravelApiEvolution\Version\HeaderStrategy;
use Ejunker\LaravelApiEvolution\Version\QueryStringStrategy;

return [

    /*
    |--------------------------------------------------------------------------
    | Versioning Strategies
    |--------------------------------------------------------------------------
    |
    | The versioning strategy(ies) to use and the configuration for each.
    |
    */

    'strategies' => [
        [
            'id' => HeaderStrategy::class,
            'config' => [
                'name' => 'API-Version',
            ],
        ],
        [
            'id' => QueryStringStrategy::class,
            'config' => [
                'name' => 'api_version',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Versions
    |--------------------------------------------------------------------------
    |
    | List of available versions and the migrations to run for them.
    |
    */

    'versions' => [
        'YYYY-MM-DD' => [
            // \App\Http\Migrations\ExampleChangeFieldName::class
        ],
    ],

];

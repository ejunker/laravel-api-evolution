<?php

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
            'id' => \Ejunker\LaravelApiEvolution\Version\HeaderStrategy::class,
            'config' => [
                'name' => 'API-Version',
            ],
        ],
        [
            'id' => \Ejunker\LaravelApiEvolution\Version\QueryStringStrategy::class,
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

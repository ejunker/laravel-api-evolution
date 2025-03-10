<?php

namespace Ejunker\LaravelApiEvolution\Tests;

use Ejunker\LaravelApiEvolution\ApiEvolutionServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Ejunker\\LaravelApiEvolution\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            ApiEvolutionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-api-evolution_table.php.stub';
        $migration->up();
        */
    }

    // public function defineRoutes($router)
    // {
    //    $router->get('/users')->name('users.index');
    // }
}

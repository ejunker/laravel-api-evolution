<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Commands;

use Illuminate\Console\GeneratorCommand;

class ApiMigrationMakeCommand extends GeneratorCommand
{
    protected $name = 'make:api-migration';

    protected $description = 'Create a new api migration';

    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../stubs/migration.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Http\Migrations';
    }
}

<?php

namespace Ejunker\LaravelApiEvolution\Commands;

use Illuminate\Console\Command;

class LaravelApiEvolutionCommand extends Command
{
    public $signature = 'laravel-api-evolution';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

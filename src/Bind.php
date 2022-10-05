<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution;

use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\JsonResource;

class Bind
{
    public function __construct(
        private readonly string $abstract,
        private readonly \Closure|string|null $concrete,
        public readonly string $description = ''
    ) {
    }

    public function handle(): void
    {
        if (is_subclass_of($this->concrete, JsonResource::class)) {
            app()->bind($this->abstract, function (Container $container) {
                return new $this->concrete(new \stdClass);
            });
        } else {
            app()->bind($this->abstract, $this->concrete);
        }
    }

    /**
     * Allow laravel to config:cache this class
     */
    public static function __set_state(array $array)
    {
        return new self(...array_values($array));
    }
}

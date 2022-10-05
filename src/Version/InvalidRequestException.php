<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Illuminate\Http\Request;

final class InvalidRequestException extends \InvalidArgumentException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function make($request): self
    {
        return new self(
            sprintf(
                'Instance of %s expected. Got %s.',
                Request::class,
                get_debug_type($request)
            )
        );
    }
}

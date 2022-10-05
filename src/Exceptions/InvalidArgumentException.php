<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct(array $allowedTypes, mixed $actual)
    {
        if (empty($allowedTypes)) {
            throw new \UnexpectedValueException('$allowedTypes parameter must specify at least one type.');
        }

        parent::__construct(
            sprintf(
                'Instance of %s expected. Got %s.',
                self::toString($allowedTypes),
                get_debug_type($actual)
            )
        );
    }

    private static function toString(array $allowedTypes): string
    {
        $lastAllowedType = array_pop($allowedTypes);

        return empty($allowedTypes)
            ? $lastAllowedType
            : implode(', ', $allowedTypes)." or $lastAllowedType";
    }
}

<?php

use Ejunker\LaravelApiEvolution\Exceptions\InvalidArgumentException;

it('throws UnexpectedValueException if allowedTypes is empty', function () {
    new InvalidArgumentException([], '');
})->throws(UnexpectedValueException::class);

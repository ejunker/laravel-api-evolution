<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Ejunker\LaravelApiEvolution\Exceptions\InvalidArgumentException;
use Illuminate\Http\Request;

final class VersionResolver
{
    /**
     * @var Strategy[]
     */
    private array $strategies;

    public function __construct(array $strategies)
    {
        self::ensureStrategyInstances($strategies);

        $this->strategies = $strategies;
    }

    /**
     * Resolve the target version for the given $request.
     */
    public function resolve(Request $request): ?string
    {
        foreach ($this->strategies as $strategy) {
            try {
                return $strategy->resolve($request);
            } catch (UnresolvedVersionException $exception) {
                // Ignore exception
            }
        }

        return null;
    }

    private static function ensureStrategyInstances(array $strategies): void
    {
        foreach ($strategies as $strategy) {
            if (! $strategy instanceof Strategy) {
                throw new InvalidArgumentException([Strategy::class], $strategy);
            }
        }
    }
}

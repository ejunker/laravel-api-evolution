<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Illuminate\Http\Request;

final class QueryStringStrategy implements Strategy
{
    private string $name;

    /**
     * @param  string  $name  Name of the query string parameter to get the target version from.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function resolve(Request $request): string
    {
        if ($version = $request->query($this->name)) {
            return $version;
        }

        throw new UnresolvedVersionException($request);
    }
}

<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Illuminate\Http\Request;

final class HeaderStrategy implements Strategy
{
    private string $name;

    /**
     * @param  string  $name  Header name to get the target version from.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function resolve(Request $request): string
    {
        if ($version = $request->header($this->name)) {
            return $version;
        }

        throw new UnresolvedVersionException($request);
    }
}

<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Illuminate\Http\Request;

final class UnresolvedVersionException extends \RuntimeException
{
    private Request $request;

    public function __construct(Request $request)
    {
        parent::__construct('Unable to resolve a version for the given request.');

        $this->request = $request;
    }

    public function request(): Request
    {
        return $this->request;
    }
}

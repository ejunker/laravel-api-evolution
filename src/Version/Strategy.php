<?php

declare(strict_types=1);

namespace Ejunker\LaravelApiEvolution\Version;

use Illuminate\Http\Request;

interface Strategy
{
    public function resolve(Request $request): string;
}

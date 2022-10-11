<?php

namespace Ejunker\LaravelApiEvolution\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}

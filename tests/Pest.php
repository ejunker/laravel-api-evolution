<?php

use Ejunker\LaravelApiEvolution\Tests\TestCase;
use Illuminate\Http\Request;

uses(TestCase::class)->in(__DIR__);
//uses(FeatureTestCase::class)->in('Feature');

function getRequest(string $uri, string $method, string $name): Request
{
    $request = Request::create($uri);
    $request->setRouteResolver(
        fn () => (new \Illuminate\Routing\Route([$method], $uri, ['as' => $name]))->bind($request)
    );

    return $request;
}

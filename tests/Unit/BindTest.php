<?php

use Ejunker\LaravelApiEvolution\Bind;
use Ejunker\LaravelApiEvolution\Tests\Http\Requests\TestRequest;
use Ejunker\LaravelApiEvolution\Tests\Http\Resources\TestResource;
use Illuminate\Foundation\Http\FormRequest;

it('can bind classes', function () {
    (new Bind(FormRequest::class, TestRequest::class))->handle();

    expect(resolve(FormRequest::class))->toBeInstanceOf(TestRequest::class);
});

it('can bind json resources', function () {
    (new Bind('MyResource', TestResource::class))->handle();

    expect(resolve('MyResource'))->toBeInstanceOf(TestResource::class);
});

it('can create Bind from config:cache state', function () {
    Bind::__set_state([
        'abstract' => FormRequest::class,
        'concrete' => TestRequest::class,
        'description' => 'this is the description',
    ])->handle();

    expect(resolve(FormRequest::class))->toBeInstanceOf(TestRequest::class);
});

<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Mockery\MockInterface;

test('remember forever', function () {
    $function = function () {
        return 'myValue';
    };

    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex'
    );

    expect($repo->rememberForever('myKey', $function))->toBe('myValue');
    expect($repo->keys())->toBe(['myKey']);
});

test('sear calls remember forever', function () {
    $callback = fn() => 'test3';

    $mock = Mockery::mock(IndexRepository::class)->makePartial();
    $mock->expects('rememberForever')
        ->with('string', $callback)
        ->andReturns('test3');

    expect($mock->sear('string', $callback))->toBe('test3');
});
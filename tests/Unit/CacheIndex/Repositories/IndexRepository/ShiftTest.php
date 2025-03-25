<?php

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('empty shift', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    expect($repo->shift())->toBeNull();
});

test('shift', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey1', 'myValue1');
    $repo->add('myKey2', 'myValue2');

    expect($repo->keys())->toBe(['myKey1', 'myKey2']);
    expect($repo->shift())->toBe('myValue1');
    expect($repo->keys())->toBe(['myKey2']);
});
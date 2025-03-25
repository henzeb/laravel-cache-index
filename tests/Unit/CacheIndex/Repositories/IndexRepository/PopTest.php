<?php

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('empty pop', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    expect($repo->pop())->toBeNull();
});

test('pop', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey1', 'myValue1');
    $repo->add('myKey2', 'myValue2');

    expect($repo->keys())->toBe(['myKey1', 'myKey2']);
    expect($repo->pop())->toBe('myValue2');
    expect($repo->keys())->toBe(['myKey1']);
});
<?php

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('empty random', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    expect($repo->random())->toBeNull();
});

test('random', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey1', 'myValue1');
    $repo->add('myKey2', 'myValue2');
    $repo->add('myKey3', 'myValue3');
    $repo->add('myKey4', 'myValue4');

    $item = $repo->random();

    expect($item)->toContain('myValue');
    expect($repo->count())->toBe(4);
});
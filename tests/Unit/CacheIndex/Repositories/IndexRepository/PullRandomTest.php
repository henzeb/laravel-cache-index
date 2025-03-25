<?php

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('empty pull random', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    expect($repo->pullRandom())->toBeNull();
});

test('pull random', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey1', 'myValue1');
    $repo->add('myKey2', 'myValue2');
    $repo->add('myKey3', 'myValue3');
    $repo->add('myKey4', 'myValue4');

    expect($repo->pullRandom())->toContain('myValue');
    expect($repo->count())->toBe(3);
});
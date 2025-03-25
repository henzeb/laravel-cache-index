<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('should return correct array of data', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex'
    );
    $repo->put('myKey1', 'myValue1');
    $repo->put('myKey2', 'myValue2');

    expect($repo->many([
        'myKey1',
        'myKey2'
    ]))->toBe([
        'myKey1' => 'myValue1',
        'myKey2' => 'myValue2'
    ]);

    expect($repo->getMultiple([
        'myKey1',
        'myKey2'
    ]))->toBe([
        'myKey1' => 'myValue1',
        'myKey2' => 'myValue2'
    ]);
});

test('should return correct array of data with defaults', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex'
    );
    $repo->put('myKey1', 'myValue1');
    $repo->put('myKey2', 'myValue2');

    expect($repo->getMultiple([
        'myKey1',
        'myKey2',
        'myKey3'
    ], 'aDefault'))->toBe([
        'myKey1' => 'myValue1',
        'myKey2' => 'myValue2',
        'myKey3' => 'aDefault',
    ]);
});
<?php

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('keys', function () {
    $arrayStore = new ArrayStore();
    $repo = new IndexRepository(
        $arrayStore,
        'myIndex',
        'array'
    );

    Carbon::setTestNow('2024-01-01 00:00:00');

    $repo->add('myKey', 'value');
    $repo->add('myExpiringKey', 'anotherValue', 10);

    expect($repo->keys())->toBe([
        'myKey',
        'myExpiringKey'
    ]);

    Carbon::setTestNow(
        now()->addSeconds(9)
    );

    expect($repo->keys())->toBe([
        'myKey',
        'myExpiringKey'
    ]);

    expect($arrayStore->get('indexed_myIndex_myExpiringKey'))->toBe('anotherValue');

    Carbon::setTestNow(
        now()->addSeconds(10)
    );

    expect($repo->keys())->toBe([
        'myKey',
    ]);

    expect($arrayStore->get('indexed_myIndex_myExpiringKey'))->toBeNull();
});
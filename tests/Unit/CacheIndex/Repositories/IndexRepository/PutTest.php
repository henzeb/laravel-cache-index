<?php

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('put', function () {
    $store = new ArrayStore();
    $repo = new IndexRepository(
        $store,
        'myIndex',
    );

    $repo->put('myKey', 'myValue');

    expect($repo->keys())->toBe([
        'myKey'
    ]);

    expect($store->get($repo->getPrefix() . 'myKey'))->toBe('myValue');
});

test('put expiration overwrite', function () {
    Carbon::setTestNow(now());

    $store = new ArrayStore();
    $repo = new IndexRepository(
        $store,
        'myIndex',
    );

    $repo->put('myKey', 'myValue', 10);
    $repo->put('myKey', 'myValue', 20);

    Carbon::setTestNow(now()->addSeconds(11));

    expect($repo->get('myKey'))->toBe('myValue');
});

test('set calls put', function () {
    $repo = $this->mock(IndexRepository::class)
        ->makePartial();

    $repo->expects('put')
        ->with('myKey', 'myValue', null)
        ->andReturn(true);

    $repo->expects('put')
        ->with('myKey', 'myValue', 10)
        ->andReturn(true);

    expect($repo->set('myKey', 'myValue'))->toBeTrue();
    expect($repo->set('myKey', 'myValue', 10))->toBeTrue();
});
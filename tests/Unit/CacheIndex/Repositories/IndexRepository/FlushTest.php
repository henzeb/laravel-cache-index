<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('should only flush its own keys', function () {
    $store = new ArrayStore();
    $store->put('myKey', 'value', 0);

    $repo = new IndexRepository($store, 'myIndex');

    $repo->put('myIndexedKey', 'myIndexedValue');

    expect($store->get($repo->getPrefix() . 'myIndexedKey'))->toBe('myIndexedValue');

    $repo->flush();

    expect($store->get('myKey'))->toBe('value');
    expect($store->get($repo->getPrefix() . 'myIndexedKey'))->toBeNull();
    expect($repo->keys())->toBe([]);
});
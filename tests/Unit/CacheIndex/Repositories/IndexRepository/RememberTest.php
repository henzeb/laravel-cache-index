<?php

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('remember', function () {
    $function = function () {
        return 'myValue';
    };
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store,
        'myIndex',
    );
    Carbon::setTestNow();
    
    expect($repo->remember('myKey', 10, $function))->toBe('myValue');
    expect($repo->keys())->toBe(['myKey']);
});

test('remember without ttl', function () {
    $function = function () {
        return 'myValue';
    };
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store,
        'myIndex'
    );
    
    expect($repo->remember('myKey', null, $function))->toBe('myValue');
    expect($repo->keys())->toBe(['myKey']);
});
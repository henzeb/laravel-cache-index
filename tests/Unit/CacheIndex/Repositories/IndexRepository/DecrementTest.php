<?php

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Illuminate\Contracts\Cache\Store;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('decrement', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->decrement('myKey');

    $this->assertStoreHas($repo, 'myKey', -1);
    expect($repo->keys())->toBe(['myKey']);

    $repo->decrement('myKey' , 2);

    $this->assertStoreHas($repo, 'myKey', -3);
    expect($repo->keys())->toBe(['myKey']);
});
<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('increment', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->increment('myKey');

    $this->assertStoreHas($repo, 'myKey', 1);
    expect($repo->keys())->toBe(['myKey']);

    $repo->increment('myKey', 2);

    $this->assertStoreHas($repo, 'myKey', 3);
    expect($repo->keys())->toBe(['myKey']);
});
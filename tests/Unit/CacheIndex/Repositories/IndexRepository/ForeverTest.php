<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('forever', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex'
    );

    $repo->forever('myKey', 'myValue');

    expect($repo->keys())->toBe(['myKey']);

    $this->assertStoreHas($repo, 'myKey', 'myValue');
    $this->assertTtl($repo, 'myKey');
});
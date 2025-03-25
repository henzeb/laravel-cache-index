<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('pull', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey', 'myValue');

    expect($repo->keys())->toBe(['myKey']);

    $repo->pull('myKey');

    expect($repo->keys())->toBe([]);
});
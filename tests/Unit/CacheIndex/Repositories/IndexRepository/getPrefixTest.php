<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('get prefix', function () {
    $repo = new IndexRepository(new ArrayStore(), 'myIndex');
    expect($repo->getPrefix())->toBe('indexed_myIndex_');
});
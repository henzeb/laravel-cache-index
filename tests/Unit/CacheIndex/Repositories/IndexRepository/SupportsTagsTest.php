<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('expects not to support tags', function () {
    expect(
        (new IndexRepository(
            new ArrayStore(), 'myIndex'
        ))->supportsTags()
    )->toBeFalse();
});
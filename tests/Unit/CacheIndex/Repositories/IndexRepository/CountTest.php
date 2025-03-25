<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('expect count', function () {
    $repo = new IndexRepository(
        new ArrayStore(), 'myIndex'
    );

//    expect($repo->count())->toBe(0);

    foreach (\array_fill(0, 10, 'myKey') as $key => $value) {
        $repo->add($value . $key, $value);
        expect($repo->count())->toBe($key + 1);
    }
});
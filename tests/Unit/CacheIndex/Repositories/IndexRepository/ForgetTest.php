<?php

use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('forget', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey', 'myValue');

    expect($repo->keys())->toBe(['myKey']);

    $repo->forget('myKey');

    expect($repo->keys())->toBe([]);

    $this->assertStoreHasNot($repo, 'myKey');
});

test('delete calls forget', function () {
    $repo = $this->mock(IndexRepository::class)
        ->makePartial();

    $repo->expects('forget')
        ->with('myKey')
        ->andReturn(true);

    expect($repo->delete('myKey', 'myValue'))->toBeTrue();
});
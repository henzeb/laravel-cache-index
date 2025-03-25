<?php

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('expect move', function () {
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->add('myKey', 'myValue');

    $repo->move('myKey', 'myIndex2');

    expect($this->getRawIndex($repo))->toBe([]);
    expect($this->getRawIndex($receivingRepo))->toBe([
        'myKey'
    ]);

    expect($repo->get('myKey'))->toBeNull();
    expect($receivingRepo->get('myKey'))->toBe('myValue');

    $this->assertStoreHasNot($repo, 'myKey');
    $this->assertStoreHas($receivingRepo, 'myKey', 'myValue');
});

test('expect move non existent', function () {
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->move('myKey', 'myIndex2');

    expect($this->getRawIndex($repo))->toBe([]);
    expect($this->getRawIndex($receivingRepo))->toBe([]);

    expect($repo->get('myKey'))->toBeNull();
    expect($receivingRepo->get('myKey'))->toBeNull();

    $this->assertStoreHasNot($repo, 'myKey');
    $this->assertStoreHasNot($receivingRepo, 'myKey');
});

test('expect moves with expiration', function () {
    Carbon::setTestNow('2024-01-01 00:00:00');

    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->add('myKey', 'myValue', 10);

    $repo->move('myKey', 'myIndex2', 10);

    expect($this->getRawIndex($repo))->toBe([]);
    expect($this->getRawIndex($receivingRepo))->toBe([
        'myKey',
    ]);

    $this->assertStoreHas($receivingRepo, 'myKey', 'myValue');
    $this->assertTtl($receivingRepo, 'myKey', 10);
});
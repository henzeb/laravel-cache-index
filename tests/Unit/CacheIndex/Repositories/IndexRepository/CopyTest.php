<?php

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Illuminate\Support\Facades\Cache;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('expect copy', function () {
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->add('myKey', 'myValue');

    $repo->copy('myKey', 'myIndex2');

    expect($this->getRawIndex($repo))->toBe([
        'myKey'
    ]);

    expect($this->getRawIndex($receivingRepo))->toBe([
        'myKey'
    ]);

    expect($repo->get('myKey'))->toBe('myValue');
    expect($receivingRepo->get('myKey'))->toBe('myValue');
});

test('expect copy non existent', function () {
    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->copy('myKey', 'myIndex2');

    expect($this->getRawIndex($repo))->toBe([]);
    expect($this->getRawIndex($receivingRepo))->toBe([]);
    expect($repo->get('myKey'))->toBeNull();
    expect($receivingRepo->get('myKey'))->toBeNull();
});

test('expect copy with expiration', function () {
    Carbon::setTestNow(Carbon::createFromTimestamp(0));

    $store = new ArrayStore();

    $repo = new IndexRepository(
        $store, 'myIndex'
    );

    $receivingRepo = new IndexRepository(
        $store, 'myIndex2'
    );

    $repo->add('myKey', 'myValue', 10);

    $repo->copy('myKey', 'myIndex2', 20);

    expect($this->getRawIndex($repo))->toBe([
        'myKey'
    ]);

    expect($this->getRawIndex($receivingRepo))->toBe([
        'myKey'
    ]);

    expect($repo->get('myKey'))->toBe('myValue');
    expect($receivingRepo->get('myKey'))->toBe('myValue');

    $this->assertTtl($repo, 'myKey', 10);
    $this->assertTtl($receivingRepo, 'myKey', 20);
});
<?php

use Carbon\Carbon;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

test('put', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->putMany(
        [
            'myKey' => 'myValue',
            'myKey2' => 'myValue2'
        ]
    );

    expect($repo->keys())->toBe([
        'myKey',
        'myKey2',
    ]);
});

test('put expiration', function () {
    Carbon::setTestNow();

    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->putMany(
        [
            'myKey' => 'myValue',
            'myKey2' => 'myValue2'
        ],
        now()->addSeconds(10)
    );

    $repo->putMany(
        [
            'myKey' => 'myValue',
            'myKey3' => 'myValue3'
        ],
        now()->addSeconds(20)
    );

    expect($this->getRawIndex($repo))->toBe([
        'myKey',
        'myKey2',
        'myKey3',
    ]);
});

test('set multiple calls put many', function () {
    $repo = $this->mock(IndexRepository::class)
        ->makePartial();

    $repo->expects('putMany')
        ->with(['myKey' => 'myValue'], null)
        ->andReturn(true);

    $repo->expects('putMany')
        ->with(['myKey' => 'myValue'], 10)
        ->andReturn(true);

    expect($repo->setMultiple(['myKey' => 'myValue']))->toBeTrue();
    expect($repo->setMultiple(['myKey' => 'myValue'], 10))->toBeTrue();
});
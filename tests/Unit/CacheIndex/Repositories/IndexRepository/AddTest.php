<?php

use Carbon\Carbon;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;

test('add', function () {
    $repo = new IndexRepository(
        new ArrayStore(),
        'myIndex',
    );

    $repo->add('myKey', 'myValue');

    expect($repo->keys())->toBe(['myKey']);
    $this->assertStoreHas($repo, 'myKey', 'myValue');
});

test('add again', function () {
    Carbon::setTestNow('2024-01-01 00:00:00');

    $mock = new ArrayStore();

    $repo = new IndexRepository(
        $mock,
        'myIndex',
        'array'
    );

    Carbon::setTestNow(now());
    $repo->add('myKey', 'myValue', now()->addSeconds(10));

    $this->assertTtl($repo, 'myKey', 10);

    $repo->add('myKey', 'myValue', now()->addSeconds(20));

    $this->assertTtl($repo, 'myKey', 10);

    expect($repo->keys())->toBe(['myKey']);
});

test('add with add method on store', function () {
    $store = new class extends ArrayStore {
        public function add(string $key, mixed $value, $ttl)
        {
            return true;
        }
    };

    $mock = $this->mock($store::class)->makePartial();
    $mock->expects('add')->andReturnTrue();

    $repo = new IndexRepository(
        $mock,
        'myIndex',
        'array'
    );
    $repo->add('myKey', 'myValue', 10);
});
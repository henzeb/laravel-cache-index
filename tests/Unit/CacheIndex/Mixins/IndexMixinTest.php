<?php

use Henzeb\CacheIndex\Providers\CacheIndexServiceProvider;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Cache;

uses()->group('mixin');

beforeEach(function () {
    $this->app->register(CacheIndexServiceProvider::class);
});

test('expects instance of index repository', function () {
    expect(Cache::index('myIndex'))->toBeInstanceOf(IndexRepository::class);
});

test('expects selected driver', function () {
    expect(Cache::index('myIndex')->getStore())->toBeInstanceOf(ArrayStore::class);
    expect(Cache::driver('file')->index('myIndex')->getStore())->toBeInstanceOf(FileStore::class);
});

test('expect index name used', function () {
    $repository = Cache::index('myIndex');

    $index = Closure::bind(
        function () {
            return $this->index;
        },
        $repository,
        IndexRepository::class
    )();

    expect($index)->toBe('myIndex');

    $repository = Cache::index('myOtherIndex');

    $index = Closure::bind(
        function () {
            return $this->index;
        },
        $repository,
        IndexRepository::class
    )();

    expect($index)->toBe('myOtherIndex');
});

test('expected driver for index', function () {
    expect(getIndexStore(Cache::index('myIndex')))->toBeInstanceOf(ArrayStore::class);
    expect(getIndexStore(Cache::driver('file')->index('myIndex')))->toBeInstanceOf(FileStore::class);
});

test('allow array as index', function () {
    $stringable = new class implements Stringable {
        public function __toString(): string
        {
            return 'stringable';
        }
    };

    $withName = new class {
        public string $name = 'withName';
    };

    $testObj = new class {};
    
    $repository = Cache::index(['test', $stringable, $withName, $testObj]);

    $index = Closure::bind(
        function () {
            return $this->index;
        },
        $repository,
        IndexRepository::class
    )();

    expect($index)->toBe('test.stringable.withName.' . $testObj::class);
});

function getIndexStore(IndexRepository $repository): Store
{
    return $repository->getStore();
}
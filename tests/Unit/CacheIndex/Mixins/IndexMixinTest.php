<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Mixins;

use Closure;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Store;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Henzeb\CacheIndex\Providers\CacheIndexServiceProvider;

class IndexMixinTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CacheIndexServiceProvider::class
        ];
    }

    public function testExpectsInstanceOfIndexRepository(): void
    {
        $this->assertEquals(IndexRepository::class, Cache::index('myIndex')::class);
    }

    public function testExpectsSelectedDriver(): void
    {
        $this->assertEquals(
            ArrayStore::class,
            Cache::index('myIndex')->getStore()::class
        );

        $this->assertEquals(
            FileStore::class,
            Cache::driver('file')->index('myIndex')->getStore()::class
        );
    }

    public function testExpectIndexNameUsed(): void
    {
        $repository = Cache::index('myIndex');

        $this->assertEquals(
            'myIndex',
            Closure::bind(
                function () {
                    return $this->index;
                },
                $repository,
                IndexRepository::class
            )()
        );

        $repository = Cache::index('myOtherIndex');

        $this->assertEquals(
            'myOtherIndex',
            Closure::bind(
                function () {
                    return $this->index;
                },
                $repository,
                IndexRepository::class
            )()
        );
    }

    public function testExpectedDriverForIndex(): void
    {
        $this->assertInstanceOf(
            ArrayStore::class,
            $this->getIndexStore(Cache::index('myIndex'))
        );

        $this->assertInstanceOf(
            FileStore::class,
            $this->getIndexStore(Cache::driver('file')->index('myIndex'))
        );
    }

    private function getIndexStore(IndexRepository $repository): Store
    {
        return $repository->getStore();
    }
}

<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Mixins;

use Closure;
use Henzeb\CacheIndex\Providers\CacheIndexServiceProvider;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;
use Stringable;

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

    public function testAllowArrayAsIndex(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return 'stringable';
            }
        };

        $withName = new class {
            public string $name = 'withName';
        };

        $repository = Cache::index(['test', $stringable, $withName, $this]);

        $this->assertEquals(
            'test.stringable.withName.' . $this::class,
            Closure::bind(
                function () {
                    return $this->index;
                },
                $repository,
                IndexRepository::class
            )()
        );
    }

    private function getIndexStore(IndexRepository $repository): Store
    {
        return $repository->getStore();
    }
}

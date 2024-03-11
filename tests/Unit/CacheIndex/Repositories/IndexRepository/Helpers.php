<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Closure;
use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use function now;

trait Helpers
{
    private function getRawIndex(IndexRepository $repository): array
    {
        return Closure::bind(
            function () {
                return $this->getIndex();
            },
            $repository,
            IndexRepository::class
        )();
    }

    public function assertStoreHas(IndexRepository $repo, string $key, mixed $value = null): void
    {
        $store = $repo->getStore();
        $key = $this->getItemKey($repo, $key);

        $this->assertTrue(
            $value ? $store->get($key) === $value : $store->get($key) !== null
        );
    }

    public function assertStoreHasNot(IndexRepository $repo, string $key): void
    {
        $store = $repo->getStore();
        $key = $this->getItemKey($repo, $key);

        $this->assertFalse(
            $store->get($key) !== null
        );
    }

    public function getItemKey(IndexRepository $repo, string $key): string
    {
        return Closure::bind(
            fn(string $key) => $this->itemKey($key),
            $repo,
            $repo::class
        )(
            $key
        );
    }

    public function assertTtl(IndexRepository $repo, string $key, int $ttl = null): void
    {
        $key = $this->getItemKey($repo, $key);
        /**
         * @var $store ArrayStore
         */
        $store = $repo->getStore();

        if ($ttl > 0) {
            $ttl = now()->addSeconds($ttl)->unix() . '.' . now()->addSeconds($ttl)->millisecond;
        }

        $this->assertEquals($ttl, $this->getRawStore($store)[$key]['expiresAt']);
    }


    private function getRawStore(ArrayStore $arrayStore): array
    {
        return Closure::bind(
            function () {
                return $this->storage;
            },
            $arrayStore,
            ArrayStore::class
        )();
    }

}

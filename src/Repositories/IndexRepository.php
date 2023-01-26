<?php

namespace Henzeb\CacheIndex\Repositories;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Henzeb\CacheIndex\Concerns\ManagesIndex;

use function array_keys;
use function method_exists;

class IndexRepository extends Repository
{
    use ManagesIndex;

    public const INDEX_PREFIX = 'index_';
    public const KEY_PREFIX = 'indexed_';

    public function __construct(
        Store $store,
        private string $index
    ) {
        parent::__construct($store);
    }

    public function many(array $keys): array
    {
        $results = $this->unprefixKeysAssoc(
            parent::many($this->prefixKeysAssoc($keys))
        );


        return collect($results)
            ->mapWithKeys(fn($value, $key) => [$key => $value])
            ->toArray();
    }

    public function pull($key, $default = null): mixed
    {
        return tap(
            parent::pull($key, $default),
            fn() => $this->deleteFromIndex($key)
        );
    }

    public function add($key, $value, $ttl = null): bool
    {
        if (method_exists($this->store, 'add')) {
            return $this->addToIndexOnSuccess(
                parent::add($key, $value, $ttl),
                $key
            );
        }

        return parent::add($key, $value, $ttl);
    }

    public function put($key, $value, $ttl = null): bool
    {
        if ($ttl) {
            return $this->addToIndexOnSuccess(
                parent::put($key, $value, $ttl),
                $key
            );
        }

        return parent::put($key, $value, $ttl);
    }

    public function putMany(array $values, $ttl = null): bool
    {
        return $this->addToIndexOnSuccess(
            parent::putMany($this->prefixKeysAssoc($values), $ttl),
            array_keys($values)
        );
    }

    public function increment($key, $value = 1): bool|int
    {
        return $this->addToIndexOnSuccess(
            parent::increment($this->itemKey($key), $value),
            $key
        );
    }

    public function decrement($key, $value = 1): bool|int
    {
        return $this->addToIndexOnSuccess(
            parent::decrement($this->itemKey($key), $value),
            $key
        );
    }

    public function forever($key, $value)
    {
        return $this->addToIndexOnSuccess(
            parent::forever($key, $value),
            $key,
        );
    }

    public function forget($key): bool
    {
        return tap(
            parent::forget($key),
            function ($forgotten) use ($key) {
                if ($forgotten) {
                    $this->deleteFromIndex($key);
                }
            }
        );
    }
}

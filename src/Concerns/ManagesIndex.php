<?php

namespace Henzeb\CacheIndex\Concerns;

use DateInterval;
use DateTimeInterface;
use Henzeb\CacheIndex\Repositories\IndexRepository;

use function array_map;
use function array_combine;
use function substr_replace;
use function str_starts_with;

trait ManagesIndex
{
    public function supportsTags(): bool
    {
        return false;
    }

    public function count(): int
    {
        return count($this->getIndex());
    }

    public function keys(): array
    {
        return $this->getIndex();
    }

    private function getIndex(string $targetIndex = null): array
    {
        /**
         * @var array $index
         */
        $index = $this->getStore()->get($this->indexName($targetIndex)) ?? [];

        foreach ($index as $key => $itemKey) {
            if (!$this->has($itemKey)) {
                unset($index[$key]);
            }
        }
        return array_values($index);
    }

    private function indexName(string $name = null): string
    {
        return IndexRepository::INDEX_PREFIX . ($name ?? $this->index);
    }

    public function addToIndexOnSuccess(
        mixed $success,
        string|array $keys
    ): bool {
        if ($success) {
            $this->addToIndex($keys);
        }
        return (bool)$success;
    }

    private function addToIndex(
        string|array $keys,
        string $targetIndex = null
    ): void {
        $index = $this->getIndex($targetIndex);

        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            $key = $this->unprefixedItemKey($key);
            if (!in_array($key, $index)) {
                $index[] = $this->unprefixedItemKey($key);
            }
        }

        $this->getStore()->forever(
            $this->indexName($targetIndex),
            $index
        );
    }

    public function move(
        string $key,
        string $targetIndex,
        DateTimeInterface|DateInterval|int|null $ttl = null
    ): bool {
        if ($this->missing($key)) {
            return false;
        }

        $this->copy($key, $targetIndex, $ttl);

        if ($this->indexName() !== $this->indexName($targetIndex)) {
            $this->forget($key);
        }

        return true;
    }

    public function copy(
        string $key,
        string $targetIndex,
        DateTimeInterface|DateInterval|int|null $ttl = null
    ): bool {
        if ($this->missing($key)) {
            return false;
        }
        $this->addToIndex($key, $targetIndex);

        if ($ttl) {
            return $this->getStore()->put(
                $this->keyPrefix($targetIndex, $key),
                $this->get($key),
                $this->getSeconds($ttl)
            );
        }

        return $this->getStore()->forever(
            $this->keyPrefix($targetIndex, $key),
            $this->get($key)
        );
    }

    public function syncTtl(
        DateTimeInterface|DateInterval|int|null $ttl = null
    ): bool {
        $keys = $this->keys();

        return $this->putMany($this->many($keys), $ttl);
    }

    public function flush(): bool
    {
        return $this->deleteMultiple($this->keys())
            && $this->getStore()->forget($this->indexName());
    }

    private function prefixKeys(array $keys): array
    {
        return array_map(fn($key) => $this->itemKey($key), $keys);
    }

    private function prefixKeysAssoc(array $keys): array
    {
        return array_combine(
            $this->prefixKeys(array_keys($keys)),
            array_values($keys)
        );
    }

    protected function itemKey($key): string
    {
        $prefix = $this->keyPrefix();

        if (str_starts_with($key, $prefix)) {
            return $key;
        }

        return $this->keyPrefix(key: $key);
    }

    private function keyPrefix(string $index = null, string $key = null): string
    {
        return IndexRepository::KEY_PREFIX . ($index ?? $this->index) . '_' . $key;
    }

    private function unprefixedItemKey(string $key): string
    {
        $prefix = $this->keyPrefix();
        if (str_starts_with($key, $prefix)) {
            return substr_replace($key, '', 0, strlen($prefix));
        }

        return $key;
    }

    private function deleteFromIndex(string|array $key): void
    {
        $index = $this->getIndex();

        foreach (is_array($key) ? $key : [$key] as $key) {
            unset($index[$key]);
        }

        $this->getStore()->forever(
            $this->indexName(),
            $index
        );
    }

    public function getPrefix(): string
    {
        return $this->keyPrefix();
    }
}

<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use PHPUnit\Framework\TestCase;
use Illuminate\Cache\ArrayStore;
use Henzeb\CacheIndex\Repositories\IndexRepository;

class FlushTest extends TestCase
{
    public function testShouldOnlyFlushItsOwnKeys()
    {
        $store = new ArraySTore();
        $store->put('myKey', 'value', 0);

        $repo = new IndexRepository($store, 'myIndex');

        $repo->put('myIndexedKey', 'myIndexedValue');

        $this->assertEquals('myIndexedValue', $store->get($repo->getPrefix() . 'myIndexedKey'));

        $repo->flush();

        $this->assertEquals('value', $store->get('myKey'));

        $this->assertNull($store->get($repo->getPrefix() . 'myIndexedKey'));

        $this->assertEquals([], $repo->keys());
    }
}

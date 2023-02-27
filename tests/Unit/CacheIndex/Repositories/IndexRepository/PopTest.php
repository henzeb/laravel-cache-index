<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class PopTest extends TestCase
{
    use Helpers;

    public function testEmptyPop(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $this->assertEquals(null, $repo->pop());
    }

    public function testPop(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey1', 'myValue1');
        $repo->add('myKey2', 'myValue2');

        $this->assertEquals(['myKey1', 'myKey2'], $repo->keys());

        $this->assertEquals('myValue2', $repo->pop());

        $this->assertEquals(
            ['myKey1'],
            $repo->keys()
        );
    }
}

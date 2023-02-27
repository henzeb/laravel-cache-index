<?php

namespace Henzeb\CacheIndex\Tests\Unit\CacheIndex\Repositories\IndexRepository;

use Henzeb\CacheIndex\Repositories\IndexRepository;
use Illuminate\Cache\ArrayStore;
use Orchestra\Testbench\TestCase;

class ShiftTest extends TestCase
{
    use Helpers;

    public function testEmptyShift(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $this->assertEquals(null, $repo->shift());
    }

    public function testShift(): void
    {
        $repo = new IndexRepository(
            new ArrayStore(),
            'myIndex',
        );

        $repo->add('myKey1', 'myValue1');
        $repo->add('myKey2', 'myValue2');

        $this->assertEquals(['myKey1', 'myKey2'], $repo->keys());

        $this->assertEquals('myValue1', $repo->shift());

        $this->assertEquals(
            ['myKey2'],
            $repo->keys()
        );
    }
}
